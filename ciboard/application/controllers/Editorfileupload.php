<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Editorfileupload class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

/**
 * 에디터를 통해 파일을 업로드하는 controller 입니다.
 */
class Editorfileupload extends CB_Controller
{

	/**
	*  모델을 로딩합니다
	*/
	protected $models = array('Editor_image');

	/**
	*  헬퍼를 로딩합니다
	*/
	protected $helpers = array('array');

	function __construct()
	{
		parent::__construct();

		$this->load->library('upload');
		

	}

	/**
	  * 스마트 에디터를 통해 이미지를 업로드하는 컨트롤러입니다.
	  */
	public function smarteditor()
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_editorfileupload_smarteditor';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		$this->_init();


		if ( isset($_FILES) && isset($_FILES['files']) && isset($_FILES['files']['name']) && isset($_FILES['files']['name'][0])) {

			$upload_path = './uploads/editor/' . cdate('Y') . '/' . cdate('m') . '/';

			$uploadconfig = array(
				'upload_path' => $upload_path,
				'allowed_types' => 'jpg|jpeg|png|gif',
				'max_size'	=> 10 * 1024,
				'encrypt_name' => TRUE,
			);
			
			$this->upload->initialize($uploadconfig);
			$_FILES['userfile']['name']= $_FILES['files']['name'][0];
			$_FILES['userfile']['type']= $_FILES['files']['type'][0];
			$_FILES['userfile']['tmp_name']= $_FILES['files']['tmp_name'][0];
			$_FILES['userfile']['error']= $_FILES['files']['error'][0];
			$_FILES['userfile']['size']= $_FILES['files']['size'][0];
			
			if ($this->upload->do_upload()) {

				// 이벤트가 존재하면 실행합니다
				Events::trigger('doupload', $eventname);

				$filedata = $this->upload->data();
				$fileupdate = array(
					'mem_id' => $this->member->item('mem_id'),
					'eim_originname' => element('orig_name', $filedata),
					'eim_filename' => cdate('Y') . '/' . cdate('m') . '/' . element('file_name', $filedata),
					'eim_filesize' => intval(element('file_size', $filedata) * 1024),
					'eim_width' => element('image_width', $filedata) ? element('image_width', $filedata) : 0,
					'eim_height' => element('image_height', $filedata) ? element('image_height', $filedata) : 0,
					'eim_type' => str_replace('.', '', element('file_ext', $filedata)),
					'eim_datetime' => cdate('Y-m-d H:i:s'),
					'eim_ip' => $this->input->ip_address(),
				);
				$image_id = $this->Editor_image_model->insert($fileupdate);
				
				$image_url = site_url('uploads/editor/' . cdate('Y') . '/' . cdate('m') . '/' . element('file_name', $filedata));
				$info = new stdClass();
				$info->oriname = element('orig_name', $filedata);
				$info->name = element('file_name', $filedata);
				$info->size = intval(element('file_size', $filedata) * 1024);
				$info->type = 'image/' . str_replace('.', '', element('file_ext', $filedata));
				$info->url = $image_url;
				$info->width = element('image_width', $filedata) ? element('image_width', $filedata) : 0;
				$info->height = element('image_height', $filedata) ? element('image_height', $filedata) : 0;

				$return['files'][0] = $info;

				// 이벤트가 존재하면 실행합니다
				Events::trigger('doupload_after', $eventname);

				exit(json_encode($return));

			} else {
				exit($this->upload->display_errors());
			}
		} else if($this->input->get('file') && $this->member->item('mem_id')){

			// 이벤트가 존재하면 실행합니다
			Events::trigger('delete_before', $eventname);

			$where = array(
				'mem_id' => 	$this->member->item('mem_id'),
				'eim_filename' => cdate('Y') . '/' . cdate('m') . '/' . $this->input->get('file'),
				'eim_ip' => $this->input->ip_address(),
			);
			$image = $this->Editor_image_model->get_one('','',$where);
			if(element('eim_filename', $image)) {

				// 이벤트가 존재하면 실행합니다
				Events::trigger('delete_after', $eventname);

				unlink( $upload_path . $this->input->get('file'));
				$this->Editor_image_model->delete('',$where);

			}
		}

	}

	/**
	  * CK 에디터를 통해 이미지를 업로드하는 컨트롤러입니다.
	  */
	public function ckeditor()
	{

		// 이벤트 라이브러리를 로딩합니다
		$eventname = 'event_editorfileupload_ckeditor';
		$this->load->event($eventname);

		// 이벤트가 존재하면 실행합니다
		Events::trigger('before', $eventname);

		$this->_init();

		$upload_path = './uploads/editor/' . cdate('Y') . '/' . cdate('m') . '/';

		$uploadconfig = array(
			'upload_path' => $upload_path,
			'allowed_types' => 'jpg|jpeg|png|gif',
			'max_size'	=> 10 * 1024,
			'encrypt_name' => TRUE,
		);

		if ( isset($_FILES) && isset($_FILES['upload']) && isset($_FILES['upload']['name'])) {

			$this->upload->initialize($uploadconfig);
			$_FILES['userfile']['name']= $_FILES['upload']['name'];
			$_FILES['userfile']['type']= $_FILES['upload']['type'];
			$_FILES['userfile']['tmp_name']= $_FILES['upload']['tmp_name'];
			$_FILES['userfile']['error']= $_FILES['upload']['error'];
			$_FILES['userfile']['size']= $_FILES['upload']['size'];
			
			if ($this->upload->do_upload()) {

				// 이벤트가 존재하면 실행합니다
				Events::trigger('doupload', $eventname);

				$filedata = $this->upload->data();
				$fileupdate = array(
					'mem_id' => $this->member->item('mem_id'),
					'eim_originname' => element('orig_name', $filedata),
					'eim_filename' => cdate('Y') . '/' . cdate('m') . '/' . element('file_name', $filedata),
					'eim_filesize' => intval(element('file_size', $filedata) * 1024),
					'eim_width' => element('image_width', $filedata) ? element('image_width', $filedata) : 0,
					'eim_height' => element('image_height', $filedata) ? element('image_height', $filedata) : 0,
					'eim_type' => str_replace('.', '', element('file_ext', $filedata)),
					'eim_datetime' => cdate('Y-m-d H:i:s'),
					'eim_ip' => $this->input->ip_address(),
				);
				$this->Editor_image_model->insert($fileupdate);
				$image_url = site_url('uploads/editor/' . cdate('Y') . '/' . cdate('m') . '/' . element('file_name', $filedata));

				// 이벤트가 존재하면 실행합니다
				Events::trigger('doupload_after', $eventname);

				echo "<script>window.parent.CKEDITOR.tools.callFunction(" . $this->input->get('CKEditorFuncNum') . ", '" . $image_url . "', '업로드완료');</script>";
			} else {
				echo $this->upload->display_errors();
			}
		}
		
	}

	public function _init() {
	
		$upload_path = './uploads/editor/';
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

	}
}
