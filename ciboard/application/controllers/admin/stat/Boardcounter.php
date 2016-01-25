<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Boardcounter class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

/**
 * 관리자>통계관리>게시판별접속자 controller 입니다.
 */
class Boardcounter extends CB_Controller
{

	/**
	*  관리자 페이지 상의 현재 디렉토리입니다
	*  페이지 이동시 필요한 정보입니다
	*/
	public $pagedir = 'stat/boardcounter';

	/**
	*  모델을 로딩합니다
	*/
	protected $models = array('Stat_count_board', 'Board');

	/**
	*  이 컨트롤러의 메인 모델 이름입니다
	*/
	protected $modelname = 'Stat_count_board_model';

	/**
	*  헬퍼를 로딩합니다
	*/
	protected $helpers = array('form', 'array');

	function __construct()
	{
		 parent::__construct();

		/**
		*  라이브러리를 로딩합니다
		*/
		$this->load->library(array('pagination', 'querystring'));
	}

	/**
	* 목록을 가져오는 메소드입니다
	*/
	public function index()
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_admin_stat_boardcounter_index';
		$this->load->event($eventname);

		$view = array();
		$view['view'] = array();

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before'] = Events::trigger('before', $eventname);
		
		$param =& $this->querystring;
		$datetype = $param->get('datetype', 'd');
		if ($datetype != 'm' && $datetype != 'y') {
			$datetype = 'd';
		}
		$start_date = $param->get('start_date', cdate('Y-m-01'));
		$end_date = $param->get('end_date', cdate('Y-m-') . cdate('t'));

		$brd_id = $param->get('brd_id');

		$result = $this->{$this->modelname}->get_board_count($datetype, $start_date, $end_date, $brd_id);

		$sum_count = 0;
		$arr = array();
		$_day = array();
		$_brd = array();
		$arr = array();
		$max = 0;
		if ($result && is_array($result)) {
			foreach ($result as $key => $value) {
				$s = element('day', $value) . '_' . element('brd_id', $value);
				if ( ! isset($arr[$s])) {
					$arr[$s]=0;
				}
				$arr[$s]+=element('scb_count', $value);
				if ( ! isset($_day[$s])) {
					$_day[$s] = element('day', $value);
				}
				if ( ! isset($_brd[$s])) {
					$brdresult = $this->board->item_id('brd_name', element('brd_id', $value));
					$_brd[$s] = $brdresult ? $brdresult : '-';
				}
				if (element('scb_count', $value) > $max) {
							$max = element('scb_count', $value);
				}
				$sum_count+=element('scb_count', $value);

			}
		}

		$view['view']['list'] = array();
		$i = 0;
		$k = 0;
		$save_count = -1;
		$tot_count = 0;

		if (count($arr)) {
			foreach ($arr as $key => $value) {
				$count = $arr[$key];
				$view['view']['list'][$k]['count'] = $count;
				$i++;
				if ($save_count != $count) {
					$no = $i;
					$save_count = $count;
				}
				$view['view']['list'][$k]['day'] = $_day[$key];
				$view['view']['list'][$k]['boardname'] = $_brd[$key];
				$rate = ($count / $sum_count * 100);
				$view['view']['list'][$k]['rate'] = $rate;
				$s_rate = number_format($rate, 1);
				$view['view']['list'][$k]['s_rate'] = $s_rate;

				$bar = (int)($count / $max * 100);
				$view['view']['list'][$k]['bar'] = $bar;
				$k++;
			}

			$view['view']['max_value'] = $max;
			$view['view']['sum_count'] = $sum_count;
		}

		$view['view']['start_date'] = $start_date;
		$view['view']['end_date'] = $end_date;

		$view['view']['boardlist'] = $this->Board_model->get('' , $select = 'brd_id, brd_name', $where='', $limit = '' , $offset = 0, $findex = 'brd_order', $forder = 'ASC');

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

		/**
		* 어드민 레이아웃을 정의합니다
		*/
		$layoutconfig		= array('layout' => 'layout', 'skin' => 'index');
		$view['layout']	= $this->managelayout->admin($layoutconfig, $this->cbconfig->get_device_view_type());
		$this->data			= $view;
		$this->layout		= element('layout_skin_file', element('layout', $view));
		$this->view			= element('view_skin_file', element('layout', $view));

	}

	/**
	* 오래된 링크클릭로그삭제 페이지입니다
	*/
	public function cleanlog()
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_admin_stat_boardcounter_cleanlog';
		$this->load->event($eventname);

		$view = array();
		$view['view'] = array();

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before'] = Events::trigger('before', $eventname);
		
		/**
		* Validation 라이브러리를 가져옵니다
		*/
		$this->load->library('form_validation');

		/**
		* 전송된 데이터의 유효성을 체크합니다
		*/
		$config = array(
			array('field'=>'day', 'label'=>'기간', 'rules'=>'trim|required|numeric|is_natural'),
		);
		$this->form_validation->set_rules($config);

		/**
		* 유효성 검사를 하지 않는 경우, 또는 유효성 검사에 실패한 경우입니다. 즉 글쓰기나 수정 페이지를 보고 있는 경우입니다
		*/
		if ($this->form_validation->run() == FALSE) {

			// 이벤트가 존재하면 실행합니다
			$view['view']['event']['formrunfalse'] = Events::trigger('formrunfalse', $eventname);

		} else {
			/**
			* 유효성 검사를 통과한 경우입니다. 즉 데이터의 insert 나 update 의 process 처리가 필요한 상황입니다
			*/

			// 이벤트가 존재하면 실행합니다
			$view['view']['event']['formruntrue'] = Events::trigger('formruntrue', $eventname);

			if ($this->input->post('criterion') && $this->input->post('day')) {
				$this->Stat_count_board_model->delete('', array('scb_date <=' => $this->input->post('criterion')));
				$view['view']['alert_message'] = '총 ' . number_format($this->input->post('log_count')) . ' 건의 ' . $this->input->post('day') . '일 이상된 게시판별접속자로그가 모두 삭제되었습니다';
			} else {
				$criterion = cdate('Y-m-d', ctimestamp() - $this->input->post('day') * 24 * 60 * 60);
				$log_count = $this->Stat_count_board_model->count_by( array('scb_date <=' => $criterion));
				$view['view']['criterion'] = $criterion;
				$view['view']['day'] = $this->input->post('day');
				$view['view']['log_count'] = $log_count;
				if ($log_count > 0) {
					$view['view']['msg'] = '총 ' . number_format($log_count) . ' 건의 ' . $this->input->post('day') . '일 이상된 게시판별접속자로그가 발견되었습니다. 이를 모두 삭제하시겠습니까?';
				} else {
					$view['view']['alert_message'] = $this->input->post('day') . '일 이상된 게시판별접속자로그가 발견되지 않았습니다';
				}
			}
		}

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

		/**
		* 어드민 레이아웃을 정의합니다
		*/
		$layoutconfig		= array('layout' => 'layout', 'skin' => 'cleanlog');
		$view['layout']	= $this->managelayout->admin($layoutconfig, $this->cbconfig->get_device_view_type());
		$this->data			= $view;
		$this->layout		= element('layout_skin_file', element('layout', $view));
		$this->view			= element('view_skin_file', element('layout', $view));

	}



}
