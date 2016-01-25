<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Post File model class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

class Post_file_model extends CB_Model {

	/**
	*  테이블명
	*/
	public $_table = 'post_file';

	/**
	*  사용되는 테이블의 프라이머리키
	*/
	public $primary_key = 'pfi_id';  // 사용되는 테이블의 프라이머리키

	function __construct()
	{
		parent::__construct();
	}

	public function get_admin_list($limit = '', $offset = '', $where = '', $like = '', $findex = '', $forder = '', $sfield = '', $skeyword = '', $sop='OR')
	{

		$select = 'post_file.*, post.mem_id as post_mem_id, post.post_userid, post.post_nickname, post.brd_id, post.post_datetime, post.post_hit, post.post_secret, post.post_title';
		$join[] = array('table' => 'post', 'on' => 'post_file.post_id = post.post_id', 'type'=>'inner');
		$result = $this->_get_list_common($select, $join, $limit, $offset, $where, $like, $findex, $forder, $sfield, $skeyword, $sop);
		return $result;
	}


	function get_post_file_by_date($type = 'd', $start_date='' , $end_date='', $brd_id='')
	{
		if ( ! $start_date) return FALSE;
		if ( ! $end_date) return FALSE;

		if ($type == 'y') $left = 4;
		else if ($type == 'm') $left = 7;
		else $left = 10;

		$this->db->select('count(*) as cnt, left(pfi_datetime, ' . $left . ') as day ', FALSE);
		$this->db->where('left(pfi_datetime, 10) >=' , $start_date);
		$this->db->where('left(pfi_datetime, 10) <=' , $end_date);
		if($brd_id) {
			$this->db->where('brd_id' , $brd_id);
		}
		$this->db->group_by('day');
		$this->db->order_by('pfi_datetime', 'desc');
		$qry = $this->db->get($this->_table);
		$result = $qry->result_array();

		return $result;

	}

	function get_post_file_count($post_id='')
	{
		if ( ! $post_id) return FALSE;

		$this->db->select('count(*) as cnt, pfi_is_image ', FALSE);
		$this->db->where('post_id' , $post_id);
		$this->db->group_by('pfi_is_image');
		$qry = $this->db->get($this->_table);
		$result = $qry->result_array();

		return $result;
	}

	public function update_plus($primary_value='', $field='', $count='')
	{
		if ( ! $primary_value) return FALSE;
		if ( ! $field) return FALSE;
		if ( ! $count) return FALSE;


		$this->db->where($this->primary_key, $primary_value);
		if ($count > 0) {
			$this->db->set($field, $field . '+' . $count, FALSE);
		} else {
			$this->db->set($field, $field . $count, FALSE);
		}
		$result = $this->db->update($this->_table);

		return $result;
	}

}
