<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Stat Count model class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

class Stat_count_model extends CB_Model {

	/**
	*  테이블명
	*/
	public $_table = 'stat_count';

	/**
	*  사용되는 테이블의 프라이머리키
	*/
	public $primary_key = 'sco_id';  // 사용되는 테이블의 프라이머리키

	function __construct()
	{
		parent::__construct();
	}

	public function get_admin_list($limit = '', $offset = '', $where = '', $like = '', $findex = '', $forder = '', $sfield = '', $skeyword = '', $sop='OR')
	{
		$result = $this->_get_list_common($select='', $join='', $limit, $offset, $where, $like, $findex, $forder, $sfield, $skeyword, $sop);
		return $result;
	}

	function get_by_date($start_date='' , $end_date='')
	{
		if ( ! $start_date) return FALSE;
		if ( ! $end_date) return FALSE;

		$this->db->where('sco_date >=' , $start_date);
		$this->db->where('sco_date <=' , $end_date);
		$qry = $this->db->get($this->_table);
		$result = $qry->result_array();
		return $result;
	}

	function get_by_time_hour($start_date='' , $end_date='')
	{
		if ( ! $start_date) return FALSE;
		if ( ! $end_date) return FALSE;

		$this->db->select('SUBSTRING(sco_time,1,2) as time, count(sco_id) as cnt ', FALSE);
		$this->db->where('sco_date >=' , $start_date);
		$this->db->where('sco_date <=' , $end_date);
		$this->db->group_by('time');
		$this->db->order_by('time');
		$qry = $this->db->get($this->_table);
		$result = $qry->result_array();
		return $result;

	}

}
