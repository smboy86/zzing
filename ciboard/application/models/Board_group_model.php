<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Board Group model class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

class Board_group_model extends CB_Model {

	/**
	*  테이블명
	*/
	public $_table = 'board_group';

	/**
	*  사용되는 테이블의 프라이머리키
	*/
	public $primary_key = 'bgr_id';  // 사용되는 테이블의 프라이머리키

	public $cache_prefix = 'board-group-model-get-'; // 캐시 사용시 프리픽스

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

	public function delete($primary_value='', $where= '')
	{
		if ( ! $primary_value) return FALSE;

		$result = parent::delete($primary_value);
		$this->cache->delete($this->cache_prefix . $primary_value);

		return $result;
	}

	public function update($primary_value='', $updatedata='', $where='')
	{
		if ( ! $primary_value) return FALSE;

		$result = parent::update($primary_value, $updatedata);
		if ($result) {
			$this->cache->delete($this->cache_prefix . $primary_value);
		}
		return $result;

	}

}
