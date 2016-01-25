<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Board model class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

class Board_model extends CB_Model {

	/**
	*  테이블명
	*/
	public $_table = 'board';

	/**
	*  사용되는 테이블의 프라이머리키
	*/
	public $primary_key = 'brd_id';  // 사용되는 테이블의 프라이머리키

	public $cache_prefix = 'board-model-get-'; // 캐시 사용시 프리픽스

	public $cache_time = 86400 ; // 캐시 저장시간

	function __construct()
	{
		parent::__construct();
	}

	public function get_admin_list($limit = '', $offset = '', $where = '', $like = '', $findex = '', $forder = '', $sfield = '', $skeyword = '', $sop='OR')
	{
		$result = $this->_get_list_common($select='', $join='', $limit, $offset, $where, $like, $findex, $forder, $sfield, $skeyword, $sop);
		return $result;
	}

	function get_one($primary_value='' , $select = '', $where='')
	{
		$use_cache = FALSE;
		if ($primary_value != '' && $select == '' && $where == '') {
			$use_cache = TRUE;
		}

		if ($use_cache) {
			$cachename = $this->cache_prefix . $primary_value;
			if ( ! $result = $this->cache->get($cachename)) {
				$result = parent::get_one($primary_value);
				 $this->cache->save($cachename, $result, $this->cache_time);
			}
		} else {
				$result = parent::get_one($primary_value, $select, $where);
		}
		return $result;
	}

	public function delete($primary_value='', $where = '')
	{
		if ( ! $primary_value) return FALSE;
		$result = parent::delete($primary_value);
		$this->cache->delete($this->cache_prefix . $primary_value);

		return $result;
	}

	public function update($primary_value='', $updatedata='', $where ='')
	{

		if ( ! $primary_value) return FALSE;

		$result = parent::update($primary_value, $updatedata);
		if ($result) {
			$this->cache->delete($this->cache_prefix . $primary_value);
		}
		return $result;

	}

	public function insert($updatedata=FALSE)
	{

		$result = parent::insert($updatedata);
		return $result;

	}

	function get_group_select($bgr_id = '')
	{

		$option = '<option value="">그룹선택</option>';

		$this->db->order_by('bgr_order', 'ASC');
		$this->db->select('bgr_id, bgr_name');
		$qry = $this->db->get('board_group');
		foreach ($qry->result_array() as $row) {
			$option .='<option value="' . $row['bgr_id'] . '"';
			if ($row['bgr_id'] == $bgr_id) {
				$option .= ' selected="selected" ';
			}
			$option .='>' . $row['bgr_name'] . '</option>';
		}

		return $option;
	}

}
