<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CB_Model class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

class CB_Model extends CI_Model
{

	/* --------------------------------------------------------------
	 * VARIABLES
	 * ------------------------------------------------------------ */

	/**
	 * 테이블명
	 */
	protected $_table;

	/**
	 * 프라이머리키
	 */
	protected $primary_key;

	/**
	 * 실제 어떤 식으로든 검색이 가능한 필드
	 */
	public $allow_search_field = array();

	/**
	 * 실제 어떤 식으로든 정렬이 가능한 필드
	 */
	public $allow_order_field = array();

	/**
	 * = 검색 필드 , like 검색이 아님
	 */
	public $search_field_equal = array();

	/* --------------------------------------------------------------
	 * GENERIC METHODS
	 * ------------------------------------------------------------ */

	/**
	 * Initialise the model, tie into the CodeIgniter superobject and
	 * try our best to guess the table name.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	public function get($primary_value='' , $select = '', $where='', $limit = '' , $offset = 0, $findex = '', $forder = '')
	{

		$result = $this->_get($primary_value, $select, $where, $limit, $offset, $findex, $forder);
		return $result->result_array();
	}

	public function get_one($primary_value='' , $select = '', $where='')
	{
		$result = $this->_get($primary_value, $select, $where, 1);
		return $result->row_array();
	}

	public function _get($primary_value='' , $select = '', $where='', $limit = '' , $offset = 0, $findex = '', $forder = '')
	{

		if ($select) $this->db->select($select);
		$this->db->from($this->_table);
		if ($primary_value) $this->db->where($this->primary_key, $primary_value);
		if ($where) $this->db->where($where);
		if ($findex) {
			if(strtoupper($forder) == 'RANDOM') {
				$forder = 'RANDOM';
			} else if(strtoupper($forder) == 'DESC') {
				$forder = 'DESC';
			} else {
				$forder = 'ASC';
			}
			$this->db->order_by($findex, $forder);
		}
		if (is_numeric($limit) && is_numeric($offset)) $this->db->limit($limit, $offset);
		$result = $this->db->get();

		return $result;
	}

	public function _get_list_common($select='', $join='', $limit = '', $offset = '', $where = '', $like = '', $findex = '', $forder = '', $sfield = '', $skeyword = '', $sop='OR')
	{

		if ( ! $findex OR ! in_array($findex, $this->allow_order_field)) $findex = $this->primary_key;

		$forder = ( strtoupper($forder) == 'ASC') ?  'ASC' : 'DESC';
		$sop = (strtoupper($sop) == 'AND') ? 'AND' : 'OR';

		$count_by_where = array();
		$search_where = array();
		$search_like = array();
		$search_or_like = array();
		if ($sfield && is_array($sfield)) {
			foreach ($sfield as $skey => $sval) {
				$ssf = $sval;
				if ($skeyword && $ssf && in_array($ssf, $this->allow_search_field)) {
					if (in_array($ssf , $this->search_field_equal)) {
						$search_where[$ssf] =$skeyword;
					} else {
						$swordarray = explode(' ', $skeyword);
						foreach ($swordarray as $str) {
							if ($ssf == '') continue;
							if ($sop == 'AND') {
								$search_like[] = array($ssf => $str);
							} else {
								$search_or_like[] = array($ssf => $str);
							}
						}
					}
				}
			}
		} else {
			$ssf = $sfield;
			if ($skeyword && $ssf && in_array($ssf, $this->allow_search_field)) {
				if (in_array($ssf , $this->search_field_equal)) {
					$search_where[$ssf] =$skeyword;
				} else {
					$swordarray = explode(' ', $skeyword);
					foreach ($swordarray as $str) {
						if ($ssf == '') continue;
						if ($sop == 'AND') {
							$search_like[] = array($ssf => $str);
						} else {
							$search_or_like[] = array($ssf => $str);
						}
					}
				}
			}
		}
		
		if ($select) $this->db->select($select);
		$this->db->from($this->_table);
		if ($join && isset($join['table']) && $join['table'] && isset($join['on']) && $join['on']) {
			if ( ! isset($join['type']) OR ! $join['type']) $join['type'] = 'left';
			$this->db->join($join['table'], $join['on'], $join['type']);
		} else if ($join && is_array($join)) {
			foreach ($join as $jkey => $jval) {
				if (isset($jval['table']) && $jval['table'] && isset($jval['on']) && $jval['on']) {
					if ( ! isset($jval['type']) OR ! $jval['type']) $jval['type'] = 'left';
					$this->db->join($jval['table'], $jval['on'], $jval['type']);
				}
			}
		}

		if ($where) $this->db->where($where);
		if ($search_where) $this->db->where($search_where);
		if ($like) $this->db->like($like);
		if ($search_like) { foreach ($search_like as $item) { foreach ($item as $skey => $sval) { $this->db->like($skey, $sval); } } }
		 if ($search_or_like) {
			$this->db->group_start();
			foreach ($search_or_like as $item) { foreach ($item as $skey => $sval) { $this->db->or_like($skey, $sval); } }
			$this->db->group_end();
		 }
		 if ($count_by_where) $this->db->where($count_by_where);

		$this->db->order_by($findex, $forder);
		if ($limit) $this->db->limit($limit, $offset);
		$qry = $this->db->get();
		$result['list'] = $qry->result_array();

		$this->db->select('count(*) as rownum');
		$this->db->from($this->_table);
		if ($join && isset($join['table']) && $join['table'] && isset($join['on']) && $join['on']) {
			if ( ! isset($join['type']) OR ! $join['type']) $join['type'] = 'left';
			$this->db->join($join['table'], $join['on'], $join['type']);
		} else if ($join && is_array($join)) {
			foreach ($join as $jkey => $jval) {
				if (isset($jval['table']) && $jval['table'] && isset($jval['on']) && $jval['on']) {
					if ( ! isset($jval['type']) OR ! $jval['type']) $jval['type'] = 'left';
					$this->db->join($jval['table'], $jval['on'], $jval['type']);
				}
			}
		}
		 if ($where) $this->db->where($where);
		 if ($search_where) $this->db->where($search_where);
		 if ($like) $this->db->like($like);
		if ($search_like) { foreach ($search_like as $item) { foreach ($item as $skey => $sval) { $this->db->like($skey, $sval); } } }
		 if ($search_or_like) {
			$this->db->group_start();
			foreach ($search_or_like as $item) { foreach ($item as $skey => $sval) { $this->db->or_like($skey, $sval); } }
			$this->db->group_end();
		 }
		 if ($count_by_where) $this->db->where($count_by_where);
		$qry = $this->db->get();
		$rows = $qry->row_array();
		$result['total_rows'] = $rows['rownum'];

		return $result;
	}

	public function delete($primary_value='', $where='')
	{
		$this->db->from($this->_table);
		if ($primary_value) $this->db->where($this->primary_key, $primary_value);
		if ($where) $this->db->where($where);
		$result = $this->db->delete($this->_table);
		return $result;
	}

	public function update($primary_value='', $updatedata='', $where='')
	{

		if ($updatedata !== FALSE) {
			if ($primary_value) $this->db->where($this->primary_key, $primary_value);
			if ($where) $this->db->where($where);
			$this->db->set($updatedata);
			$result = $this->db->update($this->_table);

			return $result;
		} else {
			return FALSE;
		}
	}

	public function insert($data=FALSE)
	{
		if ($data !== FALSE) {
			$this->db->insert($this->_table, $data);
			$insert_id = $this->db->insert_id();

			return $insert_id;
		} else {
			return FALSE;
		}
	}

	public function replace($data=FALSE)
	{
		if ($data !== FALSE) {
			$this->db->replace($this->_table, $data);

			return TRUE;
		} else {
			return FALSE;
		}
	}


	public function count_by($where = '', $like = '')
	{
		if ($where) {
			 $this->db->where($where);
		}
		if ($like) {
			$this->db->like($like);
		}
		return $this->db->count_all_results($this->_table);
	}
}
