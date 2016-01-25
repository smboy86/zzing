<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Members class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

/**
 * 관리자>회원설정>회원관리 controller 입니다.
 */
class Members extends CB_Controller
{

	/**
	*  관리자 페이지 상의 현재 디렉토리입니다
	*  페이지 이동시 필요한 정보입니다
	*/
	public $pagedir = 'member/members';

	/**
	*  모델을 로딩합니다
	*/
	protected $models = array('Member', 'Member_meta', 'Member_nickname');

	/**
	*  이 컨트롤러의 메인 모델 이름입니다
	*/
	protected $modelname = 'Member_model';

	/**
	*  헬퍼를 로딩합니다
	*/
	protected $helpers = array('form', 'array', 'chkstring');

	protected $member_denied = array(
		'0'=>'<span>승인</span>',
		'1'=>'<span style="color:#d9534f">탈퇴함</span>',
		'2'=>'<span style="color:#4F9BD9">관리자에 의한 차단</span>',
	);

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
		$eventname = 'event_admin_member_members_index';
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
			'mem_id' => 	$param->sort('mem_id', 'asc'),
			'mem_userid' => 	$param->sort('mem_userid', 'asc'),
			'mem_username' => 	$param->sort('mem_username', 'asc'),
			'mem_nickname' => 	$param->sort('mem_nickname', 'asc'),
			'mem_email' => 	$param->sort('mem_email', 'asc'),
			'mem_point' => 	$param->sort('mem_point', 'asc'),
			'mem_register_datetime' => 	$param->sort('mem_register_datetime', 'asc'),
			'mem_lastlogin_datetime' => 	$param->sort('mem_lastlogin_datetime', 'asc'),
			'mem_level' => 	$param->sort('mem_level', 'asc'),
		);
		$findex = $param->get('findex', 'member.mem_id');
		$forder = $param->get('forder', 'desc');
		$sfield = $param->get('sfield');
		$skeyword = $param->get('skeyword');

		$per_page = admin_listnum();
		$offset = ($page - 1) * $per_page;

		/**
		* 게시판 목록에 필요한 정보를 가져옵니다.
		*/
		$this->{$this->modelname}->allow_search_field = array('mem_id', 'mem_userid', 'mem_email',  'mem_username', 'mem_nickname', 'mem_level', 'mem_homepage', 'mem_register_datetime', 'mem_register_ip', 'mem_lastlogin_datetime', 'mem_lastlogin_ip', 'mem_is_admin'); // 검색이 가능한 필드
		$this->{$this->modelname}->search_field_equal = array('mem_id', 'mem_level', 'mem_is_admin'); // 검색중 like 가 아닌 = 검색을 하는 필드
		$this->{$this->modelname}->allow_order_field = array('member.mem_id', 'mem_userid', 'mem_username', 'mem_nickname', 'mem_email', 'mem_point', 'mem_register_datetime', 'mem_lastlogin_datetime', 'mem_level'); // 정렬이 가능한 필드
		$where = array();
		if ($this->input->get('mem_is_admin')) $where['mem_is_admin'] = 1;
		if ($this->input->get('mem_denied')) $where['mem_denied'] = 1;
		if ($this->input->get('mgr_id') && is_numeric($this->input->get('mgr_id'))) $where['mgr_id'] = $this->input->get('mgr_id');
		$result = $this->{$this->modelname}->get_admin_list($per_page, $offset, $where , '' , $findex, $forder, $sfield, $skeyword);
		$list_num = $result['total_rows'] - ($page - 1) * $per_page;
		if (element('list', $result)) {
			foreach (element('list', $result) as $key => $val) {
				$result['list'][$key]['mem_denied_text'] = $this->member_denied[$result['list'][$key]['mem_denied']];
				$result['list'][$key]['display_name'] =  display_username(element('mem_userid', $val), element('mem_nickname', $val), element('mem_icon', $val));
				$result['list'][$key]['meta'] = $this->Member_meta_model->get_all_meta(element('mem_id', $val));

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
		$search_option = array('mem_userid' => '회원아이디', 'mem_email' => '이메일', 'mem_username' => '회원명', 'mem_nickname' => '닉네임', 'mem_level' => '회원레벨', 'mem_homepage' => '홈페이지', 'mem_register_datetime' => '회원가입날짜', 'mem_register_ip' => '회원가입IP', 'mem_lastlogin_datetime' => '최종로그인날짜', 'mem_lastlogin_ip' => '최종로그인IP', 'mem_adminmemo' => '관리자메모');
		$view['view']['skeyword'] = ($sfield && array_key_exists($sfield, $search_option)) ? $this->input->get('skeyword') : '';
		$view['view']['search_option'] = search_option($search_option, $sfield);
		$view['view']['listall_url'] = admin_url($this->pagedir);
		$view['view']['write_url'] = admin_url($this->pagedir . '/write');
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
		$eventname = 'event_admin_member_members_write';
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
			$getdata['extras'] = $this->Member_extra_vars_model->get_all_meta($pid);
			$getdata['meta'] = $this->Member_meta_model->get_all_meta($pid);
		}
		$getdata['config_max_level'] = $this->cbconfig->item('max_level');
		$registerform = $this->cbconfig->item('registerform');
		$form = json_decode($registerform, TRUE);

		/**
		* Validation 라이브러리를 가져옵니다
		*/
		$this->load->library('form_validation');

 		if ( ! function_exists('password_hash')) {
			$this->load->helper('password');
		}


		/**
		* 전송된 데이터의 유효성을 체크합니다
		*/
		$config = array(
			array('field'=>'mem_username', 'label'=>'이름', 'rules'=>'trim|min_length[2]|max_length[20]'),
			array('field'=>'mem_level', 'label'=>'레벨', 'rules'=>'trim|required|numeric|less_than_equal_to[' . element('config_max_level', $getdata) . ']|is_natural_no_zero'),
			array('field'=>'mem_homepage', 'label'=>'홈페이지', 'rules'=>'valid_url'),
			array('field'=>'mem_birthday', 'label'=>'생일', 'rules'=>'trim|exact_length[10]'),
			array('field'=>'mem_sex', 'label'=>'성별', 'rules'=>'trim|exact_length[1]'),
			array('field'=>'mem_phone', 'label'=>'전화번호', 'rules'=>'trim|valid_phone'),
			array('field'=>'mem_zipcode', 'label'=>'우편번호', 'rules'=>'trim|exact_length[7]'),
			array('field'=>'mem_address1', 'label'=>'기본주소', 'rules'=>'trim'),
			array('field'=>'mem_address2', 'label'=>'상세주소', 'rules'=>'trim'),
			array('field'=>'mem_address3', 'label'=>'참고항목', 'rules'=>'trim'),
			array('field'=>'mem_address4', 'label'=>'지번', 'rules'=>'trim'),
			array('field'=>'mem_profile_content', 'label'=>'자기소개', 'rules'=>'trim'),
			array('field'=>'mem_open_profile', 'label'=>'정보공개', 'rules'=>'trim|exact_length[1]'),
			array('field'=>'mem_use_note', 'label'=>'쪽지사용', 'rules'=>'trim|exact_length[1]'),
			array('field'=>'mem_receive_email', 'label'=>'이메일수신여부', 'rules'=>'trim|exact_length[1]'),
			array('field'=>'mem_receive_sms', 'label'=>'SMS 문자수신여부', 'rules'=>'trim|exact_length[1]'),
		);
		if ($this->input->post($primary_key)) {
			$config[] = array('field'=>'mem_userid', 'label'=>'회원아이디', 'rules'=>'trim|required|alphanumunder|min_length[3]|max_length[20]|is_unique[member.mem_userid.mem_id.' . element('mem_id', $getdata) . ']|callback__mem_userid_check');
			$config[] = array('field'=>'mem_password', 'label'=>'패스워드', 'rules'=>'trim|min_length[4]');
			$config[] = array('field'=>'mem_email', 'label'=>'회원이메일', 'rules'=>'trim|required|valid_email|is_unique[member.mem_email.mem_id.' . element('mem_id', $getdata) . ']');
			$config[] = array('field'=>'mem_nickname', 'label'=>'회원닉네임', 'rules'=>'trim|required|min_length[2]|max_length[20]|callback__mem_nickname_check|is_unique[member.mem_nickname.mem_id.' . element('mem_id', $getdata) . ']');
		} else {
			$config[] = array('field'=>'mem_userid', 'label'=>'회원아이디', 'rules'=>'trim|required|alphanumunder|min_length[3]|max_length[20]|is_unique[member.mem_userid]');
			$config[] = array('field'=>'mem_password', 'label'=>'패스워드', 'rules'=>'trim|required|min_length[4]');
			$config[] = array('field'=>'mem_email', 'label'=>'회원이메일', 'rules'=>'trim|required|valid_email|is_unique[member.mem_email]');
			$config[] = array('field'=>'mem_nickname', 'label'=>'회원닉네임', 'rules'=>'trim|required|min_length[2]|max_length[20]|callback__mem_nickname_check|is_unique[member.mem_nickname]');
		}
		$this->form_validation->set_rules($config);
		$form_validation = $this->form_validation->run() ;
		$file_error = '';
		$updatephoto = '';
		$file_error2 = '';
		$updateicon = '';

		if ($form_validation) {
			$this->load->library('upload');
			if ( isset($_FILES) && isset($_FILES['mem_photo']) && isset($_FILES['mem_photo']['name']) && $_FILES['mem_photo']['name']) {
				$upload_path = './uploads/member_photo/';
				if ( ! is_dir($upload_path)) {
					mkdir($upload_path, 0707);
					$file = $upload_path . 'index.php';
					$f = @fopen($file, 'w');
					@fwrite($f, '');
					@fclose($f);
					@chmod($file, 0644);
				}
				$upload_path .= cdate('Y') . '/';
				if ( ! is_dir($upload_path)) {
					mkdir($upload_path, 0707);
					$file = $upload_path . 'index.php';
					$f = @fopen($file, 'w');
					@fwrite($f, '');
					@fclose($f);
					@chmod($file, 0644);
				}
				$upload_path .= cdate('m') . '/';
				if ( ! is_dir($upload_path)) {
					mkdir($upload_path, 0707);
					$file = $upload_path . 'index.php';
					$f = @fopen($file, 'w');
					@fwrite($f, '');
					@fclose($f);
					@chmod($file, 0644);
				}

				$uploadconfig = '';
				$uploadconfig['upload_path'] = $upload_path;
				$uploadconfig['allowed_types'] = 'jpg|jpeg|png|gif';
				$uploadconfig['max_size']	= '2000';
				$uploadconfig['max_width']  = '1000';
				$uploadconfig['max_height']  = '1000';
				$uploadconfig['encrypt_name'] = TRUE;

				$this->upload->initialize($uploadconfig);

				if ($this->upload->do_upload('mem_photo')) {
					$img = $this->upload->data();
					$updatephoto = cdate('Y') . '/' . cdate('m') . '/' . element('file_name', $img);
				} else {
					$file_error = $this->upload->display_errors();

				}
			}

			if ( isset($_FILES) && isset($_FILES['mem_icon']) && isset($_FILES['mem_icon']['name']) && $_FILES['mem_icon']['name']) {
				$upload_path = './uploads/member_icon/';
				if ( ! is_dir($upload_path)) {
					mkdir($upload_path, 0707);
					$file = $upload_path . 'index.php';
					$f = @fopen($file, 'w');
					@fwrite($f, '');
					@fclose($f);
					@chmod($file, 0644);
				}
				$upload_path .= cdate('Y') . '/';
				if ( ! is_dir($upload_path)) {
					mkdir($upload_path, 0707);
					$file = $upload_path . 'index.php';
					$f = @fopen($file, 'w');
					@fwrite($f, '');
					@fclose($f);
					@chmod($file, 0644);
				}
				$upload_path .= cdate('m') . '/';
				if ( ! is_dir($upload_path)) {
					mkdir($upload_path, 0707);
					$file = $upload_path . 'index.php';
					$f = @fopen($file, 'w');
					@fwrite($f, '');
					@fclose($f);
					@chmod($file, 0644);
				}
				$uploadconfig = '';
				$uploadconfig['upload_path'] = $upload_path;
				$uploadconfig['allowed_types'] = 'jpg|jpeg|png|gif';
				$uploadconfig['max_size']	= '2000';
				$uploadconfig['max_width']  = '1000';
				$uploadconfig['max_height']  = '1000';
				$uploadconfig['encrypt_name'] = TRUE;

				$this->upload->initialize($uploadconfig);

				if ($this->upload->do_upload('mem_icon')) {
					$img = $this->upload->data();
					$updateicon = cdate('Y') . '/' . cdate('m') . '/' . element('file_name', $img);
				} else {
					$file_error2 = $this->upload->display_errors();

				}
			}
		}

		/**
		* 유효성 검사를 하지 않는 경우, 또는 유효성 검사에 실패한 경우입니다. 즉 글쓰기나 수정 페이지를 보고 있는 경우입니다
		*/
		if ($form_validation == FALSE OR $file_error != '' OR $file_error2 != '') {

			// 이벤트가 존재하면 실행합니다
			$view['view']['event']['formrunfalse'] = Events::trigger('formrunfalse', $eventname);

			$view['view']['message'] = $file_error . $file_error2;

			$view['view']['data'] = $getdata;

			if ( ! $pid) {
				$view['view']['data']['mem_receive_email'] = 1;
				$view['view']['data']['mem_use_note'] = 1;
				$view['view']['data']['mem_receive_sms'] = 1;
				$view['view']['data']['mem_open_profile'] = 1;
			}

			/**
			* primary key 정보를 저장합니다
			*/
			$view['view']['primary_key'] = $primary_key;

			$html_content = '';
			$k = 0;
			if ($form && is_array($form)) {
				foreach ($form as $key => $value) {
					if ( ! element('use', $value)) continue;
					if (element('func', $value) == 'basic') continue;
					$required = element('required', $value) ? 'required' : '';

					$item = element(element('field_name', $value), element('extras', $getdata));
					$html_content[$k]['field_name'] = element('field_name', $value);
					$html_content[$k]['display_name'] = element('display_name', $value);
					$html_content[$k]['input'] = '';

					//field_type : text, url, email, phone, textarea, radio, select, checkbox, date
					if (element('field_type', $value) == 'text' OR element('field_type', $value) == 'url' OR element('field_type', $value) == 'email' OR element('field_type', $value) == 'phone' OR element('field_type', $value) == 'date') {
						if (element('field_type', $value) == 'date') {
							$html_content[$k]['input'] .= '<input type="text" id="' . element('field_name', $value) . '" name="' . element('field_name', $value) . '" class="form-control datepicker"  value="' . set_value(element('field_name', $value), $item) . '" readonly="readonly" ' . $required . '  />';
						} else if (element('field_type', $value) == 'phone') {
							$html_content[$k]['input'] .= '<input type="text" id="' . element('field_name', $value) . '" name="' . element('field_name', $value) . '" class="form-control validphone" value="' . set_value(element('field_name', $value), $item) . '" ' . $required . '  />';
						} else {
							$readonly='';
							if (element('field_name', $value) == 'mem_nickname' && $can_update_nickname == FALSE) {
								$readonly='readonly="readonly"';
							}
							$html_content[$k]['input'] .= '<input type="' . element('field_type', $value) . '" id="' . element('field_name', $value) . '" name="' . element('field_name', $value) . '" class="form-control" value="' . set_value(element('field_name', $value), $item) . '" ' . $readonly . ' ' . $required . ' />';
						}
					} else if (element('field_type', $value) == 'textarea') {
						$html_content[$k]['input'] .= '<textarea id="' . element('field_name', $value) . '" name="' . element('field_name', $value) . '" class="form-control" ' . $required . ' >' . set_value(element('field_name', $value), $item) . '</textarea>';
					} else if (element('field_type', $value) == 'radio') {
						$html_content[$k]['input'] .= '<div class="checkbox">';
						$options = explode("\n", element('options', $value));
						$i=1;
						if ($options) {
							foreach ($options as $okey => $oval) {
								$oval = trim($oval);
								$radiovalue = (element('field_name', $value) == 'mem_sex') ? $okey : $oval;
								$html_content[$k]['input'] .= '<label for="' . element('field_name', $value) . '_' . $i . '"><input type="radio" name="' . element('field_name', $value) . '" id="' . element('field_name', $value) . '_' . $i . '" value="' . $radiovalue . '"  ' . set_radio(element('field_name', $value) , $radiovalue, ($item == $radiovalue?TRUE:FALSE)) . ' /> ' . $oval . ' </label> ';
							$i++;
							}
						}
						$html_content[$k]['input'] .= '</div>';
					} else if (element('field_type', $value) == 'checkbox') {
						$html_content[$k]['input'] .= '<div class="checkbox">';
						$options = explode("\n", element('options', $value));
						$item = json_decode($item, TRUE);
						$i=1;
						if ($options) {
							foreach ($options as $okey => $oval) {
								$oval = trim($oval);
								$chkvalue = is_array($item) && in_array($oval , $item) ? $oval : '';
								$html_content[$k]['input'] .= '<label for="' . element('field_name', $value) . '_' . $i . '"><input type="checkbox" name="' . element('field_name', $value) . '[]" id="' . element('field_name', $value) . '_' . $i . '" value="' . $oval . '"  ' . set_checkbox(element('field_name', $value) , $oval, ($chkvalue?TRUE:FALSE)) . ' /> ' . $oval . ' </label> ';
							$i++;
							}
						}
						$html_content[$k]['input'] .= '</div>';
					} else if (element('field_type', $value) == 'select') {
						$html_content[$k]['input'] .= '<div class="input-group">';
						$html_content[$k]['input'] .= '<select name="' . element('field_name', $value) . '" class="form-control" ' . $required . '>';
						$html_content[$k]['input'] .= '<option value=""  >선택하세요</option> ';
						$options = explode("\n", element('options', $value));
						if ($options) {
							foreach ($options as $okey => $oval) {
								$oval = trim($oval);
								$html_content[$k]['input'] .= '<option value="' . $oval . '"  ' . set_select(element('field_name', $value) , $oval, ($item == $oval?TRUE:FALSE)) . ' >' .  $oval . '</option> ';
							}
						}
						$html_content[$k]['input'] .= '</select>';
						$html_content[$k]['input'] .= '</div>';
					}
					$k++;
				}
			}

			$view['view']['html_content'] = $html_content;

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

		} else {
			/**
			* 유효성 검사를 통과한 경우입니다. 즉 데이터의 insert 나 update 의 process 처리가 필요한 상황입니다
			*/

			// 이벤트가 존재하면 실행합니다
			$view['view']['event']['formruntrue'] = Events::trigger('formruntrue', $eventname);

			$updatedata = array(
				'mem_userid' => $this->input->post('mem_userid'),
				'mem_email' => $this->input->post('mem_email'),
				'mem_username' => $this->input->post('mem_username'),
				'mem_level' => $this->input->post('mem_level'),
				'mem_homepage' => $this->input->post('mem_homepage'),
				'mem_birthday' => $this->input->post('mem_birthday'),
				'mem_phone' => $this->input->post('mem_phone'),
				'mem_sex' => $this->input->post('mem_sex'),
				'mem_zipcode' => $this->input->post('mem_zipcode'),
				'mem_address1' => $this->input->post('mem_address1'),
				'mem_address2' => $this->input->post('mem_address2'),
				'mem_address3' => $this->input->post('mem_address3'),
				'mem_address4' => $this->input->post('mem_address4'),
				'mem_receive_email' => $this->input->post('mem_receive_email'),
				'mem_use_note' => $this->input->post('mem_use_note'),
				'mem_receive_sms' => $this->input->post('mem_receive_sms'),
				'mem_open_profile' => $this->input->post('mem_open_profile'),
				'mem_denied' => $this->input->post('mem_denied'),
				'mem_email_cert' => $this->input->post('mem_email_cert'),
				'mem_is_admin' => $this->input->post('mem_is_admin'),
				'mem_profile_content' => $this->input->post('mem_profile_content'),
				'mem_adminmemo' => $this->input->post('mem_adminmemo'),
			);

			$metadata = array();

			if ( ! isset($getdata['mem_denied']) OR $getdata['mem_denied'] != $this->input->post('mem_denied')) {
				if ( ( ! isset($getdata['mem_denied']) OR ! $getdata['mem_denied']) && $this->input->post('mem_denied')) {
					$metadata['meta_denied_datetime'] = cdate('Y-m-d H:i:s');
					$metadata['meta_denied_by_mem_id'] = $this->member->item('mem_id');
				}
			}
			if (isset($getdata['mem_denied']) && $getdata['mem_denied'] && ! $this->input->post('mem_denied')) {
					$metadata['meta_denied_datetime'] = '';
					$metadata['meta_denied_by_mem_id'] = '';
			}
			if ( ! isset($getdata['mem_email_cert']) OR $getdata['mem_email_cert'] != $this->input->post('mem_email_cert')) {
				if ( ( ! isset($getdata['mem_email_cert']) OR ! $getdata['mem_email_cert']) && $this->input->post('mem_email_cert')) {
					$metadata['meta_email_cert_datetime'] = cdate('Y-m-d H:i:s');
				}
			}
			if (isset($getdata['mem_email_cert']) && $getdata['mem_email_cert'] && ! $this->input->post('mem_email_cert')) {
					$metadata['meta_email_cert_datetime'] = '';
			}
			if (element('mem_nickname', $getdata) != $this->input->post('mem_nickname')) {
				$updatedata['mem_nickname'] =$this->input->post('mem_nickname');
				$metadata['meta_nickname_datetime'] = cdate('Y-m-d H:i:s');
			}
			if ($this->input->post('mem_password')) {
				$updatedata['mem_password'] = password_hash($this->input->post('mem_password'), PASSWORD_BCRYPT);
			}

			if ($this->input->post('mem_photo_del')) {
				$updatedata['mem_photo'] = '';
			} else if ($updatephoto) {
				$updatedata['mem_photo'] = $updatephoto;
			}
			if (element('mem_photo', $getdata) && ($this->input->post('mem_photo_del') OR $updatephoto)) {
				// 기존 파일 삭제
				 @unlink('./uploads/member_photo/' . element('mem_photo', $getdata));
			}
			if ($this->input->post('mem_icon_del')) {
				$updatedata['mem_icon'] = '';
			} else if ($updateicon) {
				$updatedata['mem_icon'] = $updateicon;
			}
			if (element('mem_icon', $getdata) && ($this->input->post('mem_icon_del') OR $updateicon)) {
				// 기존 파일 삭제
				 @unlink('./uploads/member_icon/' . element('mem_icon', $getdata));
			}

			/**
			* 게시물을 수정하는 경우입니다
			*/
			if ($this->input->post($primary_key)) {
				$mem_id = $this->input->post($primary_key);
				$this->{$this->modelname}->update($mem_id , $updatedata);
				$this->Member_meta_model->save($mem_id , $metadata);
				if ( ! element('mem_nickname', $getdata) OR element('mem_nickname', $getdata) != $this->input->post('mem_nickname')) {
					$upnick = array(
						'mni_end_datetime' => cdate('Y-m-d H:i:s'),
					);
					$this->Member_nickname_model->update('', $upnick, array('mem_id' => $mem_id));

					$nickinsert = array(
						'mem_id' => $mem_id,
						'mni_nickname' => $this->input->post('mem_nickname'),
						'mni_start_datetime' => cdate('Y-m-d H:i:s'),
					);
					$this->Member_nickname_model->insert($nickinsert);
				}

				$extradata = array();
				if ($form && is_array($form)) {
					foreach ($form as $key => $value) {
						if ( ! element('use', $value)) continue;
						if (element('func', $value) == 'basic') continue;
						$extradata[element('field_name', $value)] = $this->input->post(element('field_name', $value));
					}
					$this->Member_extra_vars_model->save($mem_id, $extradata);
				}

				$this->session->set_flashdata('message', '정상적으로 수정되었습니다');
			} else {
				/**
				* 게시물을 새로 입력하는 경우입니다
				*/
				$updatedata['mem_register_datetime'] = cdate('Y-m-d H:i:s');
				$updatedata['mem_register_ip'] = $this->input->ip_address();

				$mem_id = $this->{$this->modelname}->insert($updatedata);
				$this->Member_meta_model->save($mem_id, $metadata);
				$nickinsert = array(
					'mem_id' => $mem_id,
					'mni_nickname' => $this->input->post('mem_nickname'),
					'mni_start_datetime' => cdate('Y-m-d H:i:s'),
				);
				$this->Member_nickname_model->insert($nickinsert);

				$extradata = array();
				if ($form && is_array($form)) {
					foreach ($form as $key => $value) {
						if ( ! element('use', $value)) continue;
						if (element('func', $value) == 'basic') continue;
						$extradata[element('field_name', $value)] = $this->input->post(element('field_name', $value));
					}
					$this->Member_extra_vars_model->save($mem_id, $extradata);
				}

				$this->session->set_flashdata('message', '정상적으로 입력되었습니다');
			}

			// 이벤트가 존재하면 실행합니다
			Events::trigger('after', $eventname);

			/**
			* 게시물의 신규입력 또는 수정작업이 끝난 후 목록 페이지로 이동합니다
			*/
			$param =& $this->querystring;
			$redirecturl = admin_url($this->pagedir . '?' .  $param->output());
			redirect($redirecturl);

		}
	}

	/**
	* 목록 페이지에서 선택삭제를 하는 경우 실행되는 메소드입니다
	*/
	public function listdelete()
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_admin_member_members_listdelete';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		/**
		* 체크한 게시물의 삭제를 실행합니다
		*/
		
		$this->load->model(array('Board_admin_model', 'Board_group_admin_model', 'Member_auth_email_model', 'Member_extra_vars_model', 'Member_login_log_model', 'Member_meta_model', 'Member_register_model', 'Notification_model', 'Point_model', 'Scrap_model'));

		if ($this->input->post('chk') && is_array($this->input->post('chk'))) {
			foreach ($this->input->post('chk') as $val) {
				if ($val) {
					$where = array('mem_id' => $val);
					$this->Member_model->delete($val);
					$this->Board_admin_model->delete('', $where);
					$this->Board_group_admin_model->delete('', $where);
					$this->Member_auth_email_model->delete('', $where);
					$this->Member_extra_vars_model->delete('', $where);
					$this->Member_login_log_model->delete('', $where);
					$this->Member_meta_model->delete('', $where);
					$this->Member_register_model->delete('', $where);
					$this->Notification_model->delete('', $where);
					$this->Point_model->delete('', $where);
					$this->Scrap_model->delete('', $where);
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
	* 회원아이디 체크함수입니다
	*/
	public function _mem_userid_check($str)
	{
		if (preg_match("/[\,]?{$str}/i", $this->cbconfig->item('prohibit_id'))) {
			$this->form_validation->set_message('_mem_userid_check', $str . ' 은(는) 예약어로 사용하실 수 없는 회원아이디입니다');
			return FALSE;
		}

		return TRUE;
	}

	/**
	* 회원닉네임 체크함수입니다
	*/
	public function _mem_nickname_check($str)
	{
		if ( ! chkstring($str, _HANGUL_ + _ALPHABETIC_ + _NUMERIC_)) {
			$this->form_validation->set_message('_mem_nickname_check', '닉네임은 공백없이 한글, 영문, 숫자만 입력 가능합니다');
			return FALSE;
		}

		return TRUE;
	}

}
