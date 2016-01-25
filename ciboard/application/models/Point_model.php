<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Point model class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

class Point_model extends CB_Model {

	/**
	*  테이블명
	*/
	public $_table = 'point';

	/**
	*  사용되는 테이블의 프라이머리키
	*/
	public $primary_key = 'poi_id';  // 사용되는 테이블의 프라이머리키

	function __construct()
	{
		parent::__construct();
	}

	public function get_admin_list($limit = '', $offset = '', $where = '', $like = '', $findex = '', $forder = '', $sfield = '', $skeyword = '', $sop='OR')
	{

		$select = 'point.*, member.mem_userid, member.mem_nickname, member.mem_is_admin, member.mem_icon, member.mem_point';
		$join[] = array('table' => 'member', 'on' => 'point.mem_id = member.mem_id', 'type' => 'left');
		$result = $this->_get_list_common($select, $join, $limit, $offset, $where, $like, $findex, $forder, $sfield, $skeyword, $sop);
		return $result;
	}

	public function get_list($limit = '', $offset = '', $where = '', $like = '', $findex = '', $forder = '', $sfield = '', $skeyword = '', $sop='OR')
	{

		$result = $this->_get_list_common($select='', $join='', $limit, $offset, $where, $like, $findex, $forder, $sfield, $skeyword, $sop);
		return $result;
	}

	public function get_point_sum($mem_id='')
	{
		$this->db->select_sum('poi_point');
		$this->db->where(array('mem_id' => $mem_id));
		$result = $this->db->get('point');
		$result = $result->row_array();
		return $result['poi_point'];

	}

	public  function point_ranking_all($limit='')
	{
		if ( ! $limit) $limit = 100;
		$this->db->select_sum('poi_point');
		$this->db->select('member.mem_id, member.mem_userid, member.mem_nickname, member.mem_is_admin, member.mem_icon');
		$this->db->join('member', 'point.mem_id = member.mem_id', 'inner');
		$this->db->where('member.mem_denied', '0');
		$this->db->where('member.mem_is_admin', '0');
		$this->db->where('poi_point >', '0');
		$this->db->group_by('member.mem_id');
		$this->db->order_by('poi_point', 'DESC');
		$this->db->limit($limit);
		$qry = $this->db->get('point');
		$result = $qry->result_array();
		return $result;
	}

	public  function point_ranking_month($year='', $month='', $limit='')
	{
		if ( ! $year) $year = cdate('Y');
		if ( ! $month) $month = cdate('m');

		$start_datetime = $year . '-' . $month . '-01 00:00:00';
		$end_datetime = $year . '-' . $month . '-31 23:59:59';

		if ( ! $limit) $limit = 100;
		$this->db->select_sum('poi_point');
		$this->db->select('member.mem_id, member.mem_userid, member.mem_nickname, member.mem_is_admin, member.mem_icon');
		$this->db->join('member', 'point.mem_id = member.mem_id', 'inner');
		$this->db->where('member.mem_denied', '0');
		$this->db->where('member.mem_is_admin', '0');
		$this->db->where('point.poi_datetime >=', $start_datetime);
		$this->db->where('point.poi_datetime <=', $end_datetime);
		$this->db->where('poi_point >', '0');
		$this->db->group_by('member.mem_id');
		$this->db->order_by('poi_point', 'DESC');
		$this->db->limit($limit);
		$qry = $this->db->get('point');
		$result = $qry->result_array();
		return $result;
	}

}
