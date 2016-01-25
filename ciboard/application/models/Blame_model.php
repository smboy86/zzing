<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Blame model class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

class Blame_model extends CB_Model {

	/**
	*  테이블명
	*/
	public $_table = 'blame';

	/**
	*  사용되는 테이블의 프라이머리키
	*/
	public $primary_key = 'bla_id';  // 사용되는 테이블의 프라이머리키

	function __construct()
	{
		parent::__construct();
	}

	public function get_admin_list($limit = '', $offset = '', $where = '', $like = '', $findex = '', $forder = '', $sfield = '', $skeyword = '', $sop='OR')
	{

		$select = 'blame.*, member.mem_id, member.mem_userid, member.mem_nickname, member.mem_is_admin, member.mem_icon';
		$join[] = array('table' => 'member', 'on' => 'blame.mem_id = member.mem_id', 'type' => 'left');
		$result = $this->_get_list_common($select, $join, $limit, $offset, $where, $like, $findex, $forder, $sfield, $skeyword, $sop);
		return $result;
	}

	function get_blame_count($type = 'd', $start_date='' , $end_date='', $brd_id='')
	{
		if ( ! $start_date) return FALSE;
		if ( ! $end_date) return FALSE;

		if ($type == 'y') $left = 4;
		else if ($type == 'm') $left = 7;
		else $left = 10;

		$this->db->select('count(*) as cnt, left(bla_datetime, ' . $left . ') as day ', FALSE);
		$this->db->where('left(bla_datetime, 10) >=' , $start_date);
		$this->db->where('left(bla_datetime, 10) <=' , $end_date);
		if($brd_id) {
			$this->db->where('brd_id' , $brd_id);
		}
		$this->db->group_by('day');
		$this->db->order_by('bla_datetime', 'desc');
		$qry = $this->db->get($this->_table);
		$result = $qry->result_array();

		return $result;

	}

	function get_blame_count_by_board($start_date='' , $end_date='')
	{
		if ( ! $start_date) return FALSE;
		if ( ! $end_date) return FALSE;

		$this->db->select('count(*) as cnt, brd_id', FALSE);
		$this->db->where('left(bla_datetime, 10) >=' , $start_date);
		$this->db->where('left(bla_datetime, 10) <=' , $end_date);
		$this->db->group_by('brd_id');
		$this->db->order_by('cnt', 'desc');
		$qry = $this->db->get($this->_table);
		$result = $qry->result_array();

		return $result;

	}

}
