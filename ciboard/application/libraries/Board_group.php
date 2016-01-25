<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Board group class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

/**
 * board group table 을 주로 관리하는 class 입니다.
 */
class Board_group extends CI_Controller
{

	private $CI;
	private $group_id;
	private $group_key;
	private $group;
	private $admin;
	private $call_admin;

	function __construct()
	{
		$this->CI = & get_instance();
		$this->CI->load->model('Board_group_model');
		$this->CI->load->model('Board_group_admin_model');
		$this->CI->load->model('Board_group_meta_model');
	}

	/**
	 * board group 테이블에서 가져옵니다
	 */
	public function get_baord_group($bgr_id='', $bgr_key ='')
	{

		if ( ! $bgr_id && ! $bgr_key) return FALSE;

		if ($bgr_id) {
			$group = $this->CI->Board_group_model->get_one($bgr_id);
		} else if ($bgr_key) {
			$group = $this->CI->Board_group_model->get_one('', '', array('bgr_key' => $bgr_key));
		} else {
			return FALSE;
		}
		if (element('bgr_id', $group)) {
			$group = array_merge($group, $this->get_all_meta(element('bgr_id', $group)));
		}
		if (element('bgr_id', $group)) {
			$this->group_id[element('bgr_id', $group)] = $group;
		}
		if (element('bgr_key', $group)) {
			$this->group_key[element('bgr_key', $group)] = $group;
		}
	}

	/**
	 * board group 의 모든 meta 정보를 가져옵니다 테이블에서 가져옵니다
	 */
	public function get_all_meta($bgr_id='')
	{
		if ( ! $bgr_id) return FALSE;

		$result = $this->CI->Board_group_meta_model->get_all_meta($bgr_id);
		return $result;
	}

	/**
	 * group 의 item 을 bgr_id 기반으로 얻습니다
	 */
	public function item_id($column='', $bgr_id='')
	{
		if ( ! $column) return FALSE;
		if ( ! $bgr_id) return FALSE;
		if ( ! isset($this->group_id[$bgr_id]) OR $this->group_id[$bgr_id] == FALSE) {
			$this->get_baord_group($bgr_id, '');
		}
		if ($this->group_id[$bgr_id] == FALSE) return FALSE;
		$group = $this->group_id[$bgr_id];
		return isset($group[$column]) ? $group[$column] : FALSE;

	}

	/**
	 * group 의 item 을 bgr_key 기반으로 얻습니다
	 */
	public function item_key($column='', $bgr_key='')
	{
		if ( ! $column) return FALSE;
		if ( ! $bgr_key) return FALSE;
		if ( ! isset($this->group_key[$bgr_key]) OR $this->group_key[$bgr_key] == FALSE) {
			$this->get_baord_group('', $bgr_key);
		}
		if ($this->group_key[$bgr_key] == FALSE) return FALSE;
		$group = $this->group_key[$bgr_key];
		return isset($group[$column]) ? $group[$column] : FALSE;

	}

	/**
	 * group 의 모든 item 을 bgr_id 기반으로 얻습니다
	 */
	public function item_all($bgr_id = '')
	{
		if ( ! $bgr_id) return FALSE;
		if ( ! isset($this->group_id[$bgr_id]) OR $this->group_id[$bgr_id] == FALSE) {
			$this->get_baord_group($bgr_id, '');
		}
		if ( ! isset($this->group_id[$bgr_id]) OR $this->group_id[$bgr_id] == FALSE) return FALSE;
		return $this->group_id[$bgr_id];

	}

	/**
	 * group 의 모든 정보를 얻습니다
	 */
	public function get_group($bgr_id='')
	{

		if ( ! $bgr_id) return FALSE;

		$group = $this->CI->Board_group_model->get_one($bgr_id);
		$group = array_merge($group, $this->get_all_meta($bgr_id));
		$this->group[$bgr_id] = $group;

	}

	/**
	 * group 의 item 정보를 얻습니다
	 */
	public function group_item($column='', $bgr_id='')
	{

		if ( ! $column) return FALSE;
		if ( ! $bgr_id) return FALSE;
		if ($this->group[$bgr_id] == FALSE) {
			$this->get_group($bgr_id, '');
		}
		if ($this->group[$bgr_id] == FALSE) return FALSE;
		$group = $this->group[$bgr_id];
		return isset($group[$column]) ? $group[$column] : FALSE;

	}

	/**
	 * group 의 admin 인지를 판단합니다
	 */
	public function is_admin($bgr_id = '')
	{

		if ( ! $bgr_id) return FALSE;
		if ( ! $this->CI->member->item('mem_id')) return FALSE;
		if ($this->call_admin) return $this->admin;
		$this->call_admin = TRUE;
		$count = $this->CI->Board_group_admin_model->count_by( array('bgr_id' => $bgr_id, 'mem_id' => $this->CI->member->item('mem_id')));
		if ($count) {
			$this->admin = TRUE;
		} else {
			$this->admin = FALSE;
		}

		return $this->admin;

	}

}
