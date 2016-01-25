<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Config model class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

class Config_model extends CB_Model {

	/**
	*  테이블명
	*/
	public $_table = 'config';

	/**
	*  사용되는 테이블의 프라이머리키
	*/
	public $meta_key = 'cfg_key';

	public $meta_value = 'cfg_value';

	public $cache_name= 'config-model-get'; // 캐시 사용시 프리픽스

	public $cache_time = 86400 ; // 캐시 저장시간

	function __construct()
	{
		parent::__construct();
	}

	function get_all_meta()
	{

		$cachename = $this->cache_name;
		if ( ! $result = $this->cache->get($cachename)) {
			$result = array();
			$res = $this->get();
			if ($res && is_array($res)) {
				foreach ($res as $val) {
					$result[$val[$this->meta_key]] = $val[$this->meta_value];
				}
			}

			$this->cache->save($cachename, $result, $this->cache_time);

		}
		return $result;

	}

	function save($savedata='')
	{

		if ($savedata && is_array($savedata)) {
			foreach ($savedata as $column => $value) {
					$this->meta_update($column, $value);
			}
		}
		$this->cache->delete($this->cache_name);

	}

	function meta_update($column='', $value=FALSE)
	{
		$column = trim($column);
		if ( ! $column) return FALSE;

		$old_value = $this->item($column);
		if (empty($value)) {
			$value = '';
		}
		if ($value === $old_value)
			return FALSE;

		if (FALSE === $old_value)
			return $this->add_meta($column, $value);

		return $this->update_meta($column, $value);

	}

	function item($column='')
	{
		if ( ! $column) return FALSE;

		$result = $this->get_all_meta();

		return isset($result[ $column ]) ? $result[ $column ] :  FALSE;
	}

	function add_meta($column='', $value='')
	{
		$column = trim($column);
		if ( ! $column) return FALSE;
		
		$updatedata = array(
			'cfg_key' => $column,
			'cfg_value' => $value,
		);
		$this->db->replace($this->_table, $updatedata);

		return TRUE;

	}

	function update_meta($column='', $value='')
	{
		$column = trim($column);
		if ( ! $column) return FALSE;

		$this->db->where($this->meta_key, $column);
		$data = array($this->meta_value => $value);
		$this->db->update($this->_table, $data);

		return TRUE;

	}

}
