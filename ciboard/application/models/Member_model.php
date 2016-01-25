<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Member model class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

class Member_model extends CB_Model {

	/**
	*  테이블명
	*/
	public $_table = 'member';

	/**
	*  사용되는 테이블의 프라이머리키
	*/
	public $primary_key = 'mem_id';  // 사용되는 테이블의 프라이머리키

	public $search_sfield = '';

	public $cache_time = 86400 ; // 캐시 저장시간

	function __construct()
	{
		parent::__construct();
	}

	function get_by_memid($memid='', $select = '')
	{
			if ( ! $memid) return FALSE;
			if ( ! is_numeric($memid) OR $memid < 1) return FALSE;
			$where = array('mem_id' => $memid);
			return $this->get_one('' , $select , $where);
	}

	function get_by_userid($userid='', $select = '')
	{
			if ( ! $userid) return FALSE;
			$where = array('mem_userid' => $userid);
			return $this->get_one('' , $select , $where);
	}

	function get_by_email($email='', $select = '')
	{
			if ( ! $email) return FALSE;
			$where = array('mem_email' => $email);
			return $this->get_one('' , $select , $where);
	}

	function get_by_both($str='', $select = '')
	{
		if ( ! $str) return FALSE;
		if ($select) {
				$this->db->select($select);
		}
		$this->db->from($this->_table);
		$this->db->where('mem_userid', $str);
		$this->db->or_where('mem_email', $str);
		$result = $this->db->get();
		return $result->row_array();
	}

	public function get_super_member()
	{
		$cachename = 'super-admin-list';
		if ( ! $result = $this->cache->get($cachename)) {
			$result = $this->get_one('' , 'mem_id' , array('mem_is_admin' => '1', 'mem_denied'=>'0'));
			$this->cache->save($cachename, $result, $this->cache_time);
		}

		return $result;
	}

	public function is_super_admin($mem_id = '') {
		if( ! $mem_id) return FALSE;
		$super_members = $this->get_super_member();
		if( $super_members && in_array($mem_id, $super_members)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function delete($primary_value='', $where= '')
	{
		$result = parent::delete($primary_value, $where);
		$this->cache->delete('super-admin-list');
		return $result;
	}

	public function get_admin_list($limit = '', $offset = '', $where = '', $like = '', $findex = '', $forder = '', $sfield = '', $skeyword = '', $sop='OR')
	{
		$result = $this->_get_list_common($select='', $join='', $limit, $offset, $where, $like, $findex, $forder, $sfield, $skeyword, $sop);
		return $result;
	}

	function get_register_count($type = 'd', $start_date='' , $end_date='')
	{

		if ( ! $start_date) return FALSE;
		if ( ! $end_date) return FALSE;

		if ($type == 'y') $left = 4;
		else if ($type == 'm') $left = 7;
		else $left = 10;

		$this->db->select('count(*) as cnt, left(mem_register_datetime, ' . $left . ') as day ', FALSE);
		$this->db->where('left(mem_register_datetime, 10) >=' , $start_date);
		$this->db->where('left(mem_register_datetime, 10) <=' , $end_date);
		$this->db->where('mem_denied' , 0);
		$this->db->group_by('day');
		$this->db->order_by('mem_register_datetime', 'desc');
		$qry = $this->db->get($this->_table);
		$result = $qry->result_array();

		return $result;

	}

}
