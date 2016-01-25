<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Stat Count Board model class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

class Stat_count_board_model extends CB_Model {

	/**
	*  테이블명
	*/
	public $_table = 'stat_count_board';

	/**
	*  사용되는 테이블의 프라이머리키
	*/
	public $primary_key = 'scb_id';  // 사용되는 테이블의 프라이머리키

	function __construct()
	{
		parent::__construct();
	}

	function get_board_count($type = 'd', $start_date='', $end_date='', $brd_id='')
	{
		if ( ! $start_date) return FALSE;
		if ( ! $end_date) return FALSE;

		if ($type == 'y') {
			$left = 4;
		} else if ($type == 'm') {
			$left = 7;
		}
		if ($type == 'y' OR $type == 'm') {
			$this->db->select_sum('scb_count');
			$this->db->select('left(scb_date, ' . $left . ') as day, brd_id  ', FALSE);
		} else {
			$this->db->select_sum('scb_count');
			$this->db->select('scb_date as day, brd_id ', FALSE);
		}
		$this->db->where('scb_date >=' , $start_date);
		$this->db->where('scb_date <=' , $end_date);
		if($brd_id) {
			$this->db->where('brd_id' , $brd_id);
			$this->db->group_by(array('day'));
		} else {
			$this->db->group_by(array('day', 'brd_id'));
		}
		$this->db->order_by('scb_id', 'desc');
		$qry = $this->db->get($this->_table);
		$result = $qry->result_array();

		return $result;

	}

	function add_visit_board($brd_id='')
	{
		if ( ! $brd_id) return FALSE;

		$sql = 'INSERT INTO ' . $this->db->dbprefix($this->_table) . " (scb_date, brd_id, scb_count) VALUES ('" . cdate('Y-m-d') . "', '" . $brd_id . "', 1) ON DUPLICATE KEY UPDATE scb_count= scb_count + 1 ";
		return $this->db->query($sql);
	}

}
