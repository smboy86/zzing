<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Boards class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

/**
 * 관리자>게시판설정>게시판관리 controller 입니다.
 */
class Boards extends CB_Controller
{

	/**
	*  관리자 페이지 상의 현재 디렉토리입니다
	*  페이지 이동시 필요한 정보입니다
	*/
	public $pagedir = 'board/boards';

	/**
	*  모델을 로딩합니다
	*/
	protected $models = array('Board', 'Board_meta', 'Member', 'Config', 'Board_admin', 'Post_extra_vars', 'Board_category', 'Board_group');

	/**
	*  이 컨트롤러의 메인 모델 이름입니다
	*/
	protected $modelname = 'Board_model';

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
		$eventname = 'event_admin_board_boards_index';
		$this->load->event($eventname);

		$view = array();
		$view['view'] = array();

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before'] = Events::trigger('before', $eventname);

		/**
		* 페이지에 숫자가 아닌 문자가 입력되거나 1보다 작은 숫자가 입력되면 에러 페이지를 보여줍니다.
		*/
		$param =& $this->querystring;
		$page = $param->get('page', '1');
		if ( ! is_numeric($page) OR $page < 1) {
			show_404();
		}
		$view['view']['sort'] = array(
			'brd_key' => 	$param->sort('brd_key', 'asc'),
			'brd_name' => 	$param->sort('brd_name', 'asc'),
			'bgr_id' => 	$param->sort('bgr_id', 'asc'),
			'brd_order' => 	$param->sort('brd_order', 'asc'),
		);
		$findex = $param->get('findex', 'brd_order');
		$forder = $param->get('forder', 'asc');
		$sfield = $param->get('sfield');
		$skeyword = $param->get('skeyword');

		$per_page = admin_listnum();
		$offset = ($page - 1) * $per_page;

		/**
		* 게시판 목록에 필요한 정보를 가져옵니다.
		*/
		$this->{$this->modelname}->allow_search_field = array('brd_id', 'bgr_id', 'brd_key', 'brd_name', 'brd_mobile_name'); // 검색이 가능한 필드
		$this->{$this->modelname}->search_field_equal = array('brd_id', 'bgr_id'); // 검색중 like 가 아닌 = 검색을 하는 필드
		$this->{$this->modelname}->allow_order_field = array('brd_id', 'brd_key', 'brd_name', 'bgr_id', 'brd_order'); // 정렬이 가능한 필드
		$result = $this->{$this->modelname}->get_admin_list($per_page, $offset, '' , '' , $findex, $forder, $sfield, $skeyword);
		$list_num = $result['total_rows'] - ($page - 1) * $per_page;
		if (element('list', $result)) {
			foreach (element('list', $result) as $key => $val) {
				$result['list'][$key]['meta'] = $this->Board_meta_model->get_all_meta(element('brd_id', $val));
				$result['list'][$key]['group_option'] = $this->{$this->modelname}->get_group_select(element('bgr_id', $val));
				$result['list'][$key]['board_skin_option'] = get_skin_name('board', element('board_skin', $result['list'][$key]['meta']), '기본설정따름');
				$result['list'][$key]['board_mobile_skin_option'] = get_skin_name('board', element('board_mobile_skin', $result['list'][$key]['meta']), '기본설정따름');
				$result['list'][$key]['board_layout_option'] = get_skin_name('_layout', element('board_layout', $result['list'][$key]['meta']), '기본설정따름');
				$result['list'][$key]['board_mobile_layout_option'] = get_skin_name('_layout', element('board_mobile_layout', $result['list'][$key]['meta']), '기본설정따름');
				$result['list'][$key]['num'] = $list_num--;
			}
		}
		$view['view']['data'] = $result;
		/**
		* primary key 정보를 저장합니다
		*/
		$view['view']['primary_key'] = $this->{$this->modelname}->primary_key;

		/**
		* 페이지네이션을 생성합니다
		*/
		$config['base_url'] = admin_url($this->pagedir) . '?' . $param->replace('page');
		$config['total_rows'] = $result['total_rows'];
		$config['per_page'] = $per_page;
		$this->pagination->initialize($config);
		$view['view']['paging'] = $this->pagination->create_links();
		$view['view']['page'] = $page;

		/**
		* 쓰기 주소, 삭제 주소등 필요한 주소를 구합니다
		*/
		$search_option = array('brd_key' => 'KEY', 'brd_name' => '제목', 'brd_mobile_name' => '모바일 제목');
		$view['view']['skeyword'] = ($sfield && array_key_exists($sfield, $search_option)) ? $this->input->get('skeyword') : '';
		$view['view']['search_option'] = search_option($search_option, $sfield);
		$view['view']['listall_url'] = admin_url($this->pagedir);
		$view['view']['write_url'] = admin_url($this->pagedir . '/write');
		$view['view']['list_update_url'] = admin_url($this->pagedir . '/listupdate/?' . $param->output());
		$view['view']['list_delete_url'] = admin_url($this->pagedir . '/listdelete/?' . $param->output());

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
	* 게시판 글쓰기 또는 수정 페이지를 가져오는 메소드입니다
	*/
	public function write($pid = '')
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_admin_board_boards_write';
		$this->load->event($eventname);

		$view = array();
		$view['view'] = array();

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before'] = Events::trigger('before', $eventname);

		/**
		* 프라이머리키에 숫자형이 입력되지 않으면 에러처리합니다
		*/
		if ($pid != ''  && ! is_numeric($pid))  show_404();
		$primary_key = $this->{$this->modelname}->primary_key;

		/**
		* 수정 페이지일 경우 기존 데이터를 가져옵니다
		*/
		$getdata = array();
		if ($pid) {
			$getdata = $this->{$this->modelname}->get_one($pid);
			$getdata = array_merge($getdata, $this->Board_meta_model->get_all_meta(element('brd_id', $getdata)));
		} else {
			// 기본값 설정
			$getdata['brd_search'] = 1;
		}

		/**
		* Validation 라이브러리를 가져옵니다
		*/
		$this->load->library('form_validation');

		/**
		* 전송된 데이터의 유효성을 체크합니다
		*/
		$config = array(
			array('field'=>'is_submit', 'label'=>'전송', 'rules'=>'trim|numeric'),
			array('field'=>'brd_name', 'label'=>'게시판이름', 'rules'=>'trim|required'),
			array('field'=>'brd_mobile_name', 'label'=>'게시판모바일이름', 'rules'=>'trim'),
			array('field'=>'bgr_id', 'label'=>'그룹명', 'rules'=>'trim|required|numeric'),
			array('field'=>'board_layout', 'label'=>'레이아웃', 'rules'=>'trim'),
			array('field'=>'board_mobile_layout', 'label'=>'모바일레이아웃', 'rules'=>'trim'),
			array('field'=>'board_sidebar', 'label'=>'사이드바', 'rules'=>'trim'),
			array('field'=>'board_mobile_sidebar', 'label'=>'모바일사이드바', 'rules'=>'trim'),
			array('field'=>'board_skin', 'label'=>'스킨', 'rules'=>'trim'),
			array('field'=>'board_mobile_skin', 'label'=>'모바일스킨', 'rules'=>'trim'),
			array('field'=>'header_content', 'label'=>'상단내용', 'rules'=>'trim'),
			array('field'=>'footer_content', 'label'=>'하단내용', 'rules'=>'trim'),
			array('field'=>'mobile_header_content', 'label'=>'모바일상단내용', 'rules'=>'trim'),
			array('field'=>'mobile_footer_content', 'label'=>'모바일하단내용', 'rules'=>'trim'),
			array('field'=>'brd_order', 'label'=>'정렬순서', 'rules'=>'trim|required|numeric|is_natural|less_than_equal_to[10000]'),
			array('field'=>'brd_search', 'label'=>'검색여부', 'rules'=>'trim|numeric'),
		);
		if ($this->input->post($primary_key)) {
			$config[] = array('field'=>'brd_key', 'label'=>'게시판주소', 'rules'=>'trim|required|alpha_dash|min_length[3]|max_length[50]|is_unique[board.brd_key.brd_id.' . element('brd_id', $getdata) . ']');
		} else {
			$config[] = array('field'=>'brd_key', 'label'=>'게시판주소', 'rules'=>'trim|required|alpha_dash|min_length[3]|max_length[50]|is_unique[board.brd_key]');
		}
		$this->form_validation->set_rules($config);


		/**
		* 유효성 검사를 하지 않는 경우, 또는 유효성 검사에 실패한 경우입니다. 즉 글쓰기나 수정 페이지를 보고 있는 경우입니다
		*/
		if ($this->form_validation->run() == FALSE) {

			// 이벤트가 존재하면 실행합니다
			$view['view']['event']['formrunfalse'] = Events::trigger('formrunfalse', $eventname);

			$group_cnt = $this->Board_group_model->count_by();
			if ( ! $group_cnt)
			{
				alert('최소 1개 그룹이 생성되어야 합니다. 그룹관리 페이지로 이동합니다', admin_url('board/boardgroup'));
			}

		} else {
			/**
			* 유효성 검사를 통과한 경우입니다. 즉 데이터의 insert 나 update 의 process 처리가 필요한 상황입니다
			*/

			// 이벤트가 존재하면 실행합니다
			$view['view']['event']['formruntrue'] = Events::trigger('formruntrue', $eventname);

 			$updatedata = array(
				'bgr_id' => $this->input->post('bgr_id'),
				'brd_key' => $this->input->post('brd_key'),
				'brd_name' => $this->input->post('brd_name'),
				'brd_mobile_name' => $this->input->post('brd_mobile_name'),
				'brd_order' => $this->input->post('brd_order'),
				'brd_search' => $this->input->post('brd_search'),
			);
			$array = array('board_layout', 'board_mobile_layout', 'board_sidebar', 'board_mobile_sidebar', 'board_skin', 'board_mobile_skin', 'header_content', 'footer_content', 'mobile_header_content', 'mobile_footer_content',);

			$metadata = array();
			$groupdata = array();
			$alldata = array();
			$grp = $this->input->post('grp');
			$all = $this->input->post('all');

			foreach ($array as $value) {
				$metadata[$value] = $this->input->post($value);
				if (element($value, $grp)) {
					$groupdata[$value] = $this->input->post($value);
				}
				if (element($value, $all)) {
					$alldata[$value] = $this->input->post($value);
				}
			}

			/**
			* 게시물을 수정하는 경우입니다
			*/
			if ($this->input->post($primary_key)) {
				$this->{$this->modelname}->update($this->input->post($primary_key) , $updatedata);
				$this->Board_meta_model->save($pid, $metadata);

				$getdata = $this->{$this->modelname}->get_one($pid);
				if ($groupdata) {
					$where = array('bgr_id' => $getdata['bgr_id']);
					$res = $this->Board_model->get($primary_value='' , $select = '', $where);
					foreach ($res as $bkey => $bval) {
						if ($bval['brd_id'] == $getdata['brd_id']) continue;
						$this->Board_meta_model->save($bval['brd_id'], $groupdata);
					}
				}
				if ($alldata) {
					$res = $this->Board_model->get();
					foreach ($res as $bkey => $bval) {
						if ($bval['brd_id'] == $getdata['brd_id']) continue;
						$this->Board_meta_model->save($bval['brd_id'], $alldata);
					}
				}
				$view['view']['alert_message'] = '기본정보 설정이 저장되었습니다';
			} else {
				/**
				* 게시물을 새로 입력하는 경우입니다
				 * 기본값 설정입니다
				 */
				$upload_max_filesize = ini_get('upload_max_filesize');
				if ( ! preg_match("/([m|M])$/", $upload_max_filesize)) {
					$upload_max_filesize = (int)($upload_max_filesize / 1048576);
				} else {
					$array = array('m', 'M');
					$upload_max_filesize = str_replace($array, '', $upload_max_filesize);
				}
				$metadata['order_by_field'] = 'post_num, post_reply';
				$metadata['list_count'] = 20;
				$metadata['mobile_list_count'] = 10;
				$metadata['page_count'] = 5;
				$metadata['mobile_page_count'] = 3;
				$metadata['show_list_from_view'] = 1;
				$metadata['new_icon_hour'] = 24;
				$metadata['hot_icon_hit'] = 100;
				$metadata['hot_icon_day'] = 30;
				$metadata['subject_length'] = 60;
				$metadata['mobile_subject_length'] = 40;
				$metadata['reply_order'] = 'asc';
				$metadata['gallery_cols'] = 4;
				$metadata['gallery_image_width'] = 120;
				$metadata['gallery_image_height'] = 80;
				$metadata['mobile_gallery_cols'] = 2;
				$metadata['mobile_gallery_image_width'] = 120;
				$metadata['mobile_gallery_image_height'] = 80;
				$metadata['use_scrap'] = 1;
				$metadata['use_post_like'] = 1;
				$metadata['use_post_dislike'] = 1;
				$metadata['use_print'] = 1;
				$metadata['use_sns'] = 1;
				$metadata['use_prev_next_post'] = 1;
				$metadata['use_mobile_prev_next_post'] = 1;
				$metadata['use_blame'] = 1;
				$metadata['blame_blind_count'] = 3;
				$metadata['syntax_highlighter'] = 1;
				$metadata['comment_syntax_highlighter'] = 1;
				$metadata['use_autoplay'] = 1;
				$metadata['post_image_width'] = 600;
				$metadata['post_mobile_image_width'] = 400;
				$metadata['content_target_blank'] = 1;
				$metadata['use_auto_url'] = 1;
				$metadata['use_mobile_auto_url'] = 1;
				$metadata['use_post_dhtml'] = 1;
				$metadata['link_num'] = 2;
				$metadata['use_upload_file'] = 1;
				$metadata['upload_file_num'] = 2;
				$metadata['mobile_upload_file_num'] = 2;
				$metadata['upload_file_max_size'] = $upload_max_filesize;
				$metadata['comment_count'] = 20;
				$metadata['mobile_comment_count'] = 20;
				$metadata['comment_page_count'] = 5;
				$metadata['mobile_comment_page_count'] = 3;
				$metadata['use_comment_like'] = 1;
				$metadata['use_comment_dislike'] = 1;
				$metadata['use_comment_secret'] = 1;
				$metadata['comment_order'] = 'asc';
				$metadata['use_comment_blame'] = 1;
				$metadata['comment_blame_blind_count'] = 3;
				$metadata['protect_comment_num'] = 5;
				$metadata['use_sideview'] = 1;
				$metadata['use_tempsave'] = 1;

				$pid = $this->{$this->modelname}->insert($updatedata);
				$this->Board_meta_model->save($pid, $metadata);

				$getdata = $this->{$this->modelname}->get_one($pid);
				if ($groupdata) {
					$where = array('bgr_id' => $getdata['bgr_id']);
					$res = $this->Board_model->get($primary_value='' , $select = '', $where);
					foreach ($res as $bkey => $bval) {
						if ($bval['brd_id'] == $getdata['brd_id']) continue;
						$this->Board_meta_model->save($bval['brd_id'], $groupdata);
					}
				}
				if ($alldata) {
					$res = $this->Board_model->get();
					foreach ($res as $bkey => $bval) {
						if ($bval['brd_id'] == $getdata['brd_id']) continue;
						$this->Board_meta_model->save($bval['brd_id'], $alldata);
					}
				}
				$this->session->set_flashdata('message', '기본정보 설정이 저장되었습니다');

				$redirecturl = admin_url($this->pagedir . '/write/' .  $pid);
				redirect($redirecturl);
			}
		}

		$getdata = array();
		if ($pid) {
			$getdata = $this->{$this->modelname}->get_one($pid);
			$getdata = array_merge($getdata, $this->Board_meta_model->get_all_meta(element('brd_id', $getdata)));
		} else {
			// 기본값 설정
			$getdata['brd_search'] = 1;
		}

		$view['view']['data'] = $getdata;
		$view['view']['data']['group_option'] = $this->{$this->modelname}->get_group_select(set_value('bgr_id', element('bgr_id', $getdata)));
		$view['view']['data']['board_layout_option'] = get_skin_name('_layout', set_value('board_layout', element('board_layout', $getdata)), '기본설정따름');
		$view['view']['data']['board_mobile_layout_option'] = get_skin_name('_layout', set_value('board_mobile_layout', element('board_mobile_layout', $getdata)), '기본설정따름');
		$view['view']['data']['board_skin_option'] = get_skin_name('board', set_value('board_skin', element('board_skin', $getdata)), '기본설정따름');
		$view['view']['data']['board_mobile_skin_option'] = get_skin_name('board', set_value('board_mobile_skin', element('board_mobile_skin', $getdata)), '기본설정따름');

		/**
		* primary key 정보를 저장합니다
		*/
		$view['view']['primary_key'] = $primary_key;

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

		/**
		* 어드민 레이아웃을 정의합니다
		*/
		$layoutconfig		= array('layout' => 'layout', 'skin' => 'write');
		$view['layout']	= $this->managelayout->admin($layoutconfig, $this->cbconfig->get_device_view_type());
		$this->data			= $view;
		$this->layout		= element('layout_skin_file', element('layout', $view));
		$this->view			= element('view_skin_file', element('layout', $view));

	}

	/**
	* 게시판관리> 글쓰기>목록페이지
	*/
	public function write_list($pid = '')
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_admin_board_boards_write_list';
		$this->load->event($eventname);

		$view = array();
		$view['view'] = array();

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before'] = Events::trigger('before', $eventname);

		/**
		* 프라이머리키에 숫자형이 입력되지 않으면 에러처리합니다
		*/
		if ( ! $pid)  show_404();
		if ( ! is_numeric($pid))  show_404();

		$primary_key = $this->{$this->modelname}->primary_key;

		/**
		* Validation 라이브러리를 가져옵니다
		*/
		$this->load->library('form_validation');

		/**
		* 전송된 데이터의 유효성을 체크합니다
		*/
		$config = array(
			array('field'=>'is_submit', 'label'=>'전송', 'rules'=>'trim|numeric'),
			array('field'=>'order_by_field', 'label'=>'정렬방법', 'rules'=>'trim|required'),
			array('field'=>'list_count', 'label'=>'목록수', 'rules'=>'trim|required|numeric|is_natural_no_zero'),
			array('field'=>'mobile_list_count', 'label'=>'모바일목록수', 'rules'=>'trim|required|numeric|is_natural_no_zero'),
			array('field'=>'page_count', 'label'=>'페이지수', 'rules'=>'trim|required|numeric|is_natural_no_zero'),
			array('field'=>'mobile_page_count', 'label'=>'모바일페이지수', 'rules'=>'trim|required|numeric|is_natural_no_zero'),
			array('field'=>'always_show_write_button', 'label'=>'글쓰기버튼 항상보이기', 'rules'=>'trim|numeric'),
			array('field'=>'mobile_always_show_write_button', 'label'=>'글쓰기버튼 항상보이기 - 모바일', 'rules'=>'trim|numeric'),
			array('field'=>'show_list_from_view', 'label'=>'뷰페이지에서 목록보이기', 'rules'=>'trim|numeric'),
			array('field'=>'mobile_show_list_from_view', 'label'=>'뷰페이지에서 목록보이기', 'rules'=>'trim|numeric'),
			array('field'=>'new_icon_hour', 'label'=>'New아이콘보이기', 'rules'=>'trim|numeric'),
			array('field'=>'mobile_new_icon_hour', 'label'=>'New아이콘보이기 -  모바일', 'rules'=>'trim|numeric'),
			array('field'=>'hot_icon_hit', 'label'=>'Hot아이콘조회수', 'rules'=>'trim|numeric'),
			array('field'=>'mobile_hot_icon_hit', 'label'=>'Hot아이콘조회수 - 모바일', 'rules'=>'trim|numeric'),
			array('field'=>'hot_icon_day', 'label'=>'Hot아이콘기간', 'rules'=>'trim|numeric'),
			array('field'=>'mobile_hot_icon_day', 'label'=>'Hot아이콘기간 - 모바일', 'rules'=>'trim|numeric'),
			array('field'=>'subject_length', 'label'=>'제목길이', 'rules'=>'trim|required|numeric|is_natural'),
			array('field'=>'mobile_subject_length', 'label'=>'제목길이 - 모바일', 'rules'=>'trim|required|numeric|is_natural'),
			array('field'=>'except_notice', 'label'=>'공지사항제외', 'rules'=>'trim|numeric'),
			array('field'=>'mobile_except_notice', 'label'=>'공지사항제외 - 모바일', 'rules'=>'trim|numeric'),
			array('field'=>'except_all_notice', 'label'=>'전체공지제외', 'rules'=>'trim|numeric'),
			array('field'=>'mobile_except_all_notice', 'label'=>'전체공지제외 - 모바일', 'rules'=>'trim|numeric'),
			array('field'=>'reply_order', 'label'=>'답변 정렬 순서', 'rules'=>'trim'),
			array('field'=>'use_gallery_list', 'label'=>'갤러리 게시판 사용', 'rules'=>'trim|numeric'),
			array('field'=>'gallery_cols', 'label'=>'목록에 가로 이미지 개수', 'rules'=>'trim|numeric|is_natural'),
			array('field'=>'gallery_image_width', 'label'=>'목록 이미지 크기 - 가로', 'rules'=>'trim|numeric|is_natural'),
			array('field'=>'gallery_image_height', 'label'=>'목록 이미지 크기 - 세로', 'rules'=>'trim|numeric|is_natural'),
			array('field'=>'mobile_gallery_cols', 'label'=>'모바일 목록에 가로 이미지 개수', 'rules'=>'trim|numeric|is_natural'),
			array('field'=>'mobile_gallery_image_width', 'label'=>'모바일 목록 이미지 크기 - 가로', 'rules'=>'trim|numeric'),
			array('field'=>'mobile_gallery_image_height', 'label'=>'모바일 목록 이미지 크기 - 세로', 'rules'=>'trim|numeric'),
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

			$array = array('order_by_field', 'list_count', 'mobile_list_count', 'page_count', 'mobile_page_count', 'always_show_write_button', 'mobile_always_show_write_button', 'show_list_from_view', 'mobile_show_list_from_view', 'new_icon_hour', 'mobile_new_icon_hour', 'hot_icon_hit', 'mobile_hot_icon_hit', 'hot_icon_day', 'subject_length', 'mobile_subject_length', 'except_notice', 'mobile_except_notice', 'except_all_notice', 'mobile_except_all_notice', 'reply_order', 'use_gallery_list', 'gallery_cols', 'gallery_image_width', 'gallery_image_height', 'mobile_gallery_cols', 'mobile_gallery_image_width', 'mobile_gallery_image_height');

			$metadata = array();
			$groupdata = array();
			$alldata = array();
			$grp = $this->input->post('grp');
			$all = $this->input->post('all');

			foreach ($array as $value) {
				$metadata[$value] = $this->input->post($value);
				if (element($value, $grp)) {
					$groupdata[$value] = $this->input->post($value);
					if ($value == 'list_count') { $groupdata['mobile_list_count'] = $this->input->post('mobile_list_count');}
					if ($value == 'page_count') { $groupdata['mobile_page_count'] = $this->input->post('mobile_page_count');}
					if ($value == 'always_show_write_button') { $groupdata['mobile_always_show_write_button'] = $this->input->post('mobile_always_show_write_button');}
					if ($value == 'show_list_from_view') { $groupdata['mobile_show_list_from_view'] = $this->input->post('mobile_show_list_from_view');}
					if ($value == 'new_icon_hour') { $groupdata['mobile_new_icon_hour'] = $this->input->post('mobile_new_icon_hour');}
					if ($value == 'hot_icon_hit') {
						$groupdata['mobile_hot_icon_hit'] = $this->input->post('mobile_hot_icon_hit');
						$groupdata['hot_icon_day'] = $this->input->post('hot_icon_day');
						$groupdata['mobile_hot_icon_day'] = $this->input->post('mobile_hot_icon_day');
					}
					if ($value == 'subject_length') { $groupdata['mobile_subject_length'] = $this->input->post('mobile_subject_length');}
					if ($value == 'except_notice') { $groupdata['mobile_except_notice'] = $this->input->post('mobile_except_notice');}
					if ($value == 'except_all_notice') { $groupdata['mobile_except_all_notice'] = $this->input->post('mobile_except_all_notice');}
					if ($value == 'gallery_image_width') { $groupdata['gallery_image_height'] = $this->input->post('gallery_image_height');}
					if ($value == 'mobile_gallery_image_width') { $groupdata['mobile_gallery_image_height'] = $this->input->post('mobile_gallery_image_height');}
				}
				if (element($value, $all)) {
					$alldata[$value] = $this->input->post($value);
					if ($value == 'list_count') { $alldata['mobile_list_count'] = $this->input->post('mobile_list_count');}
					if ($value == 'page_count') { $alldata['mobile_page_count'] = $this->input->post('mobile_page_count');}
					if ($value == 'always_show_write_button') { $alldata['mobile_always_show_write_button'] = $this->input->post('mobile_always_show_write_button');}
					if ($value == 'show_list_from_view') { $alldata['mobile_show_list_from_view'] = $this->input->post('mobile_show_list_from_view');}
					if ($value == 'new_icon_hour') { $alldata['mobile_new_icon_hour'] = $this->input->post('mobile_new_icon_hour');}
					if ($value == 'hot_icon_hit') {
						$alldata['mobile_hot_icon_hit'] = $this->input->post('mobile_hot_icon_hit');
						$alldata['hot_icon_day'] = $this->input->post('hot_icon_day');
						$alldata['mobile_hot_icon_day'] = $this->input->post('mobile_hot_icon_day');
					}
					if ($value == 'subject_length') { $alldata['mobile_subject_length'] = $this->input->post('mobile_subject_length');}
					if ($value == 'except_notice') { $alldata['mobile_except_notice'] = $this->input->post('mobile_except_notice');}
					if ($value == 'except_all_notice') { $alldata['mobile_except_all_notice'] = $this->input->post('mobile_except_all_notice');}
					if ($value == 'gallery_image_width') { $alldata['gallery_image_height'] = $this->input->post('gallery_image_height');}
					if ($value == 'mobile_gallery_image_width') { $alldata['mobile_gallery_image_height'] = $this->input->post('mobile_gallery_image_height');}
				}
			}

			$this->Board_meta_model->save($pid, $metadata);

			$getdata = $this->{$this->modelname}->get_one($pid);
			if ( ! element('brd_id', $getdata))  show_404();
			if ($groupdata) {
				$where = array('bgr_id' => $getdata['bgr_id']);
				$res = $this->Board_model->get($primary_value='' , $select = '', $where);
				foreach ($res as $bkey => $bval) {
					if ($bval['brd_id'] == $getdata['brd_id']) continue;
					$this->Board_meta_model->save($bval['brd_id'], $groupdata);
				}
			}
			if ($alldata) {
				$res = $this->Board_model->get();
				foreach ($res as $bkey => $bval) {
					if ($bval['brd_id'] == $getdata['brd_id']) continue;
					$this->Board_meta_model->save($bval['brd_id'], $alldata);
				}
			}
			$view['view']['alert_message'] = '목록페이지 설정이 저장되었습니다';

		}

		$getdata = $this->{$this->modelname}->get_one($pid);
		if ( ! element('brd_id', $getdata))  show_404();
		$getdata = array_merge($getdata, $this->Board_meta_model->get_all_meta(element('brd_id', $getdata)));
		$view['view']['data'] = $getdata;

		/**
		* primary key 정보를 저장합니다
		*/
		$view['view']['primary_key'] = $primary_key;

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

		/**
		* 어드민 레이아웃을 정의합니다
		*/
		$layoutconfig		= array('layout' => 'layout', 'skin' => 'write_list');
		$view['layout']	= $this->managelayout->admin($layoutconfig, $this->cbconfig->get_device_view_type());
		$this->data			= $view;
		$this->layout		= element('layout_skin_file', element('layout', $view));
		$this->view			= element('view_skin_file', element('layout', $view));


	}

	/**
	* 게시판관리> 글쓰기>게시물열람
	*/
	public function write_post($pid = '')
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_admin_board_boards_write_post';
		$this->load->event($eventname);

		$view = array();
		$view['view'] = array();

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before'] = Events::trigger('before', $eventname);

		/**
		* 프라이머리키에 숫자형이 입력되지 않으면 에러처리합니다
		*/
		if ( ! $pid)  show_404();
		if ( ! is_numeric($pid))  show_404();

		$primary_key = $this->{$this->modelname}->primary_key;

		/**
		* Validation 라이브러리를 가져옵니다
		*/
		$this->load->library('form_validation');

		/**
		* 전송된 데이터의 유효성을 체크합니다
		*/
		$config = array(
			array('field'=>'is_submit', 'label'=>'전송', 'rules'=>'trim|numeric'),
			array('field'=>'use_scrap', 'label'=>'스크랩 기능', 'rules'=>'trim|numeric'),
			array('field'=>'use_post_like', 'label'=>'추천기능', 'rules'=>'trim|numeric'),
			array('field'=>'use_post_dislike', 'label'=>'비추천기능', 'rules'=>'trim|numeric'),
			array('field'=>'use_print', 'label'=>'본문인쇄기능', 'rules'=>'trim|numeric'),
			array('field'=>'use_sns', 'label'=>'SNS 보내기 버튼', 'rules'=>'trim|numeric'),
			array('field'=>'use_mobile_sns', 'label'=>'SNS 보내기 버튼 - 모바일', 'rules'=>'trim|numeric'),
			array('field'=>'use_prev_next_post', 'label'=>'이전글 다음글 버튼', 'rules'=>'trim|numeric'),
			array('field'=>'use_mobile_prev_next_post', 'label'=>'이전글 다음글 버튼 - 모바일', 'rules'=>'trim|numeric'),
			array('field'=>'use_blame', 'label'=>'게시물신고기능', 'rules'=>'trim|numeric'),
			array('field'=>'blame_blind_count', 'label'=>'신고시블라인드', 'rules'=>'trim|required|numeric'),
			array('field'=>'syntax_highlighter', 'label'=>'Code Syntax', 'rules'=>'trim|numeric'),
			array('field'=>'comment_syntax_highlighter', 'label'=>'Code Syntax (모바일)', 'rules'=>'trim|numeric'),
			array('field'=>'use_autoplay', 'label'=>'자동실행', 'rules'=>'trim|numeric'),
			array('field'=>'post_image_width', 'label'=>'이미지 폭 크기', 'rules'=>'trim|numeric'),
			array('field'=>'post_mobile_image_width', 'label'=>'이미지 폭 크기', 'rules'=>'trim|numeric'),
			array('field'=>'show_ip', 'label'=>'IP 보이기', 'rules'=>'trim'),
			array('field'=>'show_mobile_ip', 'label'=>'IP 보이기 (모바일)', 'rules'=>'trim'),
			array('field'=>'content_target_blank', 'label'=>'링크 새창', 'rules'=>'trim|numeric'),
			array('field'=>'mobile_content_target_blank', 'label'=>'링크 새창 (모바일)', 'rules'=>'trim|numeric'),
			array('field'=>'use_auto_url', 'label'=>'본문 안의 URL 자동 링크', 'rules'=>'trim|numeric'),
			array('field'=>'use_mobile_auto_url', 'label'=>'본문 안의 URL 자동 링크 (모바일)', 'rules'=>'trim|numeric'),
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

			$array = array('use_scrap', 'use_post_like', 'use_post_dislike', 'use_print', 'use_sns', 'use_mobile_sns', 'use_prev_next_post', 'use_mobile_prev_next_post', 'use_blame', 'blame_blind_count', 'syntax_highlighter', 'comment_syntax_highlighter', 'use_autoplay', 'post_image_width', 'post_mobile_image_width', 'show_ip', 'show_mobile_ip', 'content_target_blank', 'mobile_content_target_blank', 'use_auto_url', 'use_mobile_auto_url',);

			$metadata = array();
			$groupdata = array();
			$alldata = array();
			$grp = $this->input->post('grp');
			$all = $this->input->post('all');

			foreach ($array as $value) {
				$metadata[$value] = $this->input->post($value);
				if (element($value, $grp)) {
					$groupdata[$value] = $this->input->post($value);
					if ($value == 'use_sns') { $groupdata['use_mobile_sns'] = $this->input->post('use_mobile_sns');}
					if ($value == 'use_prev_next_post') { $groupdata['use_mobile_prev_next_post'] = $this->input->post('use_mobile_prev_next_post');}
					if ($value == 'syntax_highlighter') { $groupdata['comment_syntax_highlighter'] = $this->input->post('comment_syntax_highlighter');}
					if ($value == 'post_image_width') { $groupdata['post_mobile_image_width'] = $this->input->post('post_mobile_image_width');}
					if ($value == 'show_ip') { $groupdata['show_mobile_ip'] = $this->input->post('show_mobile_ip');}
					if ($value == 'content_target_blank') { $groupdata['mobile_content_target_blank'] = $this->input->post('mobile_content_target_blank');}
					if ($value == 'use_auto_url') { $groupdata['use_mobile_auto_url'] = $this->input->post('use_mobile_auto_url');}
				}
				if (element($value, $all)) {
					$alldata[$value] = $this->input->post($value);
					if ($value == 'use_sns') { $alldata['use_mobile_sns'] = $this->input->post('use_mobile_sns');}
					if ($value == 'use_prev_next_post') { $alldata['use_mobile_prev_next_post'] = $this->input->post('use_mobile_prev_next_post');}
					if ($value == 'syntax_highlighter') { $alldata['comment_syntax_highlighter'] = $this->input->post('comment_syntax_highlighter');}
					if ($value == 'post_image_width') { $alldata['post_mobile_image_width'] = $this->input->post('post_mobile_image_width');}
					if ($value == 'show_ip') { $alldata['show_mobile_ip'] = $this->input->post('show_mobile_ip');}
					if ($value == 'content_target_blank') { $alldata['mobile_content_target_blank'] = $this->input->post('mobile_content_target_blank');}
					if ($value == 'use_auto_url') { $alldata['use_mobile_auto_url'] = $this->input->post('use_mobile_auto_url');}
				}
			}

			$this->Board_meta_model->save($pid, $metadata);

			$getdata = $this->{$this->modelname}->get_one($pid);
			if ( ! element('brd_id', $getdata))  show_404();
			if ($groupdata) {
				$where = array('bgr_id' => $getdata['bgr_id']);
				$res = $this->Board_model->get($primary_value='' , $select = '', $where);
				foreach ($res as $bkey => $bval) {
					if ($bval['brd_id'] == $getdata['brd_id']) continue;
					$this->Board_meta_model->save($bval['brd_id'], $groupdata);
				}
			}
			if ($alldata) {
				$res = $this->Board_model->get();
				foreach ($res as $bkey => $bval) {
					if ($bval['brd_id'] == $getdata['brd_id']) continue;
					$this->Board_meta_model->save($bval['brd_id'], $alldata);
				}
			}
			$view['view']['alert_message'] = '게시물열람 설정이 저장되었습니다';

		}

		$getdata = $this->{$this->modelname}->get_one($pid);
		if ( ! element('brd_id', $getdata))  show_404();
		$getdata = array_merge($getdata, $this->Board_meta_model->get_all_meta(element('brd_id', $getdata)));
		$view['view']['data'] = $getdata;

		/**
		* primary key 정보를 저장합니다
		*/
		$view['view']['primary_key'] = $primary_key;

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

		/**
		* 어드민 레이아웃을 정의합니다
		*/
		$layoutconfig		= array('layout' => 'layout', 'skin' => 'write_post');
		$view['layout']	= $this->managelayout->admin($layoutconfig, $this->cbconfig->get_device_view_type());
		$this->data			= $view;
		$this->layout		= element('layout_skin_file', element('layout', $view));
		$this->view			= element('view_skin_file', element('layout', $view));


	}

	/**
	* 게시판관리> 글쓰기>게시물작성
	*/
	public function write_write($pid = '')
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_admin_board_boards_write_write';
		$this->load->event($eventname);

		$view = array();
		$view['view'] = array();

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before'] = Events::trigger('before', $eventname);

		/**
		* 프라이머리키에 숫자형이 입력되지 않으면 에러처리합니다
		*/
		if ( ! $pid)  show_404();
		if ( ! is_numeric($pid))  show_404();

		$primary_key = $this->{$this->modelname}->primary_key;


		/**
		* Validation 라이브러리를 가져옵니다
		*/
		$this->load->library('form_validation');

		/**
		* 전송된 데이터의 유효성을 체크합니다
		*/
		$config = array(
			array('field'=>'is_submit', 'label'=>'전송', 'rules'=>'trim|numeric'),
			array('field'=>'post_default_title', 'label'=>'글쓰기시 기본 제목', 'rules'=>'trim'),
			array('field'=>'mobile_post_default_title', 'label'=>'글쓰기시 기본 제목 - 모바일', 'rules'=>'trim'),
			array('field'=>'post_default_content', 'label'=>'글쓰기시 기본 내용', 'rules'=>'trim'),
			array('field'=>'mobile_post_default_content', 'label'=>'글쓰기시 기본 내용 - 모바일', 'rules'=>'trim'),
			array('field'=>'use_post_dhtml', 'label'=>'본문 에디터 사용', 'rules'=>'trim|numeric'),
			array('field'=>'use_mobile_post_dhtml', 'label'=>'본문 에디터 사용 - 모바일', 'rules'=>'trim|numeric'),
			array('field'=>'save_external_image', 'label'=>'외부 이미지 가져오기', 'rules'=>'trim|numeric'),
			array('field'=>'post_min_length', 'label'=>'최소 글수 제한', 'rules'=>'trim|required|numeric'),
			array('field'=>'post_max_length', 'label'=>'최대 글수 제한', 'rules'=>'trim|required|numeric'),
			array('field'=>'use_post_secret', 'label'=>'비밀글 사용', 'rules'=>'trim'),
			array('field'=>'use_post_secret_selected', 'label'=>'비밀글 기본 선택', 'rules'=>'trim|numeric'),
			array('field'=>'use_post_receive_email', 'label'=>'답변메일받기기능', 'rules'=>'trim|numeric'),
			array('field'=>'link_num', 'label'=>'링크 필드 개수', 'rules'=>'trim|required|numeric'),
			array('field'=>'mobile_link_num', 'label'=>'링크 필드 개수 - 모바일', 'rules'=>'trim|required|numeric'),
			array('field'=>'use_google_map', 'label'=>'구글 지도 사용', 'rules'=>'trim|numeric'),
			array('field'=>'use_upload_file', 'label'=>'첨부파일 기능', 'rules'=>'trim|numeric'),
			array('field'=>'upload_file_num', 'label'=>'첨부파일 개수 제한', 'rules'=>'trim|required|numeric'),
			array('field'=>'mobile_upload_file_num', 'label'=>'첨부파일 개수 제한 - 모바일', 'rules'=>'trim|required|numeric'),
			array('field'=>'upload_file_max_size', 'label'=>'첨부파일 용량제한', 'rules'=>'trim|required|numeric'),
			array('field'=>'upload_file_extension', 'label'=>'첨부파일 확장자', 'rules'=>'trim'),
			array('field'=>'comment_to_download', 'label'=>'다운로드 제한 (코멘트 필수)', 'rules'=>'trim|numeric'),
			array('field'=>'like_to_download', 'label'=>'다운로드 제한 (추천 필수)', 'rules'=>'trim|numeric'),
			array('field'=>'write_possible_days', 'label'=>'글쓰기 기간제한', 'rules'=>'trim|numeric'),
			array('field'=>'use_only_one_post', 'label'=>'글 한개만 작성가능', 'rules'=>'trim|numeric'),
		);
		$this->form_validation->set_rules($config);


		/**
		* 유효성 검사를 하지 않는 경우, 또는 유효성 검사에 실패한 경우입니다. 즉 글쓰기나 수정 페이지를 보고 있는 경우입니다
		*/
		if ($this->form_validation->run() == FALSE) {

			// 이벤트가 존재하면 실행합니다
			$view['view']['event']['formrunfalse'] = Events::trigger('formrunfalse', $eventname);

			$upload_max_filesize = ini_get("upload_max_filesize");
			if ( ! preg_match("/([m|M])$/", $upload_max_filesize)) {
				$upload_max_filesize = (int)($upload_max_filesize / 1048576) . 'M';
			}
			$view['view']['upload_max_filesize'] = $upload_max_filesize;

		} else {
			/**
			* 유효성 검사를 통과한 경우입니다. 즉 데이터의 insert 나 update 의 process 처리가 필요한 상황입니다
			*/
		
			// 이벤트가 존재하면 실행합니다
			$view['view']['event']['formruntrue'] = Events::trigger('formruntrue', $eventname);

			$array = array('post_default_title', 'mobile_post_default_title', 'post_default_content', 'mobile_post_default_content', 'use_post_dhtml', 'use_mobile_post_dhtml', 'save_external_image', 'post_min_length', 'post_max_length', 'use_post_secret', 'use_post_secret_selected', 'use_post_receive_email', 'link_num', 'mobile_link_num', 'use_google_map', 'use_upload_file', 'upload_file_num', 'mobile_upload_file_num', 'upload_file_max_size', 'upload_file_extension', 'comment_to_download', 'like_to_download', 'write_possible_days', 'use_only_one_post');

			$metadata = array();
			$groupdata = array();
			$alldata = array();
			$grp = $this->input->post('grp');
			$all = $this->input->post('all');

			foreach ($array as $value) {
				$metadata[$value] = $this->input->post($value);
				if (element($value, $grp)) {
					$groupdata[$value] = $this->input->post($value);
					if ($value == 'use_post_dhtml') { $groupdata['use_mobile_post_dhtml'] = $this->input->post('use_mobile_post_dhtml');}
					if ($value == 'link_num') { $groupdata['mobile_link_num'] = $this->input->post('mobile_link_num');}
					if ($value == 'upload_file_num') { $groupdata['mobile_upload_file_num'] = $this->input->post('mobile_upload_file_num');}
				}
				if (element($value, $all)) {
					$alldata[$value] = $this->input->post($value);
					if ($value == 'use_post_dhtml') { $alldata['use_mobile_post_dhtml'] = $this->input->post('use_mobile_post_dhtml');}
					if ($value == 'link_num') { $alldata['mobile_link_num'] = $this->input->post('mobile_link_num');}
					if ($value == 'upload_file_num') { $alldata['mobile_upload_file_num'] = $this->input->post('mobile_upload_file_num');}
				}
			}

			$this->Board_meta_model->save($pid, $metadata);

			$getdata = $this->{$this->modelname}->get_one($pid);
			if ( ! element('brd_id', $getdata))  show_404();
			if ($groupdata) {
				$where = array('bgr_id' => $getdata['bgr_id']);
				$res = $this->Board_model->get($primary_value='' , $select = '', $where);
				foreach ($res as $bkey => $bval) {
					if ($bval['brd_id'] == $getdata['brd_id']) continue;
					$this->Board_meta_model->save($bval['brd_id'], $groupdata);
				}
			}
			if ($alldata) {
				$res = $this->Board_model->get();
				foreach ($res as $bkey => $bval) {
					if ($bval['brd_id'] == $getdata['brd_id']) continue;
					$this->Board_meta_model->save($bval['brd_id'], $alldata);
				}
			}
			$view['view']['alert_message'] = '게시물작성 설정이 저장되었습니다';
		}

		$getdata = $this->{$this->modelname}->get_one($pid);
		if ( ! element('brd_id', $getdata))  show_404();
		$getdata = array_merge($getdata, $this->Board_meta_model->get_all_meta(element('brd_id', $getdata)));
		$view['view']['data'] = $getdata;

		/**
		* primary key 정보를 저장합니다
		*/
		$view['view']['primary_key'] = $primary_key;

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

		/**
		* 어드민 레이아웃을 정의합니다
		*/
		$layoutconfig		= array('layout' => 'layout', 'skin' => 'write_write');
		$view['layout']	= $this->managelayout->admin($layoutconfig, $this->cbconfig->get_device_view_type());
		$this->data			= $view;
		$this->layout		= element('layout_skin_file', element('layout', $view));
		$this->view			= element('view_skin_file', element('layout', $view));


	}

	/**
	* 게시판관리> 글쓰기>카테고리
	*/
	public function write_category($pid = '')
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_admin_board_boards_write_category';
		$this->load->event($eventname);

		$view = array();
		$view['view'] = array();

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before'] = Events::trigger('before', $eventname);

		/**
		* 프라이머리키에 숫자형이 입력되지 않으면 에러처리합니다
		*/
		if ( ! $pid)  show_404();
		if ( ! is_numeric($pid))  show_404();

		$primary_key = $this->{$this->modelname}->primary_key;
		/**
		* Validation 라이브러리를 가져옵니다
		*/
		$this->load->library('form_validation');

		/**
		* 전송된 데이터의 유효성을 체크합니다
		*/
		if ($this->input->post('type') == 'add') {
			$config = array(
				array('field'=>'bca_parent', 'label'=>'상위카테고리', 'rules'=>'trim'),
				array('field'=>'bca_value', 'label'=>'카테고리명', 'rules'=>'trim|required'),
				array('field'=>'bca_order', 'label'=>'정렬순서', 'rules'=>'trim|numeric|is_natural'),
			);
		} else {
			$config = array(
				array('field'=>'bca_id', 'label'=>'카테고리아이디', 'rules'=>'trim|required'),
				array('field'=>'bca_value', 'label'=>'카테고리명', 'rules'=>'trim|required'),
				array('field'=>'bca_order', 'label'=>'정렬순서', 'rules'=>'trim|numeric|is_natural'),
			);
		}
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

			if ($this->input->post('type') == 'add') {
				$bca_key = $this->Board_category_model->next_key($this->input->post('bca_parent'), $pid);

				$insertdata = array();
				$insertdata['brd_id'] = $pid;
				$insertdata['bca_key'] = $bca_key;
				$insertdata['bca_value'] = $this->input->post('bca_value');
				$insertdata['bca_parent'] = $this->input->post('bca_parent');
				$insertdata['bca_order'] = $this->input->post('bca_order');

				$this->Board_category_model->insert($insertdata);
				$this->cache->delete('category-' . $pid);
				$this->cache->delete('category-all-' . $pid);

				$view['view']['alert_message'] = '카테고리 설정이 저장되었습니다';
				redirect(admin_url('board/boards/write_category/' . $pid), 'refresh');
			}
			if ($this->input->post('type') == 'modify') {

				$updatedata = array();
				$updatedata['bca_value'] = $this->input->post('bca_value');
				$updatedata['bca_order'] = $this->input->post('bca_order');

				$this->Board_category_model->update($this->input->post('bca_id'), $updatedata);
				$this->cache->delete('category-' . $pid);
				$this->cache->delete('category-all-' . $pid);

				$view['view']['alert_message'] = '카테고리 정보가 수정되었습니다';
				redirect(admin_url('board/boards/write_category/' . $pid), 'refresh');

			}

		}

		$getdata = $this->Board_category_model->get_all_category($pid);
		$view['view']['data'] = $getdata;
		$view['view']['data']['brd_id'] = $pid;

		/**
		* primary key 정보를 저장합니다
		*/
		$view['view']['primary_key'] = $primary_key;

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

		/**
		* 어드민 레이아웃을 정의합니다
		*/
		$layoutconfig		= array('layout' => 'layout', 'skin' => 'write_category');
		$view['layout']	= $this->managelayout->admin($layoutconfig, $this->cbconfig->get_device_view_type());
		$this->data			= $view;
		$this->layout		= element('layout_skin_file', element('layout', $view));
		$this->view			= element('view_skin_file', element('layout', $view));


	}

	/**
	* 게시판관리> 글쓰기>카테고리 삭제
	*/
	public function write_category_delete($pid='', $bca_id='')
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_admin_board_boards_write_category_delete';
		$this->load->event($eventname);

		$view = array();
		$view['view'] = array();

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before'] = Events::trigger('before', $eventname);

		/**
		* 프라이머리키에 숫자형이 입력되지 않으면 에러처리합니다
		*/
		if ( ! $pid)  show_404();
		if ( ! is_numeric($pid))  show_404();
		if ( ! is_numeric($bca_id) OR $bca_id < 1) show_404();

		$this->Board_category_model->delete($bca_id);
		$this->cache->delete('category-' . $pid);
		$this->cache->delete('category-all-' . $pid);

		/**
		* 삭제가 끝난 후 목록페이지로 이동합니다
		*/
		$this->session->set_flashdata('message', '정상적으로 삭제되었습니다');
		$param =& $this->querystring;
		$redirecturl = admin_url('board/boards/write_category/' . $pid);
		redirect($redirecturl);

	}

	/**
	* 게시판관리> 글쓰기>댓글기능
	*/
	public function write_comment($pid = '')
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_admin_board_boards_write_comment';
		$this->load->event($eventname);

		$view = array();
		$view['view'] = array();

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before'] = Events::trigger('before', $eventname);

		/**
		* 프라이머리키에 숫자형이 입력되지 않으면 에러처리합니다
		*/
		if ( ! $pid)  show_404();
		if ( ! is_numeric($pid))  show_404();

		$primary_key = $this->{$this->modelname}->primary_key;



		/**
		* Validation 라이브러리를 가져옵니다
		*/
		$this->load->library('form_validation');

		/**
		* 전송된 데이터의 유효성을 체크합니다
		*/
		$config = array(
			array('field'=>'is_submit', 'label'=>'전송', 'rules'=>'trim|numeric'),
			array('field'=>'comment_count', 'label'=>'댓글 목록수', 'rules'=>'trim|required|numeric'),
			array('field'=>'mobile_comment_count', 'label'=>'모바일용 댓글 목록수', 'rules'=>'trim|required|numeric'),
			array('field'=>'comment_page_count', 'label'=>'댓글 페이징', 'rules'=>'trim|required|numeric'),
			array('field'=>'mobile_comment_page_count', 'label'=>'모바일용 댓글 페이징', 'rules'=>'trim|required|numeric'),
			array('field'=>'use_comment_like', 'label'=>'댓글 추천기능', 'rules'=>'trim|numeric'),
			array('field'=>'use_comment_dislike', 'label'=>'댓글 비추천 기능', 'rules'=>'trim|numeric'),
			array('field'=>'always_show_comment_textarea', 'label'=>'댓글 입력창 항상 출력', 'rules'=>'trim|numeric'),
			array('field'=>'mobile_always_show_comment_textarea', 'label'=>'댓글 입력창 항상 출력 - 모바일', 'rules'=>'trim|numeric'),
			array('field'=>'comment_default_content', 'label'=>'댓글 기본 내용', 'rules'=>'trim'),
			array('field'=>'mobile_comment_default_content', 'label'=>'댓글 기본 내용 - 모바일', 'rules'=>'trim'),
			array('field'=>'comment_min_length', 'label'=>'최소 댓글 글수 제한', 'rules'=>'trim|required|numeric'),
			array('field'=>'comment_max_length', 'label'=>'최대 댓글 글수 제한', 'rules'=>'trim|required|numeric'),
			array('field'=>'use_comment_secret', 'label'=>'댓글 비밀글', 'rules'=>'trim'),
			array('field'=>'use_comment_secret_selected', 'label'=>'댓글 비밀글 기본 선택', 'rules'=>'trim|numeric'),
			array('field'=>'show_comment_ip', 'label'=>'댓글작성자 IP 보이기', 'rules'=>'trim'),
			array('field'=>'show_mobile_comment_ip', 'label'=>'댓글작성자 IP 보이기', 'rules'=>'trim'),
			array('field'=>'notice_comment_block', 'label'=>'공지글에 댓글 금지', 'rules'=>'trim|numeric'),
			array('field'=>'comment_order', 'label'=>'댓글 정렬 순서', 'rules'=>'trim|required'),
			array('field'=>'use_comment_blame', 'label'=>'댓글신고기능', 'rules'=>'trim|numeric'),
			array('field'=>'comment_blame_blind_count', 'label'=>'댓글신고시블라인드', 'rules'=>'trim|required|numeric'),
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

			$array = array('comment_count', 'mobile_comment_count', 'comment_page_count', 'mobile_comment_page_count', 'use_comment_like', 'use_comment_dislike', 'always_show_comment_textarea', 'mobile_always_show_comment_textarea', 'comment_default_content', 'mobile_comment_default_content', 'comment_min_length', 'comment_max_length', 'use_comment_secret', 'use_comment_secret_selected', 'show_comment_ip', 'show_mobile_comment_ip', 'notice_comment_block', 'comment_order', 'use_comment_blame', 'comment_blame_blind_count');

			$metadata = array();
			$groupdata = array();
			$alldata = array();
			$grp = $this->input->post('grp');
			$all = $this->input->post('all');

			foreach ($array as $value) {
				$metadata[$value] = $this->input->post($value);
				if (element($value, $grp)) {
					$groupdata[$value] = $this->input->post($value);
					if ($value == 'always_show_comment_textarea') { $groupdata['mobile_always_show_comment_textarea'] = $this->input->post('mobile_always_show_comment_textarea');}
					if ($value == 'comment_default_content') { $groupdata['mobile_comment_default_content'] = $this->input->post('mobile_comment_default_content');}
					if ($value == 'show_comment_ip') { $groupdata['show_mobile_comment_ip'] = $this->input->post('show_mobile_comment_ip');}
				}
				if (element($value, $all)) {
					$alldata[$value] = $this->input->post($value);
					if ($value == 'always_show_comment_textarea') { $alldata['mobile_always_show_comment_textarea'] = $this->input->post('mobile_always_show_comment_textarea');}
					if ($value == 'comment_default_content') { $alldata['mobile_comment_default_content'] = $this->input->post('mobile_comment_default_content');}
					if ($value == 'show_comment_ip') { $alldata['show_mobile_comment_ip'] = $this->input->post('show_mobile_comment_ip');}
				}
			}

			$this->Board_meta_model->save($pid, $metadata);

			$getdata = $this->{$this->modelname}->get_one($pid);
			if ( ! element('brd_id', $getdata))  show_404();
			if ($groupdata) {
				$where = array('bgr_id' => $getdata['bgr_id']);
				$res = $this->Board_model->get($primary_value='' , $select = '', $where);
				foreach ($res as $bkey => $bval) {
					if ($bval['brd_id'] == $getdata['brd_id']) continue;
					$this->Board_meta_model->save($bval['brd_id'], $groupdata);
				}
			}
			if ($alldata) {
				$res = $this->Board_model->get();
				foreach ($res as $bkey => $bval) {
					if ($bval['brd_id'] == $getdata['brd_id']) continue;
					$this->Board_meta_model->save($bval['brd_id'], $alldata);
				}
			}
			$view['view']['alert_message'] = '댓글기능 설정이 저장되었습니다';

		}

		$getdata = $this->{$this->modelname}->get_one($pid);
		if ( ! element('brd_id', $getdata))  show_404();
		$getdata = array_merge($getdata, $this->Board_meta_model->get_all_meta(element('brd_id', $getdata)));
		$view['view']['data'] = $getdata;

		/**
		* primary key 정보를 저장합니다
		*/
		$view['view']['primary_key'] = $primary_key;

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

		/**
		* 어드민 레이아웃을 정의합니다
		*/
		$layoutconfig		= array('layout' => 'layout', 'skin' => 'write_comment');
		$view['layout']	= $this->managelayout->admin($layoutconfig, $this->cbconfig->get_device_view_type());
		$this->data			= $view;
		$this->layout		= element('layout_skin_file', element('layout', $view));
		$this->view			= element('view_skin_file', element('layout', $view));


	}

	/**
	* 게시판관리> 글쓰기>일반기능
	*/
	public function write_general($pid = '')
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_admin_board_boards_write_general';
		$this->load->event($eventname);

		$view = array();
		$view['view'] = array();

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before'] = Events::trigger('before', $eventname);

		/**
		* 프라이머리키에 숫자형이 입력되지 않으면 에러처리합니다
		*/
		if ( ! $pid)  show_404();
		if ( ! is_numeric($pid))  show_404();

		$primary_key = $this->{$this->modelname}->primary_key;

		/**
		* Validation 라이브러리를 가져옵니다
		*/
		$this->load->library('form_validation');

		/**
		* 전송된 데이터의 유효성을 체크합니다
		*/
		$config = array(
			array('field'=>'is_submit', 'label'=>'전송', 'rules'=>'trim|numeric'),
			array('field'=>'protect_post_day', 'label'=>'원글 수정 및 삭제 금지 기간', 'rules'=>'trim|required|numeric|is_natural'),
			array('field'=>'protect_comment_day', 'label'=>'댓글 수정 및 삭제 금지 기간', 'rules'=>'trim|required|numeric|is_natural'),
			array('field'=>'protect_comment_num', 'label'=>'원글 수정 및 삭제 댓글수', 'rules'=>'trim|required|numeric|is_natural'),
			array('field'=>'use_sideview', 'label'=>'글쓴이 사이드뷰', 'rules'=>'trim|numeric'),
			array('field'=>'use_mobile_sideview', 'label'=>'글쓴이 사이드뷰', 'rules'=>'trim|numeric'),
			array('field'=>'use_category', 'label'=>'카테고리 기능', 'rules'=>'trim|numeric'),
			array('field'=>'category_display_style', 'label'=>'카테고리 목록모양', 'rules'=>'trim'),
			array('field'=>'mobile_category_display_style', 'label'=>'카테고리 목록모양 (모바일)', 'rules'=>'trim'),
			array('field'=>'use_naver_syndi', 'label'=>'네이버 신디케이션 기능', 'rules'=>'trim|numeric'),
			array('field'=>'use_personal', 'label'=>'1:1 게시판', 'rules'=>'trim|numeric'),
			array('field'=>'use_tempsave', 'label'=>'임시저장기능', 'rules'=>'trim|numeric'),
			array('field'=>'use_post_delete_log', 'label'=>'삭제글 남김(원글)', 'rules'=>'trim|numeric'),
			array('field'=>'use_comment_delete_log', 'label'=>'삭제글 남김(댓글)', 'rules'=>'trim|numeric'),
			array('field'=>'list_date_style', 'label'=>'목록 날짜 표시 방법', 'rules'=>'trim'),
			array('field'=>'list_date_style_manual', 'label'=>'목록 날짜 표시 방법 - 사용자정의', 'rules'=>'trim'),
			array('field'=>'view_date_style', 'label'=>'본문 날짜 표시 방법', 'rules'=>'trim'),
			array('field'=>'view_date_style_manual', 'label'=>'본문 날짜 표시 방법 - 사용자정의', 'rules'=>'trim'),
			array('field'=>'comment_date_style', 'label'=>'댓글 날짜 표시 방법', 'rules'=>'trim'),
			array('field'=>'comment_date_style_manual', 'label'=>'댓글 날짜 표시 방법 - 사용자정의', 'rules'=>'trim'),
			array('field'=>'mobile_list_date_style', 'label'=>'목록 날짜 표시 방법 (모바일)', 'rules'=>'trim'),
			array('field'=>'mobile_list_date_style_manual', 'label'=>'목록 날짜 표시 방법 - 사용자정의 (모바일)', 'rules'=>'trim'),
			array('field'=>'mobile_view_date_style', 'label'=>'본문 날짜 표시 방법 (모바일)', 'rules'=>'trim'),
			array('field'=>'mobile_view_date_style_manual', 'label'=>'본문 날짜 표시 방법 - 사용자정의 (모바일)', 'rules'=>'trim'),
			array('field'=>'mobile_comment_date_style', 'label'=>'댓글 날짜 표시 방법 (모바일)', 'rules'=>'trim'),
			array('field'=>'mobile_comment_date_style_manual', 'label'=>'댓글 날짜 표시 방법 - 사용자정의 (모바일)', 'rules'=>'trim'),
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

			$array = array('protect_post_day', 'protect_comment_day', 'protect_comment_num', 'use_sideview', 'use_mobile_sideview', 'use_category', 'category_display_style', 'mobile_category_display_style', 'use_naver_syndi', 'use_personal', 'use_tempsave', 'use_post_delete_log', 'use_comment_delete_log', 'list_date_style', 'list_date_style_manual', 'view_date_style', 'view_date_style_manual', 'comment_date_style', 'comment_date_style_manual', 'mobile_list_date_style', 'mobile_list_date_style_manual', 'mobile_view_date_style', 'mobile_view_date_style_manual', 'mobile_comment_date_style', 'mobile_comment_date_style_manual');

			$metadata = array();
			$groupdata = array();
			$alldata = array();
			$grp = $this->input->post('grp');
			$all = $this->input->post('all');

			foreach ($array as $value) {
				$metadata[$value] = $this->input->post($value);
				if (element($value, $grp)) {
					$groupdata[$value] = $this->input->post($value);
					if ($value == 'use_sideview') { $groupdata['use_mobile_sideview'] = $this->input->post('use_mobile_sideview');}
					if ($value == 'list_date_style') { $groupdata['list_date_style_manual'] = $this->input->post('list_date_style_manual');}
					if ($value == 'view_date_style') { $groupdata['view_date_style_manual'] = $this->input->post('view_date_style_manual');}
					if ($value == 'comment_date_style') { $groupdata['comment_date_style_manual'] = $this->input->post('comment_date_style_manual');}
					if ($value == 'mobile_list_date_style') { $groupdata['mobile_list_date_style_manual'] = $this->input->post('mobile_list_date_style_manual');}
					if ($value == 'mobile_view_date_style') { $groupdata['mobile_view_date_style_manual'] = $this->input->post('mobile_view_date_style_manual');}
					if ($value == 'mobile_comment_date_style') { $groupdata['mobile_comment_date_style_manual'] = $this->input->post('mobile_comment_date_style_manual');}
				}
				if (element($value, $all)) {
					$alldata[$value] = $this->input->post($value);
					if ($value == 'use_sideview') { $alldata['use_mobile_sideview'] = $this->input->post('use_mobile_sideview');}
					if ($value == 'list_date_style') { $alldata['list_date_style_manual'] = $this->input->post('list_date_style_manual');}
					if ($value == 'view_date_style') { $alldata['view_date_style_manual'] = $this->input->post('view_date_style_manual');}
					if ($value == 'comment_date_style') { $alldata['comment_date_style_manual'] = $this->input->post('comment_date_style_manual');}
					if ($value == 'mobile_list_date_style') { $alldata['mobile_list_date_style_manual'] = $this->input->post('mobile_list_date_style_manual');}
					if ($value == 'mobile_view_date_style') { $alldata['mobile_view_date_style_manual'] = $this->input->post('mobile_view_date_style_manual');}
					if ($value == 'mobile_comment_date_style') { $alldata['mobile_comment_date_style_manual'] = $this->input->post('mobile_comment_date_style_manual');}
				}
			}

			$this->Board_meta_model->save($pid, $metadata);

			$getdata = $this->{$this->modelname}->get_one($pid);
			if ( ! element('brd_id', $getdata))  show_404();
			if ($groupdata) {
				$where = array('bgr_id' => $getdata['bgr_id']);
				$res = $this->Board_model->get($primary_value='' , $select = '', $where);
				foreach ($res as $bkey => $bval) {
					if ($bval['brd_id'] == $getdata['brd_id']) continue;
					$this->Board_meta_model->save($bval['brd_id'], $groupdata);
				}
			}
			if ($alldata) {
				$res = $this->Board_model->get();
				foreach ($res as $bkey => $bval) {
					if ($bval['brd_id'] == $getdata['brd_id']) continue;
					$this->Board_meta_model->save($bval['brd_id'], $alldata);
				}
			}
			$view['view']['alert_message'] = '일반기능 설정이 저장되었습니다';
		}

		$getdata = $this->{$this->modelname}->get_one($pid);
		if ( ! element('brd_id', $getdata))  show_404();
		$getdata = array_merge($getdata, $this->Board_meta_model->get_all_meta(element('brd_id', $getdata)));
		$view['view']['data'] = $getdata;

		/**
		* primary key 정보를 저장합니다
		*/
		$view['view']['primary_key'] = $primary_key;

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

		/**
		* 어드민 레이아웃을 정의합니다
		*/
		$layoutconfig		= array('layout' => 'layout', 'skin' => 'write_general');
		$view['layout']	= $this->managelayout->admin($layoutconfig, $this->cbconfig->get_device_view_type());
		$this->data			= $view;
		$this->layout		= element('layout_skin_file', element('layout', $view));
		$this->view			= element('view_skin_file', element('layout', $view));


	}

	/**
	* 게시판관리> 글쓰기>포인트기능
	*/
	public function write_point($pid = '')
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_admin_board_boards_write_point';
		$this->load->event($eventname);

		$view = array();
		$view['view'] = array();

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before'] = Events::trigger('before', $eventname);

		/**
		* 프라이머리키에 숫자형이 입력되지 않으면 에러처리합니다
		*/
		if ( ! $pid)  show_404();
		if ( ! is_numeric($pid))  show_404();

		$primary_key = $this->{$this->modelname}->primary_key;


		/**
		* Validation 라이브러리를 가져옵니다
		*/
		$this->load->library('form_validation');

		/**
		* 전송된 데이터의 유효성을 체크합니다
		*/
		$config = array(
			array('field'=>'is_submit', 'label'=>'전송', 'rules'=>'trim|numeric'),
			array('field'=>'use_point', 'label'=>'포인트기능사용', 'rules'=>'trim|numeric'),
			array('field'=>'use_point_info', 'label'=>'포인트안내사용', 'rules'=>'trim|numeric'),
			array('field'=>'point_write', 'label'=>'원글작성포인트', 'rules'=>'trim|required|numeric'),
			array('field'=>'point_comment', 'label'=>'댓글작성포인트', 'rules'=>'trim|required|numeric'),
			array('field'=>'point_post_delete', 'label'=>'작성자본인이원글삭제시포인트', 'rules'=>'trim|required|numeric'),
			array('field'=>'point_admin_post_delete', 'label'=>'관리자가원글삭제시포인트', 'rules'=>'trim|required|numeric'),
			array('field'=>'point_comment_delete', 'label'=>'작성자본인이댓글삭제시포인트', 'rules'=>'trim|required|numeric'),
			array('field'=>'point_admin_comment_delete', 'label'=>'관리자가댓글삭제시포인트', 'rules'=>'trim|required|numeric'),
			array('field'=>'point_fileupload', 'label'=>'파일업로드포인트', 'rules'=>'trim|required|numeric'),
			array('field'=>'point_filedownload', 'label'=>'파일다운로드포인트', 'rules'=>'trim|required|numeric'),
			array('field'=>'point_filedownload_uploader', 'label'=>'파일다운로드시업로더에게포인트', 'rules'=>'trim|required|numeric'),
			array('field'=>'point_read', 'label'=>'게시글조회포인트', 'rules'=>'trim|required|numeric'),
			array('field'=>'point_post_like', 'label'=>'원글추천포인트', 'rules'=>'trim|required|numeric'),
			array('field'=>'point_post_dislike', 'label'=>'원글비추천포인트', 'rules'=>'trim|required|numeric'),
			array('field'=>'point_post_liked', 'label'=>'원글추천받음포인트', 'rules'=>'trim|required|numeric'),
			array('field'=>'point_post_disliked', 'label'=>'원글비추천받음포인트', 'rules'=>'trim|required|numeric'),
			array('field'=>'point_comment_like', 'label'=>'댓글추천포인트', 'rules'=>'trim|required|numeric'),
			array('field'=>'point_comment_dislike', 'label'=>'댓글비추천포인트', 'rules'=>'trim|required|numeric'),
			array('field'=>'point_comment_liked', 'label'=>'댓글추천받음포인트', 'rules'=>'trim|required|numeric'),
			array('field'=>'point_comment_disliked', 'label'=>'댓글비추천받음포인트', 'rules'=>'trim|required|numeric'),
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

			$array = array('use_point', 'use_point_info', 'point_write',  'point_comment',  'point_post_delete', 'point_admin_post_delete', 'point_comment_delete', 'point_admin_comment_delete', 'point_fileupload',  'point_filedownload',  'point_filedownload_uploader',  'point_read', 'point_post_like', 'point_post_dislike', 'point_post_liked', 'point_post_disliked', 'point_comment_like', 'point_comment_dislike', 'point_comment_liked', 'point_comment_disliked',);

			$metadata = array();
			$groupdata = array();
			$alldata = array();
			$grp = $this->input->post('grp');
			$all = $this->input->post('all');

			foreach ($array as $value) {
				$metadata[$value] = $this->input->post($value);
				if (element($value, $grp)) {
					$groupdata[$value] = $this->input->post($value);
				}
				if (element($value, $all)) {
					$alldata[$value] = $this->input->post($value);
				}
			}

			$this->Board_meta_model->save($pid, $metadata);

			$getdata = $this->{$this->modelname}->get_one($pid);
			if ( ! element('brd_id', $getdata))  show_404();
			if ($groupdata) {
				$where = array('bgr_id' => $getdata['bgr_id']);
				$res = $this->Board_model->get($primary_value='' , $select = '', $where);
				foreach ($res as $bkey => $bval) {
					if ($bval['brd_id'] == $getdata['brd_id']) continue;
					$this->Board_meta_model->save($bval['brd_id'], $groupdata);
				}
			}
			if ($alldata) {
				$res = $this->Board_model->get();
				foreach ($res as $bkey => $bval) {
					if ($bval['brd_id'] == $getdata['brd_id']) continue;
					$this->Board_meta_model->save($bval['brd_id'], $alldata);
				}
			}
			$view['view']['alert_message'] = '포인트기능 설정이 저장되었습니다';
		}

		$getdata = $this->{$this->modelname}->get_one($pid);
		if ( ! element('brd_id', $getdata))  show_404();
		$getdata = array_merge($getdata, $this->Board_meta_model->get_all_meta(element('brd_id', $getdata)));
		$view['view']['data'] = $getdata;

		/**
		* primary key 정보를 저장합니다
		*/
		$view['view']['primary_key'] = $primary_key;

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

		/**
		* 어드민 레이아웃을 정의합니다
		*/
		$layoutconfig		= array('layout' => 'layout', 'skin' => 'write_point');
		$view['layout']	= $this->managelayout->admin($layoutconfig, $this->cbconfig->get_device_view_type());
		$this->data			= $view;
		$this->layout		= element('layout_skin_file', element('layout', $view));
		$this->view			= element('view_skin_file', element('layout', $view));

	}

	/**
	* 게시판관리> 글쓰기>메일/쪽지/문자
	*/
	public function write_alarm($pid = '')
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_admin_board_boards_write_alarm';
		$this->load->event($eventname);

		$view = array();
		$view['view'] = array();

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before'] = Events::trigger('before', $eventname);

		/**
		* 프라이머리키에 숫자형이 입력되지 않으면 에러처리합니다
		*/
		if ( ! $pid)  show_404();
		if ( ! is_numeric($pid))  show_404();

		$primary_key = $this->{$this->modelname}->primary_key;


		/**
		* Validation 라이브러리를 가져옵니다
		*/
		$this->load->library('form_validation');

		/**
		* 전송된 데이터의 유효성을 체크합니다
		*/
		$config = array(
			array('field'=>'is_submit', 'label'=>'전송', 'rules'=>'trim|numeric'),
			array('field'=>'send_email_post_super_admin', 'label'=>'메일사용(원글작성시) - 최고관리자에게', 'rules'=>'trim|numeric'),
			array('field'=>'send_email_post_group_admin', 'label'=>'메일사용(원글작성시) - 그룹관리자에게', 'rules'=>'trim|numeric'),
			array('field'=>'send_email_post_board_admin', 'label'=>'메일사용(원글작성시) - 게시판관리자에게', 'rules'=>'trim|numeric'),
			array('field'=>'send_email_post_writer', 'label'=>'메일사용(원글작성시) - 게시글작성자에게', 'rules'=>'trim|numeric'),
			array('field'=>'send_email_comment_super_admin', 'label'=>'메일사용(댓글작성시) - 최고관리자에게', 'rules'=>'trim|numeric'),
			array('field'=>'send_email_comment_group_admin', 'label'=>'메일사용(댓글작성시) - 그룹관리자에게', 'rules'=>'trim|numeric'),
			array('field'=>'send_email_comment_board_admin', 'label'=>'메일사용(댓글작성시) - 게시판관리자에게', 'rules'=>'trim|numeric'),
			array('field'=>'send_email_comment_post_writer', 'label'=>'메일사용(댓글작성시) - 원글작성자에게', 'rules'=>'trim|numeric'),
			array('field'=>'send_email_comment_comment_writer', 'label'=>'메일사용(댓글작성시) - 해당댓글작성자에게', 'rules'=>'trim|numeric'),
			array('field'=>'send_email_blame_super_admin', 'label'=>'메일사용(신고발생시) - 최고관리자에게', 'rules'=>'trim|numeric'),
			array('field'=>'send_email_blame_group_admin', 'label'=>'메일사용(신고발생시) - 그룹관리자에게', 'rules'=>'trim|numeric'),
			array('field'=>'send_email_blame_board_admin', 'label'=>'메일사용(신고발생시) - 게시판관리자에게', 'rules'=>'trim|numeric'),
			array('field'=>'send_email_blame_post_writer', 'label'=>'메일사용(신고발생시) - 원글작성자에게', 'rules'=>'trim|numeric'),
			array('field'=>'send_email_comment_blame_super_admin', 'label'=>'메일사용(댓글신고발생시) - 최고관리자에게', 'rules'=>'trim|numeric'),
			array('field'=>'send_email_comment_blame_group_admin', 'label'=>'메일사용(댓글신고발생시) - 그룹관리자에게', 'rules'=>'trim|numeric'),
			array('field'=>'send_email_comment_blame_board_admin', 'label'=>'메일사용(댓글신고발생시) - 게시판관리자에게', 'rules'=>'trim|numeric'),
			array('field'=>'send_email_comment_blame_post_writer', 'label'=>'메일사용(댓글신고발생시) - 원글작성자에게', 'rules'=>'trim|numeric'),
			array('field'=>'send_email_comment_blame_comment_writer', 'label'=>'메일사용(댓글신고발생시) - 댓글작성자에게', 'rules'=>'trim|numeric'),
			array('field'=>'send_note_post_super_admin', 'label'=>'쪽지사용(원글작성시) - 최고관리자에게', 'rules'=>'trim|numeric'),
			array('field'=>'send_note_post_group_admin', 'label'=>'쪽지사용(원글작성시) - 그룹관리자에게', 'rules'=>'trim|numeric'),
			array('field'=>'send_note_post_board_admin', 'label'=>'쪽지사용(원글작성시) - 게시판관리자에게', 'rules'=>'trim|numeric'),
			array('field'=>'send_note_post_writer', 'label'=>'쪽지사용(원글작성시) - 원글작성자에게', 'rules'=>'trim|numeric'),
			array('field'=>'send_note_comment_super_admin', 'label'=>'쪽지사용(댓글작성시) - 최고관리자에게', 'rules'=>'trim|numeric'),
			array('field'=>'send_note_comment_group_admin', 'label'=>'쪽지사용(댓글작성시) - 그룹관리자에게', 'rules'=>'trim|numeric'),
			array('field'=>'send_note_comment_board_admin', 'label'=>'쪽지사용(댓글작성시) - 게시판관리자에게', 'rules'=>'trim|numeric'),
			array('field'=>'send_note_comment_post_writer', 'label'=>'쪽지사용(댓글작성시) - 원글작성자에게', 'rules'=>'trim|numeric'),
			array('field'=>'send_note_comment_comment_writer', 'label'=>'쪽지사용(댓글작성시) - 해당댓글작성자에게', 'rules'=>'trim|numeric'),
			array('field'=>'send_note_blame_super_admin', 'label'=>'쪽지사용(신고발생시) - 최고관리자에게', 'rules'=>'trim|numeric'),
			array('field'=>'send_note_blame_group_admin', 'label'=>'쪽지사용(신고발생시) - 그룹관리자에게', 'rules'=>'trim|numeric'),
			array('field'=>'send_note_blame_board_admin', 'label'=>'쪽지사용(신고발생시) - 게시판관리자에게', 'rules'=>'trim|numeric'),
			array('field'=>'send_note_blame_post_writer', 'label'=>'쪽지사용(신고발생시) - 원글작성자에게', 'rules'=>'trim|numeric'),
			array('field'=>'send_note_comment_blame_super_admin', 'label'=>'쪽지사용(댓글신고발생시) - 최고관리자에게', 'rules'=>'trim|numeric'),
			array('field'=>'send_note_comment_blame_group_admin', 'label'=>'쪽지사용(댓글신고발생시) - 그룹관리자에게', 'rules'=>'trim|numeric'),
			array('field'=>'send_note_comment_blame_board_admin', 'label'=>'쪽지사용(댓글신고발생시) - 게시판관리자에게', 'rules'=>'trim|numeric'),
			array('field'=>'send_note_comment_blame_post_writer', 'label'=>'쪽지사용(댓글신고발생시) - 원글작성자에게', 'rules'=>'trim|numeric'),
			array('field'=>'send_note_comment_blame_comment_writer', 'label'=>'쪽지사용(댓글신고발생시) - 댓글작성자에게', 'rules'=>'trim|numeric'),
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

			$array = array('send_email_post_super_admin', 'send_email_post_group_admin', 'send_email_post_board_admin', 'send_email_post_writer', 'send_email_comment_super_admin', 'send_email_comment_group_admin', 'send_email_comment_board_admin', 'send_email_comment_post_writer', 'send_email_comment_comment_writer', 'send_email_blame_super_admin', 'send_email_blame_group_admin', 'send_email_blame_board_admin', 'send_email_blame_post_writer', 'send_email_comment_blame_super_admin', 'send_email_comment_blame_group_admin', 'send_email_comment_blame_board_admin', 'send_email_comment_blame_post_writer', 'send_email_comment_blame_comment_writer', 'send_note_post_super_admin', 'send_note_post_group_admin', 'send_note_post_board_admin', 'send_note_post_writer', 'send_note_comment_super_admin', 'send_note_comment_group_admin', 'send_note_comment_board_admin', 'send_note_comment_post_writer', 'send_note_comment_comment_writer', 'send_note_blame_super_admin', 'send_note_blame_group_admin', 'send_note_blame_board_admin', 'send_note_blame_post_writer', 'send_note_comment_blame_super_admin', 'send_note_comment_blame_group_admin', 'send_note_comment_blame_board_admin', 'send_note_comment_blame_post_writer', 'send_note_comment_blame_comment_writer' );

			$metadata = array();
			foreach ($array as $value) {
				$metadata[$value] = $this->input->post($value);
			}

			$groupdata = array();
			if ($this->input->post('grp')) {
				foreach ($this->input->post('grp') as $key => $value) {
					if ($value) {
						if ($key == 'send_email_post') {
							$groupdata['send_email_post_super_admin'] = $this->input->post('send_email_post_super_admin');
							$groupdata['send_email_post_group_admin'] = $this->input->post('send_email_post_group_admin');
							$groupdata['send_email_post_board_admin'] = $this->input->post('send_email_post_board_admin');
							$groupdata['send_email_post_writer'] = $this->input->post('send_email_post_writer');
						}
						if ($key == 'send_email_comment') {
							$groupdata['send_email_comment_super_admin'] = $this->input->post('send_email_comment_super_admin');
							$groupdata['send_email_comment_group_admin'] = $this->input->post('send_email_comment_group_admin');
							$groupdata['send_email_comment_board_admin'] = $this->input->post('send_email_comment_board_admin');
							$groupdata['send_email_comment_post_writer'] = $this->input->post('send_email_comment_post_writer');
							$groupdata['send_email_comment_comment_writer'] = $this->input->post('send_email_comment_comment_writer');
						}
						if ($key == 'send_email_blame') {
							$groupdata['send_email_blame_super_admin'] = $this->input->post('send_email_blame_super_admin');
							$groupdata['send_email_blame_group_admin'] = $this->input->post('send_email_blame_group_admin');
							$groupdata['send_email_blame_board_admin'] = $this->input->post('send_email_blame_board_admin');
							$groupdata['send_email_blame_post_writer'] = $this->input->post('send_email_blame_post_writer');
						}
						if ($key == 'send_email_comment_blame') {
							$groupdata['send_email_comment_blame_super_admin'] = $this->input->post('send_email_comment_blame_super_admin');
							$groupdata['send_email_comment_blame_group_admin'] = $this->input->post('send_email_comment_blame_group_admin');
							$groupdata['send_email_comment_blame_board_admin'] = $this->input->post('send_email_comment_blame_board_admin');
							$groupdata['send_email_comment_blame_post_writer'] = $this->input->post('send_email_comment_blame_post_writer');
							$groupdata['send_email_comment_blame_comment_writer'] = $this->input->post('send_email_comment_blame_comment_writer');
						}
						if ($key == 'send_note_post') {
							$groupdata['send_note_post_super_admin'] = $this->input->post('send_note_post_super_admin');
							$groupdata['send_note_post_group_admin'] = $this->input->post('send_note_post_group_admin');
							$groupdata['send_note_post_board_admin'] = $this->input->post('send_note_post_board_admin');
							$groupdata['send_note_post_writer'] = $this->input->post('send_note_post_writer');
						}
						if ($key == 'send_note_comment') {
							$groupdata['send_note_comment_super_admin'] = $this->input->post('send_note_comment_super_admin');
							$groupdata['send_note_comment_group_admin'] = $this->input->post('send_note_comment_group_admin');
							$groupdata['send_note_comment_board_admin'] = $this->input->post('send_note_comment_board_admin');
							$groupdata['send_note_comment_post_writer'] = $this->input->post('send_note_comment_post_writer');
							$groupdata['send_note_comment_comment_writer'] = $this->input->post('send_note_comment_comment_writer');
						}
						if ($key == 'send_note_blame') {
							$groupdata['send_note_blame_super_admin'] = $this->input->post('send_note_blame_super_admin');
							$groupdata['send_note_blame_group_admin'] = $this->input->post('send_note_blame_group_admin');
							$groupdata['send_note_blame_board_admin'] = $this->input->post('send_note_blame_board_admin');
							$groupdata['send_note_blame_post_writer'] = $this->input->post('send_note_blame_post_writer');
						}
						if ($key == 'send_note_comment_blame') {
							$groupdata['send_note_comment_blame_super_admin'] = $this->input->post('send_note_comment_blame_super_admin');
							$groupdata['send_note_comment_blame_group_admin'] = $this->input->post('send_note_comment_blame_group_admin');
							$groupdata['send_note_comment_blame_board_admin'] = $this->input->post('send_note_comment_blame_board_admin');
							$groupdata['send_note_comment_blame_post_writer'] = $this->input->post('send_note_comment_blame_post_writer');
							$groupdata['send_note_comment_blame_comment_writer'] = $this->input->post('send_note_comment_blame_comment_writer');
						}
					}
				}
			}

			$alldata = array();
			if ($this->input->post('all')) {
				foreach ($this->input->post('all') as $key => $value) {
					if ($value) {
						if ($key == 'send_email_post') {
							$alldata['send_email_post_super_admin'] = $this->input->post('send_email_post_super_admin');
							$alldata['send_email_post_group_admin'] = $this->input->post('send_email_post_group_admin');
							$alldata['send_email_post_board_admin'] = $this->input->post('send_email_post_board_admin');
							$alldata['send_email_post_writer'] = $this->input->post('send_email_post_writer');
						}
						if ($key == 'send_email_comment') {
							$alldata['send_email_comment_super_admin'] = $this->input->post('send_email_comment_super_admin');
							$alldata['send_email_comment_group_admin'] = $this->input->post('send_email_comment_group_admin');
							$alldata['send_email_comment_board_admin'] = $this->input->post('send_email_comment_board_admin');
							$alldata['send_email_comment_post_writer'] = $this->input->post('send_email_comment_post_writer');
							$alldata['send_email_comment_comment_writer'] = $this->input->post('send_email_comment_comment_writer');
						}
						if ($key == 'send_email_blame') {
							$alldata['send_email_blame_super_admin'] = $this->input->post('send_email_blame_super_admin');
							$alldata['send_email_blame_group_admin'] = $this->input->post('send_email_blame_group_admin');
							$alldata['send_email_blame_board_admin'] = $this->input->post('send_email_blame_board_admin');
							$alldata['send_email_blame_post_writer'] = $this->input->post('send_email_blame_post_writer');
						}
						if ($key == 'send_email_comment_blame') {
							$alldata['send_email_comment_blame_super_admin'] = $this->input->post('send_email_comment_blame_super_admin');
							$alldata['send_email_comment_blame_group_admin'] = $this->input->post('send_email_comment_blame_group_admin');
							$alldata['send_email_comment_blame_board_admin'] = $this->input->post('send_email_comment_blame_board_admin');
							$alldata['send_email_comment_blame_post_writer'] = $this->input->post('send_email_comment_blame_post_writer');
							$alldata['send_email_comment_blame_comment_writer'] = $this->input->post('send_email_comment_blame_comment_writer');
						}
						if ($key == 'send_note_post') {
							$alldata['send_note_post_super_admin'] = $this->input->post('send_note_post_super_admin');
							$alldata['send_note_post_group_admin'] = $this->input->post('send_note_post_group_admin');
							$alldata['send_note_post_board_admin'] = $this->input->post('send_note_post_board_admin');
							$alldata['send_note_post_writer'] = $this->input->post('send_note_post_writer');
						}
						if ($key == 'send_note_comment') {
							$alldata['send_note_comment_super_admin'] = $this->input->post('send_note_comment_super_admin');
							$alldata['send_note_comment_group_admin'] = $this->input->post('send_note_comment_group_admin');
							$alldata['send_note_comment_board_admin'] = $this->input->post('send_note_comment_board_admin');
							$alldata['send_note_comment_post_writer'] = $this->input->post('send_note_comment_post_writer');
							$alldata['send_note_comment_comment_writer'] = $this->input->post('send_note_comment_comment_writer');
						}
						if ($key == 'send_note_blame') {
							$alldata['send_note_blame_super_admin'] = $this->input->post('send_note_blame_super_admin');
							$alldata['send_note_blame_group_admin'] = $this->input->post('send_note_blame_group_admin');
							$alldata['send_note_blame_board_admin'] = $this->input->post('send_note_blame_board_admin');
							$alldata['send_note_blame_post_writer'] = $this->input->post('send_note_blame_post_writer');
						}
						if ($key == 'send_note_comment_blame') {
							$alldata['send_note_comment_blame_super_admin'] = $this->input->post('send_note_comment_blame_super_admin');
							$alldata['send_note_comment_blame_group_admin'] = $this->input->post('send_note_comment_blame_group_admin');
							$alldata['send_note_comment_blame_board_admin'] = $this->input->post('send_note_comment_blame_board_admin');
							$alldata['send_note_comment_blame_post_writer'] = $this->input->post('send_note_comment_blame_post_writer');
							$alldata['send_note_comment_blame_comment_writer'] = $this->input->post('send_note_comment_blame_comment_writer');
						}
					}
				}
			}

			$this->Board_meta_model->save($pid, $metadata);

			$getdata = $this->{$this->modelname}->get_one($pid);
			if ( ! element('brd_id', $getdata))  show_404();
			if ($groupdata) {
				$where = array('bgr_id' => $getdata['bgr_id']);
				$res = $this->Board_model->get($primary_value='' , $select = '', $where);
				foreach ($res as $bkey => $bval) {
					if ($bval['brd_id'] == $getdata['brd_id']) continue;
					$this->Board_meta_model->save($bval['brd_id'], $groupdata);
				}
			}
			if ($alldata) {
				$res = $this->Board_model->get();
				foreach ($res as $bkey => $bval) {
					if ($bval['brd_id'] == $getdata['brd_id']) continue;
					$this->Board_meta_model->save($bval['brd_id'], $alldata);
				}
			}
			$view['view']['alert_message'] = '메일/쪽지 설정이 저장되었습니다';

		}

		$getdata = $this->{$this->modelname}->get_one($pid);
		if ( ! element('brd_id', $getdata))  show_404();
		$getdata = array_merge($getdata, $this->Board_meta_model->get_all_meta(element('brd_id', $getdata)));
		$view['view']['data'] = $getdata;

		/**
		* primary key 정보를 저장합니다
		*/
		$view['view']['primary_key'] = $primary_key;

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

		/**
		* 어드민 레이아웃을 정의합니다
		*/
		$layoutconfig		= array('layout' => 'layout', 'skin' => 'write_alarm');
		$view['layout']	= $this->managelayout->admin($layoutconfig, $this->cbconfig->get_device_view_type());
		$this->data			= $view;
		$this->layout		= element('layout_skin_file', element('layout', $view));
		$this->view			= element('view_skin_file', element('layout', $view));


	}

	/**
	* 게시판관리> 글쓰기>RSS 설정
	*/
	public function write_rss($pid = '')
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_admin_board_boards_write_rss';
		$this->load->event($eventname);

		$view = array();
		$view['view'] = array();

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before'] = Events::trigger('before', $eventname);

		/**
		* 프라이머리키에 숫자형이 입력되지 않으면 에러처리합니다
		*/
		if ( ! $pid)  show_404();
		if ( ! is_numeric($pid))  show_404();

		$primary_key = $this->{$this->modelname}->primary_key;

		/**
		* Validation 라이브러리를 가져옵니다
		*/
		$this->load->library('form_validation');

		/**
		* 전송된 데이터의 유효성을 체크합니다
		*/
		$config = array(
			array('field'=>'is_submit', 'label'=>'전송', 'rules'=>'trim|numeric'),
			array('field'=>'use_rss_feed', 'label'=>'RSS 피드공개', 'rules'=>'trim'),
			array('field'=>'use_rss_total_feed', 'label'=>'통합피드에포함', 'rules'=>'trim|numeric'),
			array('field'=>'rss_feed_content', 'label'=>'내용공개설정', 'rules'=>'trim|numeric'),
			array('field'=>'rss_feed_description', 'label'=>'RSS 피드설명', 'rules'=>'trim'),
			array('field'=>'rss_feed_copyright', 'label'=>'RSS 피드 저작권', 'rules'=>'trim'),
			array('field'=>'rss_feed_post_count', 'label'=>'RSS 출력 게시물수', 'rules'=>'trim|numeric'),
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

			$array = array('use_rss_feed', 'use_rss_total_feed', 'rss_feed_content', 'rss_feed_description', 'rss_feed_copyright', 'rss_feed_post_count',);

			$metadata = array();
			$groupdata = array();
			$alldata = array();
			$grp = $this->input->post('grp');
			$all = $this->input->post('all');

			foreach ($array as $value) {
				$metadata[$value] = $this->input->post($value);
				if (element($value, $grp)) {
					$groupdata[$value] = $this->input->post($value);
				}
				if (element($value, $all)) {
					$alldata[$value] = $this->input->post($value);
				}
			}

			$this->Board_meta_model->save($pid, $metadata);

			$getdata = $this->{$this->modelname}->get_one($pid);
			if ( ! element('brd_id', $getdata))  show_404();
			if ($groupdata) {
				$where = array('bgr_id' => $getdata['bgr_id']);
				$res = $this->Board_model->get($primary_value='' , $select = '', $where);
				foreach ($res as $bkey => $bval) {
					if ($bval['brd_id'] == $getdata['brd_id']) continue;
					$this->Board_meta_model->save($bval['brd_id'], $groupdata);
				}
			}
			if ($alldata) {
				$res = $this->Board_model->get();
				foreach ($res as $bkey => $bval) {
					if ($bval['brd_id'] == $getdata['brd_id']) continue;
					$this->Board_meta_model->save($bval['brd_id'], $alldata);
				}
			}
			$view['view']['alert_message'] = 'RSS 설정이 저장되었습니다';

		}

		$getdata = $this->{$this->modelname}->get_one($pid);
		if ( ! element('brd_id', $getdata))  show_404();
		$getdata = array_merge($getdata, $this->Board_meta_model->get_all_meta(element('brd_id', $getdata)));
		$view['view']['data'] = $getdata;

		/**
		* primary key 정보를 저장합니다
		*/
		$view['view']['primary_key'] = $primary_key;

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

		/**
		* 어드민 레이아웃을 정의합니다
		*/
		$layoutconfig		= array('layout' => 'layout', 'skin' => 'write_rss');
		$view['layout']	= $this->managelayout->admin($layoutconfig, $this->cbconfig->get_device_view_type());
		$this->data			= $view;
		$this->layout		= element('layout_skin_file', element('layout', $view));
		$this->view			= element('view_skin_file', element('layout', $view));

	}

	/**
	* 게시판관리> 글쓰기>권한관리
	*/
	public function write_access($pid = '')
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_admin_board_boards_write_access';
		$this->load->event($eventname);

		$view = array();
		$view['view'] = array();

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before'] = Events::trigger('before', $eventname);

		/**
		* 프라이머리키에 숫자형이 입력되지 않으면 에러처리합니다
		*/
		if ( ! $pid)  show_404();
		if ( ! is_numeric($pid))  show_404();

		$primary_key = $this->{$this->modelname}->primary_key;

		/**
		* Validation 라이브러리를 가져옵니다
		*/
		$this->load->library('form_validation');

		/**
		* 전송된 데이터의 유효성을 체크합니다
		*/
		$config = array(
			array('field'=>'is_submit', 'label'=>'전송', 'rules'=>'trim|numeric'),
			array('field'=>'access_list', 'label'=>'권한 - 목록', 'rules'=>'trim|numeric'),
			array('field'=>'access_list_level', 'label'=>'권한 - 목록 레벨', 'rules'=>'trim|numeric'),
			array('field'=>'access_list_group[]', 'label'=>'권한 - 목록 그룹', 'rules'=>'trim'),
			array('field'=>'access_view', 'label'=>'권한 - 열람', 'rules'=>'trim|numeric'),
			array('field'=>'access_view_level', 'label'=>'권한 - 열람 레벨', 'rules'=>'trim|numeric'),
			array('field'=>'access_view_group[]', 'label'=>'권한 - 열람 그룹', 'rules'=>'trim'),
			array('field'=>'access_write', 'label'=>'권한 - 글작성', 'rules'=>'trim|numeric'),
			array('field'=>'access_write_level', 'label'=>'권한 - 글작성 레벨', 'rules'=>'trim|numeric'),
			array('field'=>'access_write_group[]', 'label'=>'권한 - 글작성 그룹', 'rules'=>'trim'),
			array('field'=>'access_reply', 'label'=>'권한 - 답변', 'rules'=>'trim|numeric'),
			array('field'=>'access_reply_level', 'label'=>'권한 - 답변 레벨', 'rules'=>'trim|numeric'),
			array('field'=>'access_reply_group[]', 'label'=>'권한 - 답변 그룹', 'rules'=>'trim'),
			array('field'=>'access_comment', 'label'=>'권한 - 댓글', 'rules'=>'trim|numeric'),
			array('field'=>'access_comment_level', 'label'=>'권한 - 댓글 레벨', 'rules'=>'trim|numeric'),
			array('field'=>'access_comment_group[]', 'label'=>'권한 - 댓글 그룹', 'rules'=>'trim'),
			array('field'=>'access_upload', 'label'=>'권한 - 파일업로드', 'rules'=>'trim|numeric'),
			array('field'=>'access_upload_level', 'label'=>'권한 - 파일업로드 레벨', 'rules'=>'trim|numeric'),
			array('field'=>'access_upload_group[]', 'label'=>'권한 - 파일업로드 그룹', 'rules'=>'trim'),
			array('field'=>'access_download', 'label'=>'권한 - 파일다운로드', 'rules'=>'trim|numeric'),
			array('field'=>'access_download_level', 'label'=>'권한 - 파일다운로드 레벨', 'rules'=>'trim|numeric'),
			array('field'=>'access_download_group[]', 'label'=>'권한 - 파일다운로드 그룹', 'rules'=>'trim'),
			array('field'=>'access_dhtml', 'label'=>'권한 - DHTML', 'rules'=>'trim|numeric'),
			array('field'=>'access_dhtml_level', 'label'=>'권한 - DHTML 레벨', 'rules'=>'trim|numeric'),
			array('field'=>'access_dhtml_group[]', 'label'=>'권한 - DHTML 그룹', 'rules'=>'trim'),
			array('field'=>'access_blame', 'label'=>'권한 - 신고', 'rules'=>'trim|numeric'),
			array('field'=>'access_blame_level', 'label'=>'권한 - 신고 레벨', 'rules'=>'trim|numeric'),
			array('field'=>'access_blame_group[]', 'label'=>'권한 - 신고 그룹', 'rules'=>'trim'),
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

			$array = array('access_list', 'access_list_level', 'access_view', 'access_view_level', 'access_write', 'access_write_level', 'access_reply', 'access_reply_level', 'access_comment',   'access_comment_level', 'access_upload', 'access_upload_level',   'access_download',   'access_download_level', 'access_dhtml', 'access_dhtml_level',  'access_blame',  'access_blame_level');

			$array_checkbox = array('access_list_group', 'access_view_group', 'access_write_group', 'access_reply_group', 'access_comment_group', 'access_upload_group', 'access_download_group', 'access_dhtml_group', 'access_blame_group');

			$metadata = array();
			$groupdata = array();
			$alldata = array();
			$grp = $this->input->post('grp');
			$all = $this->input->post('all');

			foreach ($array as $value) {
				$metadata[$value] = $this->input->post($value);
				if (element($value, $grp)) {
					$groupdata[$value] = $this->input->post($value);
					if ($value == 'access_list') {
						$groupdata['access_list_level'] = $this->input->post('access_list_level');
						$groupdata['access_list_group'] = json_encode($this->input->post('access_list_group'));
					}
					if ($value == 'access_view') {
						$groupdata['access_view_level'] = $this->input->post('access_view_level');
						$groupdata['access_view_group'] = json_encode($this->input->post('access_view_group'));
					}
					if ($value == 'access_write') {
						$groupdata['access_write_level'] = $this->input->post('access_write_level');
						$groupdata['access_write_group'] = json_encode($this->input->post('access_write_group'));
					}
					if ($value == 'access_reply') {
						$groupdata['access_reply_level'] = $this->input->post('access_reply_level');
						$groupdata['access_reply_group'] = json_encode($this->input->post('access_reply_group'));
					}
					if ($value == 'access_comment') {
						$groupdata['access_comment_level'] = $this->input->post('access_comment_level');
						$groupdata['access_comment_group'] = json_encode($this->input->post('access_comment_group'));
					}
					if ($value == 'access_upload') {
						$groupdata['access_upload_level'] = $this->input->post('access_upload_level');
						$groupdata['access_upload_group'] = json_encode($this->input->post('access_upload_group'));
					}
					if ($value == 'access_download') {
						$groupdata['access_download_level'] = $this->input->post('access_download_level');
						$groupdata['access_download_group'] = json_encode($this->input->post('access_download_group'));
					}
					if ($value == 'access_dhtml') {
						$groupdata['access_dhtml_level'] = $this->input->post('access_dhtml_level');
						$groupdata['access_dhtml_group'] = json_encode($this->input->post('access_dhtml_group'));
					}
					if ($value == 'access_blame') {
						$groupdata['access_blame_level'] = $this->input->post('access_blame_level');
						$groupdata['access_blame_group'] = json_encode($this->input->post('access_blame_group'));
					}
				}
				if (element($value, $all)) {
					$alldata[$value] = $this->input->post($value);
					if ($value == 'access_list') {
						$alldata['access_list_level'] = $this->input->post('access_list_level');
						$alldata['access_list_group'] = json_encode($this->input->post('access_list_group'));
					}
					if ($value == 'access_view') {
						$alldata['access_view_level'] = $this->input->post('access_view_level');
						$alldata['access_view_group'] = json_encode($this->input->post('access_view_group'));
					}
					if ($value == 'access_write') {
						$alldata['access_write_level'] = $this->input->post('access_write_level');
						$alldata['access_write_group'] = json_encode($this->input->post('access_write_group'));
					}
					if ($value == 'access_reply') {
						$alldata['access_reply_level'] = $this->input->post('access_reply_level');
						$alldata['access_reply_group'] = json_encode($this->input->post('access_reply_group'));
					}
					if ($value == 'access_comment') {
						$alldata['access_comment_level'] = $this->input->post('access_comment_level');
						$alldata['access_comment_group'] = json_encode($this->input->post('access_comment_group'));
					}
					if ($value == 'access_upload') {
						$alldata['access_upload_level'] = $this->input->post('access_upload_level');
						$alldata['access_upload_group'] = json_encode($this->input->post('access_upload_group'));
					}
					if ($value == 'access_download') {
						$alldata['access_download_level'] = $this->input->post('access_download_level');
						$alldata['access_download_group'] = json_encode($this->input->post('access_download_group'));
					}
					if ($value == 'access_dhtml') {
						$alldata['access_dhtml_level'] = $this->input->post('access_dhtml_level');
						$alldata['access_dhtml_group'] = json_encode($this->input->post('access_dhtml_group'));
					}
					if ($value == 'access_blame') {
						$alldata['access_blame_level'] = $this->input->post('access_blame_level');
						$alldata['access_blame_group'] = json_encode($this->input->post('access_blame_group'));
					}
				}
			}
			foreach ($array_checkbox as $value) {
				$metadata[$value] = json_encode($this->input->post($value));
			}

			$this->Board_meta_model->save($pid, $metadata);

			$getdata = $this->{$this->modelname}->get_one($pid);
			if ( ! element('brd_id', $getdata))  show_404();
			if ($groupdata) {
				$where = array('bgr_id' => $getdata['bgr_id']);
				$res = $this->Board_model->get($primary_value='' , $select = '', $where);
				foreach ($res as $bkey => $bval) {
					if ($bval['brd_id'] == $getdata['brd_id']) continue;
					$this->Board_meta_model->save($bval['brd_id'], $groupdata);
				}
			}
			if ($alldata) {
				$res = $this->Board_model->get();
				foreach ($res as $bkey => $bval) {
					if ($bval['brd_id'] == $getdata['brd_id']) continue;
					$this->Board_meta_model->save($bval['brd_id'], $alldata);
				}
			}
			$view['view']['alert_message'] = '권한관리 설정이 저장되었습니다';

		}

		$getdata = $this->{$this->modelname}->get_one($pid);
		if ( ! element('brd_id', $getdata))  show_404();
		$getdata = array_merge($getdata, $this->Board_meta_model->get_all_meta(element('brd_id', $getdata)));
		$getdata['config_max_level'] = $this->cbconfig->item('max_level');
		$view['view']['data'] = $getdata;

		/**
		* primary key 정보를 저장합니다
		*/
		$view['view']['primary_key'] = $primary_key;

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

		/**
		* 어드민 레이아웃을 정의합니다
		*/
		$layoutconfig		= array('layout' => 'layout', 'skin' => 'write_access');
		$view['layout']	= $this->managelayout->admin($layoutconfig, $this->cbconfig->get_device_view_type());
		$this->data			= $view;
		$this->layout		= element('layout_skin_file', element('layout', $view));
		$this->view			= element('view_skin_file', element('layout', $view));

	}

	/**
	* 게시판관리> 글쓰기>사용자정의
	*/
	public function write_extravars($pid = '')
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_admin_board_boards_write_extravars';
		$this->load->event($eventname);

		$view = array();
		$view['view'] = array();

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before'] = Events::trigger('before', $eventname);

		/**
		* 프라이머리키에 숫자형이 입력되지 않으면 에러처리합니다
		*/
		if ( ! $pid)  show_404();
		if ( ! is_numeric($pid))  show_404();

		$primary_key = $this->{$this->modelname}->primary_key;

		/**
		* Validation 라이브러리를 가져옵니다
		*/
		$this->load->library('form_validation');

		/**
		* 전송된 데이터의 유효성을 체크합니다
		*/
		$config = array(
			array('field'=>'s', 'label'=>'사용자정의', 'rules'=>'trim'),
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

			$updatedata = $this->input->post();

			$array_count_values = ( is_array($updatedata) && element('field_name', $updatedata)) ? array_count_values(element('field_name', $updatedata)) : '';
			$fail = FALSE;

			if ($fail == FALSE && $array_count_values) {
				foreach ($array_count_values as $akey => $aval) {
					if ($aval > 1) {
						$view['view']['warning_message'] = $akey . ' 값이 ' . $aval . ' 회 중복 입력되었습니다. ID 값이 중복되지 않게 입력해주세요';
						$fail = TRUE;
						break;
					}
				}
			}
			if ($fail == FALSE && $array_count_values) {
				foreach (element('field_name', $updatedata) as $fkey => $fval) {
					if ($fval == '') {
						$view['view']['warning_message'] = '비어있는 ID 값이 있습니다. ID 값을 빠뜨리지 말고 입력해주세요';
						$fail = TRUE;
						break;
					}
				}
			}
			if ($fail == FALSE && $array_count_values) {
				foreach (element('display_name', $updatedata) as $fkey => $fval) {
					if ($fval == '') {
						$view['view']['warning_message'] = '비어있는 입력항목제목이 있습니다. 입력항목제목 값을 빠뜨리지 말고 입력해주세요';
						$fail = TRUE;
						break;
					}
				}
			}
			if ($fail == FALSE) {

				$order = 0;
				$update = array();
				$extra_vars_field = array();

				if (element('key', $updatedata)) {
					foreach (element('key', $updatedata) as $key => $value) {
						if ($value) {
							$update[$value] = array(
								'field_name'	=> element($order, element('field_name', $updatedata)),
								'display_name'	=> element($order, element('display_name', $updatedata)),
								'use'	=> element($value, element('use', $updatedata)),
								'field_type'	=> element($value, element('field_type', $updatedata)),
								'required'	=> element($value, element('required', $updatedata)),
								'options'	=> element($value, element('options', $updatedata)),
							);
						} else {
							$update[$updatedata['field_name'][$order]] = array(
								'field_name' => element($order, element('field_name', $updatedata)),
								'display_name'	=> element($order, element('display_name', $updatedata)),
								'use'	=> element($key, element('use', $updatedata)),
								'field_type'	=> element($key, element('field_type', $updatedata)),
								'required'	=> element($key, element('required', $updatedata)),
								'options'	=> element($key, element('options', $updatedata)),
							);
						}
						$extra_vars_field[] = element($order, element('field_name', $updatedata));

						$order++;
					}
				}

				$old_boardform = $this->board->item_id('extravars', $pid);
				$old_data = json_decode($old_boardform, TRUE);
				if ($old_data) {
					foreach ($old_data as $oldkey => $oldvalue) {
						if ( ! in_array($oldkey, $extra_vars_field)) {
							$this->Post_extra_vars_model->deletemeta_item($oldkey);
						}
					}
				}
				$metadata = array('extravars' => json_encode($update));
				$this->Board_meta_model->save($pid, $metadata);
				$view['view']['alert_message'] = '정상적으로 저장되었습니다';
			}
		}

		$getdata = $this->{$this->modelname}->get_one($pid);
		if ( ! element('brd_id', $getdata))  show_404();
		$getdata = array_merge($getdata, $this->Board_meta_model->get_all_meta(element('brd_id', $getdata)));
		$getdata['result'] = json_decode(element('extravars', $getdata), TRUE);
		$view['view']['data'] = $getdata;

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

		/**
		* 어드민 레이아웃을 정의합니다
		*/
		$layoutconfig		= array('layout' => 'layout', 'skin' => 'write_extravars');
		$view['layout']	= $this->managelayout->admin($layoutconfig, $this->cbconfig->get_device_view_type());
		$this->data			= $view;
		$this->layout		= element('layout_skin_file', element('layout', $view));
		$this->view			= element('view_skin_file', element('layout', $view));

	}


	/**
	* 게시판관리> 글쓰기>게시판관리자
	*/
	public function write_admin($pid = '')
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_admin_board_boards_write_admin';
		$this->load->event($eventname);

		$view = array();
		$view['view'] = array();

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before'] = Events::trigger('before', $eventname);

		/**
		* 프라이머리키에 숫자형이 입력되지 않으면 에러처리합니다
		*/
		if ( ! $pid)  show_404();
		if ( ! is_numeric($pid))  show_404();

		/**
		* Validation 라이브러리를 가져옵니다
		*/
		$this->load->library('form_validation');
		/**
		* 전송된 데이터의 유효성을 체크합니다
		*/
		$config = array(
			array('field'=>'brd_id', 'label'=>'게시판아이디', 'rules'=>'trim|required|numeric|is_natural'),
			array('field'=>'userid', 'label'=>'회원아이디', 'rules'=>'trim|required|alpha_dash|min_length[3]|max_length[50]|callback__userid_check['.$pid.']'),
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

			$memdata = $this->Member_model->get_by_userid($this->input->post('userid'), 'mem_id');
			$mem_id = element('mem_id', $memdata);

			$insertdata = array(
				'brd_id' => $this->input->post('brd_id'),
				'mem_id' => $mem_id,
			);

			$this->Board_admin_model->insert($insertdata);
		}


		/**
		* 페이지에 숫자가 아닌 문자가 입력되거나 1보다 작은 숫자가 입력되면 에러 페이지를 보여줍니다.
		*/
		$param =& $this->querystring;
		$findex = $param->get('findex', $this->Board_admin_model->primary_key);
		$forder = $param->get('forder', 'desc');

		$getdata = $this->{$this->modelname}->get_one($pid);
		if ( ! element('brd_id', $getdata))  show_404();
		$result = $this->Board_admin_model->get('', '', array('brd_id' => $pid) , '' , '', $findex, $forder);
		if ($result && is_array($result)) {
			foreach ($result as $key => $val) {
				$result[$key]['member'] = $dbmember = $this->Member_model->get_by_memid(element('mem_id', $val), 'mem_id, mem_userid, mem_nickname, mem_email, mem_icon');
				$result[$key]['display_name'] = display_username(element('mem_userid', $dbmember), element('mem_nickname', $dbmember), element('mem_icon', $dbmember));
			}
		}
		$view['view']['list'] = $result;
		$view['view']['data'] = $getdata;

		$primary_key = $this->Board_admin_model->primary_key;

		$view['view']['list_delete_url'] = admin_url($this->pagedir . '/write_admin_delete/' . $pid . '?' . $param->output());
		$view['view']['primary_key'] = $primary_key;

		// 이벤트가 존재하면 실행합니다
		$view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

		/**
		* 어드민 레이아웃을 정의합니다
		*/
		$layoutconfig		= array('layout' => 'layout', 'skin' => 'write_admin');
		$view['layout']	= $this->managelayout->admin($layoutconfig, $this->cbconfig->get_device_view_type());
		$this->data			= $view;
		$this->layout		= element('layout_skin_file', element('layout', $view));
		$this->view			= element('view_skin_file', element('layout', $view));

	}

	/**
	* 게시판관리> 글쓰기>회원아이디체크
	*/
	public function _userid_check($str='', $pid)
	{
		if ( ! $str) {
			$this->form_validation->set_message('_userid_check', '회원아이디가 입력되지 않았습니다');
			return FALSE;
		}

		$getdata = $this->Member_model->get_by_userid($str, 'mem_id, mem_denied');

		if ( ! element('mem_id', $getdata)) {
			$this->form_validation->set_message('_userid_check', $str . ' 은(는) 존재하지 않는 회원아이디입니다');
			return FALSE;
		} else if (element('mem_denied', $getdata)) {
			$this->form_validation->set_message('_userid_check', $str . ' 은(는) 탈퇴 또는 차단된 회원아이디입니다');
			return FALSE;
		} else {
			$chkdata = $this->Board_admin_model->get_one('', '', array('brd_id' => $pid, 'mem_id' => element('mem_id', $getdata)));
			if (element('mem_id', $chkdata)) {
				$this->form_validation->set_message('_userid_check', $str . ' 은(는) 이미 입력된 회원아이디입니다');
				return FALSE;
			}

			return TRUE;
		}
	}

	/**
	* 목록 페이지에서 선택수정을 하는 경우 실행되는 메소드입니다
	*/
	public function listupdate()
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_admin_board_boards_listupdate';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		/**
		* 체크한 게시물의 업데이트를 실행합니다
		*/
		if ($this->input->post('chk') && is_array($this->input->post('chk'))) {
			foreach ($this->input->post('chk') as $val) {
				if ($val) {
					$bgr_id = $this->input->post('bgr_id');
					$brd_name = $this->input->post('brd_name');
					$brd_mobile_name = $this->input->post('brd_mobile_name');
					$brd_search = $this->input->post('brd_search');
					$brd_search_update = isset($brd_search[$val]) ? '1' : '';
					$brd_order = $this->input->post('brd_order');
					$board_layout = $this->input->post('board_layout');
					$board_mobile_layout = $this->input->post('board_mobile_layout');
					$board_sidebar = $this->input->post('board_sidebar');
					$board_mobile_sidebar = $this->input->post('board_mobile_sidebar');
					$board_skin = $this->input->post('board_skin');
					$board_mobile_skin = $this->input->post('board_mobile_skin');
					$point_read = $this->input->post('point_read');
					$point_write = $this->input->post('point_write');
					$point_comment = $this->input->post('point_comment');
					$point_download = $this->input->post('point_download');
					$updatedata = array(
						'bgr_id' => $bgr_id[$val],
						'brd_name' => $brd_name[$val],
						'brd_mobile_name' => $brd_mobile_name[$val],
						'brd_search' => $brd_search_update,
						'brd_order' => $brd_order[$val],
					);
					$metadata = array(
						'board_layout' => $board_layout[$val],
						'board_mobile_layout' => $board_mobile_layout[$val],
						'board_sidebar' => $board_sidebar[$val],
						'board_mobile_sidebar' => $board_mobile_sidebar[$val],
						'board_skin' => $board_skin[$val],
						'board_mobile_skin' => $board_mobile_skin[$val],
						'point_read' => $point_read[$val],
						'point_write' => $point_write[$val],
						'point_comment' => $point_comment[$val],
						'point_download' => $point_download[$val],
					);
					$this->{$this->modelname}->update($val, $updatedata);
					$this->Board_meta_model->save($val, $metadata);
				}
			}
		}

		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		/**
		* 업데이트가 끝난 후 목록페이지로 이동합니다
		*/
		$this->session->set_flashdata('message', '정상적으로 수정되었습니다');
		$param =& $this->querystring;
		$redirecturl = admin_url($this->pagedir . '?' .  $param->output());
		redirect($redirecturl);
	}

	/**
	* 목록 페이지에서 선택삭제를 하는 경우 실행되는 메소드입니다
	*/
	public function listdelete()
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_admin_board_boards_listdelete';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		/**
		* 체크한 게시물의 삭제를 실행합니다
		*/
		$this->load->model(array('Blame_model', 'Comment_model', 'Like_model', 'Post_model', 'Post_extra_vars_model', 'Post_file_model', 'Post_link_model', 'Post_meta_model', 'Scrap_model', 'Stat_count_board_model', 'Tempsave_model'));

		if ($this->input->post('chk') && is_array($this->input->post('chk'))) {
			foreach ($this->input->post('chk') as $val) {
				if ($val) {
					$this->Board_model->delete($val);
					$this->Board_meta_model->deletemeta($val);
					
					$where = array('brd_id' => $val);
					$this->Blame_model->delete('', $where);
					$this->Board_admin_model->delete('', $where);
					$this->Board_category_model->delete('', $where);
					$this->Comment_model->delete('', $where);
					$this->Like_model->delete('', $where);
					$this->Post_model->delete('', $where);
					$this->Post_extra_vars_model->delete('', $where);
					$this->Post_file_model->delete('', $where);
					$this->Post_link_model->delete('', $where);
					$this->Post_meta_model->delete('', $where);
					$this->Scrap_model->delete('', $where);
					$this->Stat_count_board_model->delete('', $where);
					$this->Tempsave_model->delete('', $where);
				}
			}
		}

		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		/**
		* 삭제가 끝난 후 목록페이지로 이동합니다
		*/
		$this->session->set_flashdata('message', '정상적으로 삭제되었습니다');
		$param =& $this->querystring;
		$redirecturl = admin_url($this->pagedir . '?' .  $param->output());
		redirect($redirecturl);

	}

	/**
	* 목록 페이지에서 선택삭제를 하는 경우 실행되는 메소드입니다
	*/
	public function write_admin_delete($pid = '')
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_admin_board_boards_write_admin_delete';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		if ( ! $pid)  show_404();
		if ( ! is_numeric($pid))  show_404();

		/**
		* 체크한 게시물의 삭제를 실행합니다
		*/
		if ($this->input->post('chk') && is_array($this->input->post('chk'))) {
			foreach ($this->input->post('chk') as $val) {
				if ($val) {
					$this->Board_admin_model->delete($val);
				}
			}
		}

		// 이벤트가 존재하면 실행합니다
		Events::trigger('after', $eventname);

		/**
		* 삭제가 끝난 후 목록페이지로 이동합니다
		*/
		$this->session->set_flashdata('message', '정상적으로 삭제되었습니다');
		$param =& $this->querystring;
		$redirecturl = admin_url($this->pagedir . '/write_admin/' . $pid . '?' .  $param->output());
		redirect($redirecturl);

	}

}
