<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Rssconfig class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

/**
 * 관리자>환경설정>RSS 피드 controller 입니다.
 */
class Rssconfig extends CB_Controller
{

	/**
	*  관리자 페이지 상의 현재 디렉토리입니다
	*  페이지 이동시 필요한 정보입니다
	*/
	public $pagedir = 'config/rssconfig';

	/**
	*  모델을 로딩합니다
	*/
	protected $models = array('Config');

	/**
	*  이 컨트롤러의 메인 모델 이름입니다
	*/
	protected $modelname = 'Config_model';

	/**
	*  헬퍼를 로딩합니다
	*/
	protected $helpers = array('form', 'array');

	function __construct()
	{
		 parent::__construct();
	}

	/**
	* 환경설정>RSS 피드 페이지입니다
	*/
	public function index()
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_admin_config_rssconfig_index';
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
			array('field'=>'is_submit', 'label'=>'전송', 'rules'=>'trim|numeric'),
			array('field'=>'use_total_rss_feed', 'label'=>'통합 RSS 피드사용', 'rules'=>'trim|numeric'),
			array('field'=>'total_rss_feed_content', 'label'=>'통합 RSS 내용공개', 'rules'=>'trim|numeric'),
			array('field'=>'total_rss_feed_title', 'label'=>'통합 RSS 제목', 'rules'=>'trim'),
			array('field'=>'total_rss_feed_description', 'label'=>'통합 RSS 설명', 'rules'=>'trim'),
			array('field'=>'total_rss_feed_copyright', 'label'=>'RSS 표시 저작권', 'rules'=>'trim'),
			array('field'=>'total_rss_feed_count', 'label'=>'RSS 출력 게시물수', 'rules'=>'trim|numeric|is_natural'),
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

			$array = array('use_total_rss_feed', 'total_rss_feed_content', 'total_rss_feed_title', 'total_rss_feed_description', 'total_rss_feed_copyright', 'total_rss_feed_count');
			foreach ($array as $value) {
				$savedata[$value] = $this->input->post($value);
			}

			$this->Config_model->save($savedata);
			$view['view']['alert_message'] = 'RSS 피드설정이 저장되었습니다';
		}

		$getdata = $this->Config_model->get_all_meta();
		$view['view']['data'] = $getdata;

		$whereboard = array(
			'bmt_key' => 'use_rss_total_feed',
			'bmt_value' => '1',
		);
		$boardlist = $this->Board_meta_model->get($primary_value='' , $select = '', $whereboard);
		$rssboard = array();
		if ($boardlist && is_array($boardlist)) {
			foreach ($boardlist as $key => $value) {
				$rssboard[element('brd_id', $value)] = $this->board->item_all(element('brd_id', $value));
			}
		}
		$view['view']['rssboard'] = $rssboard;

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

}
