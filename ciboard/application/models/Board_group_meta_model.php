<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Board Group Meta model class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

class Board_group_meta_model extends CB_Model {

	/**
	*  테이블명
	*/
	public $_table = 'board_group_meta';

	/**
	*  사용되는 테이블의 프라이머리키
	*/
	public $parent_key = 'bgr_id';

	public $meta_key = 'bgm_key';

	public $meta_value = 'bgm_value';

	public $cache_prefix= 'board-group-meta-model-get-'; // 캐시 사용시 프리픽스

	public $cache_time = 86400 ; // 캐시 저장시간

	function __construct()
	{
		parent::__construct();
	}

	function get_all_meta($parent_value='')
	{
		if ( ! $parent_value) return FALSE;

		$cachename = $this->cache_prefix . $parent_value;
		if ( ! $result = $this->cache->get($cachename)) {
			$result = array();
			$res = $this->get($primary_value='' , $select = '', array($this->parent_key => $parent_value));
			if ($res && is_array($res)) {
				foreach ($res as $val) {
					$result[$val[$this->meta_key]] = $val[$this->meta_value];
				}
			}

			$this->cache->save($cachename, $result, $this->cache_time);

		}
		return $result;

	}

	function save($parentkey='', $savedata='')
	{
		if ( ! $parentkey) return FALSE;

		if ($savedata && is_array($savedata)) {
			foreach ($savedata as $column => $value) {
					$this->meta_update($parentkey, $column, $value);
			}
		}
		$this->cache->delete($this->cache_prefix . $parentkey);

	}

	function deletemeta($parentkey='')
	{
		if ( ! $parentkey) return FALSE;

		$this->delete('', array($this->parent_key => $parentkey));
		$this->cache->delete($this->cache_prefix . $parentkey);

	}

	function meta_update($parentkey='', $column='', $value=FALSE)
	{
		if ( ! $parentkey) return FALSE;

		$column = trim($column);
		if ( ! $column) return FALSE;

		$old_value = $this->item($parentkey, $column);
		if (empty($value)) {
			$value = '';
		}
		if ($value === $old_value)
			return FALSE;

		if (FALSE === $old_value)
			return $this->add_meta($parentkey, $column, $value);

		return $this->update_meta($parentkey, $column, $value);

	}

	function item($parentkey='', $column='')
	{
		if ( ! $parentkey) return FALSE;
		if ( ! $column) return FALSE;

		$result = $this->get_all_meta($parentkey);

		return isset($result[ $column ]) ? $result[ $column ] :  FALSE;
	}

	function add_meta($parentkey='', $column='', $value='')
	{
		if ( ! $parentkey) return FALSE;

		$column = trim($column);
		if ( ! $column) return FALSE;

		$updatedata = array(
			'bgr_id' => $parentkey,
			'bgm_key' => $column,
			'bgm_value' => $value,
		);
		$this->db->replace($this->_table, $updatedata);

		return TRUE;

	}

	function update_meta($parentkey='', $column='', $value='')
	{
		if ( ! $parentkey) return FALSE;

		$column = trim($column);
		if ( ! $column) return FALSE;

		$this->db->where($this->parent_key, $parentkey);
		$this->db->where($this->meta_key, $column);
		$data = array($this->meta_value => $value);
		$this->db->update($this->_table, $data);

		return TRUE;

	}

}
