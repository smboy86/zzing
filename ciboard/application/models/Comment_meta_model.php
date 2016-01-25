<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Comment Meta model class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

class Comment_meta_model extends CB_Model {

	/**
	*  테이블명
	*/
	public $_table = 'comment_meta';

	/**
	*  사용되는 테이블의 프라이머리키
	*/
	public $parent_key = 'cmt_id';

	public $meta_key = 'cme_key';

	public $meta_value = 'cme_value';

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

	function save($parentkey='', $savedata='')
	{
		if ( ! $parentkey) return FALSE;

		if ($savedata && is_array($savedata)) {
			foreach ($savedata as $column => $value) {
					$this->meta_update($parentkey, $column, $value);
			}
		}
	}

	function deletemeta($parentkey='')
	{
		if ( ! $parentkey) return FALSE;

		$this->delete('', array($this->parent_key => $parentkey));

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
		if ($value === $old_value) {
			return FALSE;
		}

		if (FALSE === $old_value) {
			return $this->add_meta($parentkey, $column, $value);
		}

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
			'cmt_id' => $parentkey,
			'cme_key' => $column,
			'cme_value' => $value,
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

	function delete_meta_column($parentkey='', $column='')
	{
		if ( ! $parentkey) return FALSE;

		$column = trim($column);
		if ( ! $column) return FALSE;

		$this->delete('', array($this->parent_key, $parentkey, $this->meta_key=>$column));

		return TRUE;

	}

}
