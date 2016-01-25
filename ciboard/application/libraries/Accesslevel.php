<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Accesslevel class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

/**
 * 권한이 있는지 없는지 판단하는 class 입니다.
 */
class Accesslevel extends CI_Controller
{

	private $CI;

	function __construct()
	{
		$this->CI = & get_instance();
	}

	/**
	* 접근권한이 있는지를 판단합니다
	*/
	public function is_accessable($access_type='', $level='', $group='' , $check = array())
	{

		if ( ! $access_type) { // 모든 사용자
			return TRUE;
		} else if ($access_type == '1') { // 로그인 사용자
			if ($this->CI->member->is_member() == FALSE) {
				return FALSE;
			}
			return TRUE;
		} else if ($access_type == '100') { // 관리자
			if ($this->CI->member->is_admin($check) == FALSE) {
				return FALSE;
			}
			return TRUE;
		} else if ($access_type == '3') { // 특정레벨이상인자
			if ($this->CI->member->is_admin($check)) {
				return TRUE;
			}
			if ($this->CI->member->is_member() == FALSE) {
				return FALSE;
			}
			if ($this->CI->member->item('mem_level') < $level) {
				return FALSE;
			}
			return TRUE;

		}

	}

	/**
	* 접근권한이 없으면 alert 를 띄웁니다
	*/
	public function check($access_type='', $level='', $group='' , $alertmessage='' , $check = array())
	{

		if ( ! $alertmessage) {
			$alertmessage = '접근 권한이 없습니다';
		}
		$accessable = $this->is_accessable($access_type, $level, $group , $check);

		if ($accessable) {
			return TRUE;
		} else {
			alert($alertmessage);
			return FALSE;
		}

	}

}
