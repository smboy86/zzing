<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Point class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

/**
 * 포인트 추가 및 삭제를 관리하는 class 입니다.
 */
class Point extends CI_Controller
{

	private $CI;

	function __construct()
	{
		$this->CI = & get_instance();
		$this->CI->load->helper( array('array'));
	}

	/**
	  * 포인트를 추가합니다
	  */
	function insert_point($mem_id='', $point='', $content='', $poi_type='', $poi_related_id='', $poi_action='')
	{

		// 포인트 사용을 하지 않는다면 return
		if ( ! $this->CI->cbconfig->item('use_point')) {
			return FALSE;
		}

		// 포인트가 없다면 업데이트 할 필요 없음
		if ($point == '' OR $point == 0 OR $point == '0') {
			return FALSE;
		}

		// 회원아이디가 없다면 업데이트 할 필요 없음
		if ( ! $mem_id) {
			return FALSE;
		}

		if ( ! $content) {
			return FALSE;
		}

		if ( ! $poi_type && ! $poi_related_id && ! $poi_action) {
			return FALSE;
		}

		$member = $this->CI->Member_model->get_by_memid($mem_id, 'mem_id');

		if ( ! element('mem_id', $member)) {
			return FALSE;
		}

		$this->CI->load->model('Point_model');

		// 이미 등록된 내역이라면 건너뜀
		if ($poi_type OR $poi_related_id OR $poi_action) {
			$where = array(
				'mem_id' => $mem_id,
				'poi_type' => $poi_type,
				'poi_related_id' => $poi_related_id,
				'poi_action' => $poi_action,
			);
			$cnt = $this->CI->Point_model->count_by($where);

			if ($cnt > 0)
				return FALSE;
		}

		$insertdata = array(
			'mem_id' => $mem_id,
			'poi_datetime' => cdate('Y-m-d H:i:s'),
			'poi_content' => $content,
			'poi_point' => $point,
			'poi_type' => $poi_type,
			'poi_related_id' => $poi_related_id,
			'poi_action' => $poi_action,
		);
		$this->CI->Point_model->insert($insertdata);

		$sum = $this->CI->Point_model->get_point_sum($mem_id);
		$this->CI->Member_model->update($mem_id, array('mem_point' => $sum));

		return $sum;
	}

	/**
	  * 포인트를 삭제합니다
	  */
	function delete_point($mem_id='', $poi_type='', $poi_related_id='', $poi_action='')
	{
		if ( ! $mem_id) return FALSE;

		if ($poi_type OR $poi_related_id OR $poi_action) {
			$this->CI->load->model('Point_model');

			$where = array(
				'mem_id' => $mem_id,
				'poi_type' => $poi_type,
				'poi_related_id' => $poi_related_id,
				'poi_action' => $poi_action,
			);
			$this->CI->Point_model->delete('', $where);

			// 포인트 내역의 합을 구하고
			$sum = $this->CI->Point_model->get_point_sum($mem_id);
			$this->CI->Member_model->update($mem_id, array('mem_point' => $sum));
			return $sum;
		}

		return FALSE;
	}

	/**
	  * 포인트 PK 를 이용한 포인트 삭제입니다.
	  */
	public function delete_point_by_pk($poi_id='')
	{

		if ( ! $poi_id) return FALSE;

		$this->CI->load->model('Point_model');

		$result = $this->CI->Point_model->get_one($poi_id, 'mem_id');
		$this->CI->Point_model->delete($poi_id);

		if (element('mem_id', $result)) {
			$mem_id = element('mem_id', $result);
			// 포인트 내역의 합을 구하고
			$sum = $this->CI->Point_model->get_point_sum($mem_id);
			$this->CI->Member_model->update($mem_id, array('mem_point' => $sum));
			return $sum;
		}

		return TRUE;
	}

}
