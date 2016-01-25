<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Member class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

/**
 * member table 을 관리하는 class 입니다.
 */
class Member extends CI_Controller
{

	private $CI;
	private $mb;

	function __construct()
	{
		$this->CI = & get_instance();
		$this->CI->load->model( array('Member_model', 'Member_meta_model', 'Member_extra_vars_model'));
		$this->CI->load->helper( array('array'));
	}

	/**
	 * 접속한 유저가 회원인지 아닌지를 판단합니다
	 */
	public function is_member()
	{
		if ($this->CI->session->userdata('mem_id')) {
			return $this->CI->session->userdata('mem_id');
		} else {
			return FALSE;
		}

	}

	/**
	 * 접속한 유저가 관리자인지 아닌지를 판단합니다
	 */
	public function is_admin($check = array())
	{

		if ($this->item('mem_is_admin')) {
			return 'super';
		} else if (element('group_id', $check)) {
			$this->CI->load->library('board_group');
			return $this->CI->board_group->is_admin(element('group_id', $check)) ? 'group' : FALSE;
		} else if (element('board_id', $check)) {
			$this->CI->load->library('board');
			return $this->CI->board->is_admin(element('board_id', $check)) ? 'board' : FALSE;
		} else {
			return FALSE;
		}
	}

	/**
	 * member, member_extra_vars, member_meta 테이블에서 정보를 가져옵니다
	 */
	public function get_member()
	{
		if ($this->is_member()) {
			if ( ! $this->mb) {
				$member = $this->CI->Member_model->get_by_memid($this->is_member());
				$member = array_merge($member, $this->get_all_extras(element('mem_id', $member)));
				$member = array_merge($member, $this->get_all_meta(element('mem_id', $member)));
				$this->mb = $member;
			}
			return $this->mb;
		} else {
			return FALSE;
		}
	}

	/**
	 * get_member 에서 가져온 데이터의 item 을 보여줍니다
	 */
	public function item($column='')
	{
		if ( ! $column) return FALSE;
		if ($this->mb == FALSE) {
			$this->get_member();
		}
		if ($this->mb == FALSE) return FALSE;
		$member = $this->mb;
		return isset($member[$column]) ? $member[$column] : FALSE;

	}

	/**
	 * member_extra_vars 테이블에서 가져옵니다
	 */
	public function get_all_extras($mem_id='')
	{
		if ( ! $mem_id) return FALSE;

		$result = array();
		$res = $this->CI->Member_extra_vars_model->get($primary_value='' , $select = '', array('mem_id' => $mem_id));
		if ($res && is_array($res)) {
			foreach ($res as $val) {
				$result[$val['mev_key']] = $val['mev_value'];
			}
		}
		return $result;
	}

	/**
	 * member_meta 테이블에서 가져옵니다
	 */
	public function get_all_meta($mem_id='')
	{
		if ( ! $mem_id) return FALSE;

		$result = array();
		$res = $this->CI->Member_meta_model->get($primary_value='' , $select = '', array('mem_id' => $mem_id));
		if ($res && is_array($res)) {
			foreach ($res as $val) {
				if (element('mmt_key', $val)) {
					$result[element('mmt_key', $val)] = element('mmt_value', $val);
				}
			}
		}
		return $result;
	}

}
