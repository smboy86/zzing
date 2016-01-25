<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// 검색 파라미터
class Querystring {
	private $CI;

	public function __construct()
	{
		$this->CI =& get_instance();
	}

	// 값 가져오기
	public function get($param, $value=FALSE)
	{
		if ( ! $this->CI->input->get($param)) {
			return $value;
		} else {
			return $this->CI->input->get($param);
		}
	}

	// 전체 주소
	public function output()
	{
		return $this->CI->input->server('QUERY_STRING');
	}

	// 쿼리스트링 수정
	public function replace($key='', $val='', $query_string = '')
	{
		if ( ! $key)
			return FALSE;

		$query_string = $query_string ? $query_string : $this->CI->input->server('QUERY_STRING');
		parse_str($query_string, $qr);

		// remove from query string
		if ($key != '')
		{
			if($val) {
				$qr[$key] = $val;
			} else {
				unset($qr[$key]);
			}
		}
		// return result
		$return = '';
		if (count($qr) > 0)
		{
			$return = http_build_query($qr);
		}
		return $return;

	}

	// 필드 정렬
	public function sort($findex, $forder='desc')
	{
		if ($this->get('findex') == $findex) {
			$param_qstr = $this->replace('forder', (strtolower($this->get('forder')) == 'asc') ? 'desc' : 'asc');
		} else {
			$param_qstr = $this->replace('forder', '', $this->replace('findex', '')) . '&amp;findex=' . $findex . '&amp;forder=' . $forder;
		}

		return '?' . $param_qstr;
	}

}
