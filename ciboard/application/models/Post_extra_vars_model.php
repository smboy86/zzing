<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Post Extra Vars model class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

class Post_extra_vars_model extends CB_Model {

	/**
	*  테이블명
	*/
	public $_table = 'post_extra_vars';

	/**
	*  사용되는 테이블의 프라이머리키
	*/
	public $parent_key = 'post_id';

	public $meta_key = 'pev_key';

	public $meta_value = 'pev_value';

	function __construct()
	{
		parent::__construct();
	}

	function get_all_meta($parent_value='')
	{
		if ( ! $parent_value) return FALSE;

		$result = array();
		$res = $this->get($primary_value='' , $select = '', array($this->parent_key => $parent_value));
		if ($res && is_array($res)) {
			foreach ($res as $val) {
				$result[$val[$this->meta_key]] = $val[$this->meta_value];
			}
		}

		return $result;

	}

	function save($parentkey='', $brd_id ='', $savedata='')
	{
		if ( ! $parentkey) return FALSE;

		if ($savedata && is_array($savedata)) {
			foreach ($savedata as $column => $value) {
					$this->meta_update($parentkey, $brd_id, $column, $value);
			}
		}
	}

	function deletemeta($parentkey='')
	{
		if ( ! $parentkey) return FALSE;

		$this->delete('', array($this->parent_key => $parentkey));
	}

	function meta_update($parentkey='', $brd_id='', $column='', $value=FALSE)
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

		if (is_array($value)) {
			$value = json_encode($value);
		}

		if (FALSE === $old_value)
			return $this->add_meta($parentkey, $brd_id, $column, $value);

		return $this->update_meta($parentkey, $column, $value);

	}

	function item($parentkey='', $column='')
	{
		if ( ! $parentkey) return FALSE;
		if ( ! $column) return FALSE;

		$result = $this->get_all_meta($parentkey);

		return isset($result[ $column ]) ? $result[ $column ] :  FALSE;
	}

	function add_meta($parentkey='', $brd_id='', $column='', $value='')
	{
		if ( ! $parentkey) return FALSE;

		$column = trim($column);
		if ( ! $column) return FALSE;

		$updatedata = array(
			'post_id' => $parentkey,
			'brd_id' => $brd_id,
			'pev_key' => $column,
			'pev_value' => $value,
		);
		$this->db->replace($this->_table, $updatedata);

		return TRUE;

	}

	function deletemeta_item($column='')
	{
		if ( ! $column) return FALSE;

		$this->delete('', array($this->meta_key=>$column));

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
