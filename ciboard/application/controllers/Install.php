<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Login class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

/**
 * 로그인 페이지와 관련된 controller 입니다.
 */
class Install extends CI_Controller
{

    /**
     * 모델을 로딩합니다
     */
    protected $models = array();


    function __construct()
    {
        parent::__construct();

        $this->load->helper(array('array', 'form'));

        ini_set('display_errors', 0);
        if (version_compare(PHP_VERSION, '5.3', '>=')) {
            error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
        } else {
            error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
        }
    }


    /**
     * 인스톨 페이지입니다
     */
    public function index()
    {
        if (config_item('install_ip') === '' OR config_item('install_ip') !== $this->input->ip_address()) {
            $header = array();
            $header['install_step'] = 0;
            $this->load->view('install/header', $header);
            $this->load->view('install/step0');
            $this->load->view('install/footer');
            return;
        }

        if ($this->_check_installed()) {
            alert('이미 데이터베이스에 config 테이블이 존재하여 설치를 진행하지 않습니다');
        }
        redirect('install/step1');
    }


    public function step1()
    {
        if (config_item('install_ip') === '' OR config_item('install_ip') !== $this->input->ip_address()) {
            $header = array();
            $header['install_step'] = 0;
            $this->load->view('install/header', $header);
            $this->load->view('install/step0');
            $this->load->view('install/footer');
            return;
        }

        if ($this->_check_installed()) {
            alert('이미 데이터베이스에 config 테이블이 존재하여 설치를 진행하지 않습니다');
        }

        $header = array();
        $header['install_step'] = 1;
        $this->load->view('install/header', $header);
        $this->load->view('install/step1');
        $this->load->view('install/footer');
    }


    public function step2()
    {
        if (config_item('install_ip') === '' OR config_item('install_ip') !== $this->input->ip_address()) {
            $header = array();
            $header['install_step'] = 0;
            $this->load->view('install/header', $header);
            $this->load->view('install/step0');
            $this->load->view('install/footer');
            return;
        }

        if ($this->_check_installed()) {
            alert('이미 데이터베이스에 config 테이블이 존재하여 설치를 진행하지 않습니다');
        }
        if ( ! $this->input->post('agree')) {
            alert('약관에 동의하신 후에 설치가 가능합니다', site_url('install'));
            return;
        }

        $view = array();
        $header = array();
        $message = '';

        $install_avaiable = true;
        $view['title1'] = 'PHP VERSION';
        $phpversion = phpversion();
        if (version_compare($phpversion, '5.2.4') >= 0) {
            $view['content1'] = '<span class="bold color_blue">' . $phpversion . '</span>';
        } else {
            $view['content1'] = '<span class="bold color_red">' . $phpversion . '</span>';
            $install_avaiable = false;
            $message .= 'PHP Version 을 5.2.4 이상으로 업그레이드 한 후에 설치가 가능합니다<br />';
        }
        $view['desc1'] = 'PHP version 5.2.4 or newer is recommended.';

        $view['title2'] = 'GD Support';
        if (extension_loaded('gd') && function_exists('gd_info')) {
            $view['content2'] = '<span class="bold color_blue">지원</span>';
        } else {
            $view['content2'] = '<span class="bold color_red">미지원</span>';
            $install_avaiable = false;
            $message .= 'GD Library 를 설치한 후에 Install 이 가능합니다<br />';
        }

        $view['title3'] = 'XML Support';
        $xml_support = extension_loaded('xml');
        if ($xml_support) {
            $view['content3'] = '<span class="bold color_blue">지원</span>';
        } else {
            $view['content3'] = '<span class="bold color_red">미지원</span>';
            $install_avaiable = false;
            $message .= 'XML Library 를 설치한 후에 Install 이 가능합니다<br />';
        }

        $view['title4'] = 'iconv Support';
        $iconv = function_exists('iconv');
        if ($iconv) {
            $view['content4'] = '<span class="bold color_blue">지원</span>';
        } else {
            $view['content4'] = '<span class="bold color_red">미지원</span>';
            $install_avaiable = false;
            $message .= 'Iconv Library 를 설치한 후에 Install 이 가능합니다<br />';
        }

        $view['title5'] = 'CURL Support';
        $curl = function_exists('curl_version');
        if ($curl) {
            $view['content5'] = '<span class="bold color_blue">지원</span>';
        } else {
            $view['content5'] = '<span class="bold color_red">미지원</span>';
            $install_avaiable = false;
            $message .= 'CURL Extension 을 설치한 후에 Install 이 가능합니다<br />';
        }

        $view['title6'] = 'uploads directory';
        $uploads_dir = './uploads';
        if (is_dir($uploads_dir) === false) {
            $install_avaiable = false;
            $view['content6'] = '<span class="bold color_red">루트 디렉토리에 uploads 라는 디렉토리가 존재하지 않습니다</span>';
            $message .= '루트 디렉토리에서 uploads 라는 디렉토리를 생성하시고 그 퍼미션을 707 로 변경해주세요<br />';
        } elseif ( ! (is_readable($uploads_dir) && is_writeable($uploads_dir))) {
            $install_avaiable = false;
            $view['content6'] = '<span class="bold color_red">uploads 디렉토리의 퍼미션이 올바르지 않습니다</span>';
            $message .= 'uploads 라는 디렉토리의 퍼미션을 707 로 변경해주세요<br />';
        } else {
            $view['content6'] = '<span class="bold color_blue"> 정상</span>';
        }

        $view['title7'] = 'cache directory';
        $cache_dir = APPPATH . 'cache';
        if (is_dir($cache_dir) === false) {
            $install_avaiable = false;
            $view['content7'] = '<span class="bold color_red">application 디렉토리에 cache 라는 디렉토리가 존재하지 않습니다</span>';
            $message .= 'application/cache 를 생성해주시고 그 퍼미션을 707 로 변경해주세요<br />';
        } elseif ( ! (is_readable($cache_dir) && is_writeable($cache_dir))) {
            $install_avaiable = false;
            $view['content7'] = '<span class="bold color_red">application/cache 디렉토리의 퍼미션이 올바르지 않습니다</span>';
            $message .= 'application/cache 디렉토리의 퍼미션을 707 로 변경해주세요<br />';
        } else {
            $view['content7'] = '<span class="bold color_blue"> 정상</span>';
        }

        $view['title8'] = 'logs directory';
        $log_dir = './application/logs';
        if (is_dir($log_dir) === false) {
            $install_avaiable = false;
            $view['content8'] = '<span class="bold color_red">application 디렉토리에 logs 라는 디렉토리가 존재하지 않습니다</span>';
            $message .= 'application/logs 를 생성해주시고 그 퍼미션을 707 로 변경해주세요<br />';
        } elseif ( ! (is_readable($log_dir) && is_writeable($log_dir))) {
            $install_avaiable = false;
            $view['content8'] = '<span class="bold color_red">application/logs 디렉토리의 퍼미션이 올바르지 않습니다</span>';
            $message .= 'application/logs 디렉토리의 퍼미션을 707 로 변경해주세요<br />';
        } else {
            $view['content8'] = '<span class="bold color_blue"> 정상</span>';
        }

        $view['title9'] = 'migration_enabled';
        $this->config->load('migration');
        if (config_item('migration_enabled') === false) {
            $install_avaiable = false;
            $view['content9'] = '<span class="bold color_red">config[\'migration_enabled\'] 가 활성화되어있지 않습니다</span>';
            $message .= 'application/config/migration.php 안에 config[\'migration_enabled\'] 의 값을 true 로 변경해주세요<br />';
        } else {
            $view['content9'] = '<span class="bold color_blue"> 정상</span>';
        }

        $view['install_avaiable'] = $install_avaiable;
        $view['message'] = $message;

        $header['install_step'] = 2;
        $this->load->view('install/header', $header);
        $this->load->view('install/step2', $view);
        $this->load->view('install/footer');
    }


    public function step3()
    {
        if (config_item('install_ip') === '' OR config_item('install_ip') !== $this->input->ip_address()) {
            $header = array();
            $header['install_step'] = 0;
            $this->load->view('install/header', $header);
            $this->load->view('install/step0');
            $this->load->view('install/footer');
            return;
        }

        if ($this->_check_installed()) {
            alert('이미 데이터베이스에 config 테이블이 존재하여 설치를 진행하지 않습니다');
        }
        if ( ! $this->input->post('agree')) {
            alert('약관에 동의하신 후에 설치가 가능합니다', site_url('install'));
            return;
        }

        $view = array();
        $header = array();
        $message = '';

        $install_avaiable = true;

        $view['title1'] = 'encryption_key';
        if (config_item('encryption_key')) {
            $view['content1'] = '<span class="bold color_blue">설정완료</span>';
        } else {
            $install_avaiable = false;
            $view['content1'] = '<span class="bold color_red">비어있음</span>';
            $message .= 'application/config/config.php 의 &dollar;config[\'encryption_key\'] 에 내용을 입력해주세요, 현재 그 값이 비어있습니다. 한번 입력하신 값은 변경하지 말아주세요. 패스워드 암호화에 사용됩니다<br />';
        }

        $view['title2'] = 'base_url';
        if (config_item('base_url')) {
            $view['content2'] = '<span class="bold color_blue">설정완료</span>';
        } else {
            $install_avaiable = false;
            $view['content2'] = '<span class="bold color_red">비어있음</span>';
            $message .= 'application/config/config.php 의 &dollar;config[\'base_url\'] 에 현재 사이트 주소를 입력해주세요, 현재 그 값이 비어있습니다<br />';
        }

        $view['install_avaiable'] = $install_avaiable;
        $view['message'] = $message;

        $header['install_step'] = 3;
        $this->load->view('install/header', $header);
        $this->load->view('install/step3', $view);
        $this->load->view('install/footer');
    }


    public function step4()
    {
        if (config_item('install_ip') === '' OR config_item('install_ip') !== $this->input->ip_address()) {
            $header = array();
            $header['install_step'] = 0;
            $this->load->view('install/header', $header);
            $this->load->view('install/step0');
            $this->load->view('install/footer');
            return;
        }

        if ($this->_check_installed()) {
            alert('이미 데이터베이스에 config 테이블이 존재하여 설치를 진행하지 않습니다');
        }
        if ( ! $this->input->post('agree')) {
            alert('약관에 동의하신 후에 설치가 가능합니다', site_url('install'));
            return;
        }

        $view = array();
        $header = array();
        $message = '';

        include(APPPATH . 'config/database.php');

        $install_avaiable = true;
        $dbinfo = $db['default'];

        $view['title2'] = 'username';
        if ( ! empty($dbinfo['username'])) {
            $view['content2'] = '<span class="bold color_blue">설정완료</span>';
        } else {
            $install_avaiable = false;
            $view['content2'] = '<span class="bold color_red">비어있음</span>';
            $message .= 'application/config/database.php 의 &dollar;db[\'default\'][\'username\'] 에 데이터베이스 정보를 입력해주세요<br />';
        }

        $view['title3'] = 'password';
        if ( ! empty($dbinfo['password'])) {
            $view['content3'] = '<span class="bold color_blue">설정완료</span>';
        } else {
            $install_avaiable = false;
            $view['content3'] = '<span class="bold color_red">비어있음</span>';
            $message .= 'application/config/database.php 의 &dollar;db[\'default\'][\'password\'] 에 데이터베이스 정보를 입력해주세요<br />';
        }

        $view['title4'] = 'database';
        if ( ! empty($dbinfo['database'])) {
            $view['content4'] = '<span class="bold color_blue">설정완료</span>';
        } else {
            $install_avaiable = false;
            $view['content4'] = '<span class="bold color_red">비어있음</span>';
            $message .= 'application/config/database.php 의 &dollar;db[\'default\'][\'database\'] 에 데이터베이스 정보를 입력해주세요<br />';
        }
        $view['title5'] = 'db connect';
        $view['content5'] = '';
        if ($install_avaiable) {
            $database = $this->load->database($dbinfo, true);
            $connected = $database->initialize();
            if ($connected) {
                $view['content5'] = '<span class="bold color_blue">데이터베이스 접속 성공</span>';
            } else {
                $install_avaiable = false;
                $view['content5'] = '<span class="bold color_red">데이터베이스 접속 실패</span>';
                $message .= 'application/config/database.php 의 데이터베이스 정보가 올바르게 입력되었는지 확인해주세요<br />';
            }
        }

        $view['install_avaiable'] = $install_avaiable;
        $view['message'] = $message;

        $header['install_step'] = 4;
        $this->load->view('install/header', $header);
        $this->load->view('install/step4', $view);
        $this->load->view('install/footer');
    }


    public function step5()
    {
        if (config_item('install_ip') === '' OR config_item('install_ip') !== $this->input->ip_address()) {
            $header = array();
            $header['install_step'] = 0;
            $this->load->view('install/header', $header);
            $this->load->view('install/step0');
            $this->load->view('install/footer');
            return;
        }

        if ($this->_check_installed()) {
            alert('이미 데이터베이스에 config 테이블이 존재하여 설치를 진행하지 않습니다');
        }
        if ( ! $this->input->post('agree')) {
            alert('약관에 동의하신 후에 설치가 가능합니다', site_url('install'));
            return;
        }

        $view = array();
        $header = array();

        $config = array();
        $config['mem_userid'] = array(
            'field' => 'mem_userid',
            'label' => 'User ID',
            'rules' => 'trim|required|alphanumunder|min_length[3]|max_length[20]',
        );
        $config['mem_password'] = array(
            'field' => 'mem_password',
            'label' => '패스워드',
            'rules' => 'trim|required|min_length[4]',
        );
        $config['mem_password_re'] = array(
            'field' => 'mem_password_re',
            'label' => '패스워드 확인',
            'rules' => 'trim|required|min_length[4]|matches[mem_password]',
        );
        $config['mem_nickname'] = array(
            'field' => 'mem_nickname',
            'label' => '닉네임',
            'rules' => 'trim|required|min_length[2]|max_length[20]|callback__mem_nickname_check',
        );
        $config['mem_email'] = array(
            'field' => 'mem_email',
            'label' => '이메일',
            'rules' => 'trim|required|valid_email|max_length[50]',
        );
        $config['skin'] = array(
            'field' => 'skin',
            'label' => '스킨',
            'rules' => 'trim|required',
        );
        $form_validation = '';
        if ($this->input->post('mem_userid')) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules($config);
            $form_validation = $this->form_validation->run();
        }
        $this->load->database();
        if ($form_validation) {
            if ($this->_create_tables() === false) {
                alert('설치에 실패하였습니다', site_url('install'));
                return;
            }
            if ($this->_migration_tables() === false) {
                alert('설치에 실패하였습니다', site_url('install'));
                return;
            }
            $this->_insert_init_data();

            redirect();
        } else {
            $header['install_step'] = 5;
            $this->load->view('install/header', $header);
            $this->load->view('install/step5', $view);
            $this->load->view('install/footer');
        }
    }


    public function _check_installed()
    {
        include(APPPATH . 'config/database.php');
        $dbinfo = $db['default'];

        if (empty($dbinfo['username'])) {
            return false;
        }
        if (empty($dbinfo['password'])) {
            return false;
        }
        if (empty($dbinfo['database'])) {
            return false;
        }
        $database = $this->load->database($dbinfo, true);
        if (empty($database->conn_id)) {
            return false;
        }
        $connected = $database->initialize();
        if ($connected) {
            $this->load->database();
            if ($this->db->table_exists('config')) {
                return true;
            }
        }
        return false;

    }


    public function _create_tables()
    {
        $this->load->database();
        $this->load->dbforge();


        // autologin table
        $this->dbforge->add_field(array(
            'aul_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ),
            'mem_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'aul_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'aul_ip' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => '',
            ),
            'aul_datetime' => array(
                'type' => 'DATETIME',
                'null' => true,
        ),
        ));
        $this->dbforge->add_key('aul_id', true);
        $this->dbforge->add_key('mem_id');
        if ($this->dbforge->create_table('autologin', true) === false) {
            return false;
        }


        // banner table
        $this->dbforge->add_field(array(
            'ban_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ),
            'ban_start_date' => array(
                'type' => 'DATE',
                'null' => true,
            ),
            'ban_end_date' => array(
                'type' => 'DATE',
                'null' => true,
            ),
            'bng_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'ban_title' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'ban_url' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'ban_target' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'ban_device' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'ban_width' => array(
                'type' => 'MEDIUMINT',
                'constraint' => 6,
                'unsigned' => true,
                'default' => '0',
            ),
            'ban_height' => array(
                'type' => 'MEDIUMINT',
                'constraint' => 6,
                'unsigned' => true,
                'default' => '0',
            ),
            'ban_hit' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'ban_order' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'ban_image' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'ban_activated' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'unsigned' => true,
                'default' => '0',
            ),
            'ban_datetime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'ban_ip' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => '',
            ),
            'mem_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
        ));
        $this->dbforge->add_key('ban_id', true);
        $this->dbforge->add_key(array('bng_name'));
        $this->dbforge->add_key(array('ban_start_date'));
        $this->dbforge->add_key(array('ban_end_date'));
        if ($this->dbforge->create_table('banner', true) === false) {
            return false;
        }


        // banner group table
        $this->dbforge->add_field(array(
            'bng_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ),
            'bng_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
        ));
        $this->dbforge->add_key('bng_id', true);
        $this->dbforge->add_key(array('bng_name'));
        if ($this->dbforge->create_table('banner_group', true) === false) {
            return false;
        }


        // blame table
        $this->dbforge->add_field(array(
            'bla_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ),
            'target_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'target_type' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'unsigned' => true,
                'default' => '0',
            ),
            'brd_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'mem_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'target_mem_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'bla_datetime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'bla_ip' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => '',
            ),
        ));
        $this->dbforge->add_key('bla_id', true);
        $this->dbforge->add_key('mem_id');
        $this->dbforge->add_key('target_mem_id');
        $this->dbforge->add_key('target_id');
        $this->dbforge->add_key('brd_id');
        if ($this->dbforge->create_table('blame', true) === false) {
            return false;
        }


        // board table
        $this->dbforge->add_field(array(
            'brd_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ),
            'bgr_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'brd_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'brd_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'brd_mobile_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'brd_order' => array(
                'type' => 'INT',
                'constraint' => 11,
                'default' => '0',
            ),
            'brd_search' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'unsigned' => true,
                'default' => '0',
            ),
        ));
        $this->dbforge->add_key('brd_id', true);
        $this->dbforge->add_key('bgr_id');
        if ($this->dbforge->create_table('board', true) === false) {
            return false;
        }
        $this->db->query('ALTER TABLE ' . $this->db->dbprefix . 'board ADD UNIQUE KEY `brd_key` (`brd_key`)');


        // board_admin table
        $this->dbforge->add_field(array(
            'bam_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ),
            'brd_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'mem_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
        ));
        $this->dbforge->add_key('bam_id', true);
        $this->dbforge->add_key('brd_id');
        if ($this->dbforge->create_table('board_admin', true) === false) {
            return false;
        }


        // board_category table
        $this->dbforge->add_field(array(
            'bca_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ),
            'brd_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'bca_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'bca_value' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'bca_parent' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'bca_order' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
        ));
        $this->dbforge->add_key('bca_id', true);
        $this->dbforge->add_key('brd_id');
        if ($this->dbforge->create_table('board_category', true) === false) {
            return false;
        }


        // board_group table
        $this->dbforge->add_field(array(
            'bgr_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ),
            'bgr_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'bgr_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'bgr_order' => array(
                'type' => 'INT',
                'constraint' => 11,
                'default' => '0',
            ),
        ));
        $this->dbforge->add_key('bgr_id', true);
        $this->dbforge->add_key('bgr_order');
        if ($this->dbforge->create_table('board_group', true) === false) {
            return false;
        }
        $this->db->query('ALTER TABLE ' . $this->db->dbprefix . 'board_group ADD UNIQUE KEY `bgr_key` (`bgr_key`)');


        // board_group_admin table
        $this->dbforge->add_field(array(
            'bga_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ),
            'bgr_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'mem_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
        ));
        $this->dbforge->add_key('bga_id', true);
        $this->dbforge->add_key('bgr_id');
        if ($this->dbforge->create_table('board_group_admin', true) === false) {
            return false;
        }


        // board_group_meta table
        $this->dbforge->add_field(array(
            'bgr_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'bgm_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'bgm_value' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
        ));
        if ($this->dbforge->create_table('board_group_meta', true) === false) {
            return false;
        }
        $this->db->query('ALTER TABLE ' . $this->db->dbprefix . 'board_group_meta ADD UNIQUE KEY `bgr_id_bgm_key` (`bgr_id`, `bgm_key`)');


        // board_meta table
        $this->dbforge->add_field(array(
            'brd_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'bmt_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'bmt_value' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
        ));
        if ($this->dbforge->create_table('board_meta', true) === false) {
            return false;
        }
        $this->db->query('ALTER TABLE ' . $this->db->dbprefix . 'board_meta ADD UNIQUE KEY `brd_id_bmt_key` (`brd_id`, `bmt_key`)');


        // comment table
        $this->dbforge->add_field(array(
            'cmt_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ),
            'post_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'brd_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'cmt_num' => array(
                'type' => 'INT',
                'constraint' => 11,
                'default' => '0',
            ),
            'cmt_reply' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'default' => '',
            ),
            'cmt_html' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'unsigned' => true,
                'default' => '0',
            ),
            'cmt_secret' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'unsigned' => true,
                'default' => '0',
            ),
            'cmt_content' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'mem_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'default' => '0',
            ),
            'cmt_password' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'cmt_userid' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'default' => '',
            ),
            'cmt_username' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'default' => '',
            ),
            'cmt_nickname' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'default' => '',
            ),
            'cmt_email' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'cmt_homepage' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'cmt_datetime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'cmt_updated_datetime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'cmt_ip' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => '',
            ),
            'cmt_like' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'cmt_dislike' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'cmt_blame' => array(
                'type' => 'MEDIUMINT',
                'constraint' => 6,
                'unsigned' => true,
                'default' => '0',
            ),
            'cmt_device' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'default' => '',
            ),
            'cmt_del' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'unsigned' => true,
                'default' => '0',
            ),
        ));
        $this->dbforge->add_key('cmt_id', true);
        $this->dbforge->add_key(array('post_id', 'cmt_num', 'cmt_reply'));
        if ($this->dbforge->create_table('comment', true) === false) {
            return false;
        }


        // comment_meta table
        $this->dbforge->add_field(array(
            'cmt_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'cme_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'cme_value' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
        ));
        if ($this->dbforge->create_table('comment_meta', true) === false) {
            return false;
        }
        $this->db->query('ALTER TABLE ' . $this->db->dbprefix . 'comment_meta ADD UNIQUE KEY `cmt_id_cme_key` (`cmt_id`, `cme_key`)');


        // config table
        $this->dbforge->add_field(array(
            'cfg_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'cfg_value' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
        ));
        if ($this->dbforge->create_table('config', true) === false) {
            return false;
        }
        $this->db->query('ALTER TABLE ' . $this->db->dbprefix . 'config ADD UNIQUE KEY `cfg_key` (`cfg_key`)');


        // currentvisitor table
        $this->dbforge->add_field(array(
            'cur_ip' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => '',
            ),
            'mem_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'cur_mem_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'cur_datetime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'cur_page' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'cur_url' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'cur_referer' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'cur_useragent' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
        ));
        $this->dbforge->add_key('cur_ip', true);
        if ($this->dbforge->create_table('currentvisitor', true) === false) {
            return false;
        }


        // document table
        $this->dbforge->add_field(array(
            'doc_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ),
            'doc_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'doc_title' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'doc_content' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'doc_mobile_content' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'doc_content_html_type' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'unsigned' => true,
                'default' => '0',
            ),
            'doc_layout' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'doc_mobile_layout' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'doc_sidebar' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'unsigned' => true,
                'default' => '0',
            ),
            'doc_mobile_sidebar' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'unsigned' => true,
                'default' => '0',
            ),
            'doc_skin' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'doc_mobile_skin' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'doc_hit' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'mem_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'doc_datetime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'doc_updated_mem_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'doc_updated_datetime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
        ));
        $this->dbforge->add_key('doc_id', true);
        if ($this->dbforge->create_table('document', true) === false) {
            return false;
        }
        $this->db->query('ALTER TABLE ' . $this->db->dbprefix . 'document ADD UNIQUE KEY `doc_key` (`doc_key`)');


        // editor_image table
        $this->dbforge->add_field(array(
            'eim_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ),
            'mem_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'eim_originname' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'eim_filename' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'eim_filesize' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'eim_width' => array(
                'type' => 'MEDIUMINT',
                'constraint' => 6,
                'unsigned' => true,
                'default' => '0',
            ),
            'eim_height' => array(
                'type' => 'MEDIUMINT',
                'constraint' => 6,
                'unsigned' => true,
                'default' => '0',
            ),
            'eim_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'default' => '',
            ),
            'eim_datetime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'eim_ip' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => '',
            ),
        ));
        $this->dbforge->add_key('eim_id', true);
        $this->dbforge->add_key('mem_id');
        if ($this->dbforge->create_table('editor_image', true) === false) {
            return false;
        }


        // faq table
        $this->dbforge->add_field(array(
            'faq_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ),
            'fgr_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'faq_title' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'faq_content' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'faq_mobile_content' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'faq_content_html_type' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'unsigned' => true,
                'default' => '0',
            ),
            'faq_order' => array(
                'type' => 'INT',
                'constraint' => 11,
                'default' => '0',
            ),
            'faq_datetime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'faq_ip' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => '',
            ),
            'mem_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
        ));
        $this->dbforge->add_key('faq_id', true);
        $this->dbforge->add_key('fgr_id');
        if ($this->dbforge->create_table('faq', true) === false) {
            return false;
        }


        // faq_group table
        $this->dbforge->add_field(array(
            'fgr_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ),
            'fgr_title' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'fgr_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'fgr_layout' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'fgr_mobile_layout' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'fgr_sidebar' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'unsigned' => true,
                'default' => '0',
            ),
            'fgr_mobile_sidebar' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'unsigned' => true,
                'default' => '0',
            ),
            'fgr_skin' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'fgr_mobile_skin' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'fgr_datetime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'fgr_ip' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => '',
            ),
            'mem_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
        ));
        $this->dbforge->add_key('fgr_id', true);
        if ($this->dbforge->create_table('faq_group', true) === false) {
            return false;
        }
        $this->db->query('ALTER TABLE ' . $this->db->dbprefix . 'faq_group ADD UNIQUE KEY `fgr_key` (`fgr_key`)');


        // follow table
        $this->dbforge->add_field(array(
            'fol_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ),
            'mem_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'target_mem_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'fol_datetime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
        ));
        $this->dbforge->add_key('fol_id', true);
        $this->dbforge->add_key('mem_id');
        $this->dbforge->add_key('target_mem_id');
        if ($this->dbforge->create_table('follow', true) === false) {
            return false;
        }


        // like table
        $this->dbforge->add_field(array(
            'lik_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ),
            'target_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'target_type' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'unsigned' => true,
                'default' => '0',
            ),
            'brd_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'mem_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'target_mem_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'lik_type' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'unsigned' => true,
            ),
            'lik_datetime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'lik_ip' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => '',
            ),
        ));
        $this->dbforge->add_key('lik_id', true);
        $this->dbforge->add_key('target_id');
        $this->dbforge->add_key('mem_id');
        $this->dbforge->add_key('target_mem_id');
        if ($this->dbforge->create_table('like', true) === false) {
            return false;
        }


        // member table
        $this->dbforge->add_field(array(
            'mem_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ),
            'mem_userid' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'default' => '',
            ),
            'mem_email' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'mem_password' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'mem_username' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'default' => '',
            ),
            'mem_nickname' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'default' => '',
            ),
            'mem_level' => array(
                'type' => 'MEDIUMINT',
                'constraint' => 6,
                'unsigned' => true,
                'default' => '0',
            ),
            'mem_point' => array(
                'type' => 'INT',
                'constraint' => 11,
                'default' => '0',
            ),
            'mem_homepage' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'mem_phone' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'mem_birthday' => array(
                'type' => 'CHAR',
                'constraint' => '10',
                'default' => '',
            ),
            'mem_sex' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'unsigned' => true,
                'default' => '0',
            ),
            'mem_zipcode' => array(
                'type' => 'VARCHAR',
                'constraint' => '7',
                'default' => '',
            ),
            'mem_address1' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'mem_address2' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'mem_address3' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'mem_address4' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'mem_receive_email' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'unsigned' => true,
                'default' => '0',
            ),
            'mem_use_note' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'unsigned' => true,
                'default' => '0',
            ),
            'mem_receive_sms' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'unsigned' => true,
                'default' => '0',
            ),
            'mem_open_profile' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'unsigned' => true,
                'default' => '0',
            ),
            'mem_denied' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'unsigned' => true,
                'default' => '0',
            ),
            'mem_email_cert' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'unsigned' => true,
                'default' => '0',
            ),
            'mem_register_datetime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'mem_register_ip' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => '',
            ),
            'mem_lastlogin_datetime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'mem_lastlogin_ip' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => '',
            ),
            'mem_is_admin' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'unsigned' => true,
                'default' => '0',
            ),
            'mem_profile_content' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'mem_adminmemo' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'mem_following' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'mem_followed' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'mem_icon' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'mem_photo' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
        ));
        $this->dbforge->add_key('mem_id', true);
        $this->dbforge->add_key('mem_email');
        $this->dbforge->add_key('mem_lastlogin_datetime');
        $this->dbforge->add_key('mem_register_datetime');
        if ($this->dbforge->create_table('member', true) === false) {
            return false;
        }
        $this->db->query('ALTER TABLE ' . $this->db->dbprefix . 'member ADD UNIQUE KEY `mem_userid` (`mem_userid`)');


        // member_auth_email table
        $this->dbforge->add_field(array(
            'mae_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ),
            'mem_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'mae_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'mae_type' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'unsigned' => true,
                'default' => '0',
            ),
            'mae_generate_datetime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'mae_use_datetime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'mae_expired' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'unsigned' => true,
                'default' => '0',
            ),
        ));
        $this->dbforge->add_key('mae_id', true);
        $this->dbforge->add_key(array('mae_key', 'mem_id'));
        if ($this->dbforge->create_table('member_auth_email', true) === false) {
            return false;
        }


        // member_certify table
        $this->dbforge->add_field(array(
            'mce_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ),
            'mem_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'mce_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'mce_datetime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'mce_ip' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => '',
            ),
            'mce_content' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
        ));
        $this->dbforge->add_key('mce_id', true);
        $this->dbforge->add_key('mem_id');
        $this->dbforge->add_key('mce_type');
        if ($this->dbforge->create_table('member_certify', true) === false) {
            return false;
        }


        // member_extra_vars table
        $this->dbforge->add_field(array(
            'mem_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'mev_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'mev_value' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
        ));
        if ($this->dbforge->create_table('member_extra_vars', true) === false) {
            return false;
        }
        $this->db->query('ALTER TABLE ' . $this->db->dbprefix . 'member_extra_vars ADD UNIQUE KEY `mem_id_mev_key` (`mem_id`, `mev_key`)');


        // member_login_log table
        $this->dbforge->add_field(array(
            'mll_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ),
            'mll_success' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'unsigned' => true,
                'default' => '0',
            ),
            'mem_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'mll_datetime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'mll_ip' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => '',
            ),
            'mll_reason' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'mll_useragent' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'mll_url' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'mll_referer' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
        ));
        $this->dbforge->add_key('mll_id', true);
        $this->dbforge->add_key('mll_success');
        $this->dbforge->add_key('mem_id');
        if ($this->dbforge->create_table('member_login_log', true) === false) {
            return false;
        }


        // member_meta table
        $this->dbforge->add_field(array(
            'mem_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'mmt_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'mmt_value' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
        ));
        if ($this->dbforge->create_table('member_meta', true) === false) {
            return false;
        }
        $this->db->query('ALTER TABLE ' . $this->db->dbprefix . 'member_meta ADD UNIQUE KEY `mem_id_mmt_key` (`mem_id`, `mmt_key`)');


        // member_nickname table
        $this->dbforge->add_field(array(
            'mni_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ),
            'mem_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'mni_nickname' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'default' => '',
            ),
            'mni_start_datetime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'mni_end_datetime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
        ));
        $this->dbforge->add_key('mni_id', true);
        $this->dbforge->add_key('mem_id');
        $this->dbforge->add_key('mni_nickname');
        if ($this->dbforge->create_table('member_nickname', true) === false) {
            return false;
        }


        // member_register table
        $this->dbforge->add_field(array(
            'mrg_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ),
            'mem_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'mrg_ip' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => '',
            ),
            'mrg_datetime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'mrg_recommend_mem_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'mrg_useragent' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'mrg_referer' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
        ));
        $this->dbforge->add_key('mrg_id', true);
        if ($this->dbforge->create_table('member_register', true) === false) {
            return false;
        }
        $this->db->query('ALTER TABLE ' . $this->db->dbprefix . 'member_register ADD UNIQUE KEY `mem_id` (`mem_id`)');


        // menu table
        $this->dbforge->add_field(array(
            'men_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ),
            'men_parent' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'men_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'men_link' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'men_target' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'men_desktop' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'unsigned' => true,
                'default' => '0',
            ),
            'men_mobile' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'unsigned' => true,
                'default' => '0',
            ),
            'men_custom' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'men_order' => array(
                'type' => 'INT',
                'constraint' => 11,
                'default' => '0',
            ),
        ));
        $this->dbforge->add_key('men_id', true);
        if ($this->dbforge->create_table('menu', true) === false) {
            return false;
        }


        // note table
        $this->dbforge->add_field(array(
            'nte_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ),
            'send_mem_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'recv_mem_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'nte_type' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'unsigned' => true,
                'default' => '0',
            ),
            'related_note_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'nte_title' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'nte_content' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'nte_content_html_type' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'unsigned' => true,
                'default' => '0',
            ),
            'nte_datetime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'nte_read_datetime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
        ));
        $this->dbforge->add_key('nte_id', true);
        $this->dbforge->add_key('send_mem_id');
        $this->dbforge->add_key('recv_mem_id');
        if ($this->dbforge->create_table('note', true) === false) {
            return false;
        }


        // notification table
        $this->dbforge->add_field(array(
            'not_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ),
            'mem_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'target_mem_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'not_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'not_content_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'not_message' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'not_url' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'not_datetime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'not_read_datetime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
        ));
        $this->dbforge->add_key('not_id', true);
        $this->dbforge->add_key('mem_id');
        if ($this->dbforge->create_table('notification', true) === false) {
            return false;
        }


        // point table
        $this->dbforge->add_field(array(
            'poi_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ),
            'mem_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'poi_datetime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'poi_content' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'poi_point' => array(
                'type' => 'INT',
                'constraint' => 11,
                'default' => '0',
            ),
            'poi_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'default' => '',
            ),
            'poi_related_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'default' => '',
            ),
            'poi_action' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
        ));
        $this->dbforge->add_key('poi_id', true);
        $this->dbforge->add_key(array('mem_id', 'poi_type', 'poi_related_id', 'poi_action'));
        if ($this->dbforge->create_table('point', true) === false) {
            return false;
        }


        // popup table
        $this->dbforge->add_field(array(
            'pop_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ),
            'pop_start_date' => array(
                'type' => 'DATE',
                'null' => true,
            ),
            'pop_end_date' => array(
                'type' => 'DATE',
                'null' => true,
            ),
            'pop_is_center' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'unsigned' => true,
                'default' => '0',
            ),
            'pop_left' => array(
                'type' => 'MEDIUMINT',
                'constraint' => 6,
                'unsigned' => true,
                'default' => '0',
            ),
            'pop_top' => array(
                'type' => 'MEDIUMINT',
                'constraint' => 6,
                'unsigned' => true,
                'default' => '0',
            ),
            'pop_width' => array(
                'type' => 'MEDIUMINT',
                'constraint' => 6,
                'unsigned' => true,
                'default' => '0',
            ),
            'pop_height' => array(
                'type' => 'MEDIUMINT',
                'constraint' => 6,
                'unsigned' => true,
                'default' => '0',
            ),
            'pop_device' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'default' => '',
            ),
            'pop_title' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'pop_content' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'pop_content_html_type' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'unsigned' => true,
                'default' => '0',
            ),
            'pop_disable_hours' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'pop_activated' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'unsigned' => true,
                'default' => '0',
            ),
            'pop_page' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'unsigned' => true,
                'default' => '0',
            ),
            'pop_datetime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'pop_ip' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => '',
            ),
            'mem_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
        ));
        $this->dbforge->add_key('pop_id', true);
        $this->dbforge->add_key('pop_start_date');
        $this->dbforge->add_key('pop_end_date');
        if ($this->dbforge->create_table('popup', true) === false) {
            return false;
        }


        // post table
        $this->dbforge->add_field(array(
            'post_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ),
            'post_num' => array(
                'type' => 'INT',
                'constraint' => 11,
                'default' => '0',
            ),
            'post_reply' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'default' => '',
            ),
            'brd_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'post_title' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'post_content' => array(
                'type' => 'MEDIUMTEXT',
                'null' => true,
            ),
            'post_category' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'mem_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'default' => '0',
            ),
            'post_userid' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'default' => '',
            ),
            'post_username' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'default' => '',
            ),
            'post_nickname' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'default' => '',
            ),
            'post_email' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'post_homepage' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'post_datetime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'post_password' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'post_updated_datetime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'post_update_mem_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'post_comment_count' => array(
                'type' => 'MEDIUMINT',
                'constraint' => 6,
                'unsigned' => true,
                'default' => '0',
            ),
            'post_comment_updated_datetime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'post_link_count' => array(
                'type' => 'MEDIUMINT',
                'constraint' => 6,
                'unsigned' => true,
                'default' => '0',
            ),
            'post_secret' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'unsigned' => true,
                'default' => '0',
            ),
            'post_html' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'unsigned' => true,
                'default' => '0',
            ),
            'post_hide_comment' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'unsigned' => true,
                'default' => '0',
            ),
            'post_notice' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'unsigned' => true,
                'default' => '0',
            ),
            'post_receive_email' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'unsigned' => true,
                'default' => '0',
            ),
            'post_hit' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'post_like' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'post_dislike' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'post_ip' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => '',
            ),
            'post_blame' => array(
                'type' => 'MEDIUMINT',
                'constraint' => 6,
                'unsigned' => true,
                'default' => '0',
            ),
            'post_device' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'default' => '',
            ),
            'post_file' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'unsigned' => true,
                'default' => '0',
            ),
            'post_image' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'unsigned' => true,
                'default' => '0',
            ),
            'post_del' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'unsigned' => true,
                'default' => '0',
            ),
        ));
        $this->dbforge->add_key('post_id', true);
        $this->dbforge->add_key(array('post_num', 'post_reply'));
        $this->dbforge->add_key('brd_id');
        $this->dbforge->add_key('post_datetime');
        $this->dbforge->add_key('post_updated_datetime');
        $this->dbforge->add_key('post_comment_updated_datetime');
        if ($this->dbforge->create_table('post', true) === false) {
            return false;
        }


        // post_extra_vars tables
        $this->dbforge->add_field(array(
            'post_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'brd_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'pev_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'pev_value' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
        ));
        if ($this->dbforge->create_table('post_extra_vars', true) === false) {
            return false;
        }
        $this->db->query('ALTER TABLE ' . $this->db->dbprefix . 'post_extra_vars ADD UNIQUE KEY `post_id_pev_key` (`post_id`, `pev_key`)');


        // post_file table
        $this->dbforge->add_field(array(
            'pfi_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ),
            'post_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'brd_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'mem_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'pfi_originname' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'pfi_filename' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'pfi_download' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'pfi_filesize' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'pfi_width' => array(
                'type' => 'MEDIUMINT',
                'constraint' => 6,
                'unsigned' => true,
                'default' => '0',
            ),
            'pfi_height' => array(
                'type' => 'MEDIUMINT',
                'constraint' => 6,
                'unsigned' => true,
                'default' => '0',
            ),
            'pfi_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
                'default' => '',
            ),
            'pfi_is_image' => array(
                'type' => 'TINYINT',
                'constraint' => 4,
                'unsigned' => true,
                'default' => '0',
            ),
            'pfi_datetime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'pfi_ip' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => '',
            ),
        ));
        $this->dbforge->add_key('pfi_id', true);
        $this->dbforge->add_key('post_id');
        $this->dbforge->add_key('mem_id');
        if ($this->dbforge->create_table('post_file', true) === false) {
            return false;
        }


        // post_link table
        $this->dbforge->add_field(array(
            'pln_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ),
            'post_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'brd_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'pln_url' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'pln_hit' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
        ));
        $this->dbforge->add_key('pln_id', true);
        $this->dbforge->add_key('post_id');
        if ($this->dbforge->create_table('post_link', true) === false) {
            return false;
        }


        // post_meta table
        $this->dbforge->add_field(array(
            'post_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'brd_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'pmt_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'pmt_value' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
        ));
        if ($this->dbforge->create_table('post_meta', true) === false) {
            return false;
        }
        $this->db->query('ALTER TABLE ' . $this->db->dbprefix . 'post_meta ADD UNIQUE KEY `post_id_pmt_key` (`post_id`, `pmt_key`)');


        // scrap table
        $this->dbforge->add_field(array(
            'scr_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ),
            'mem_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'post_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'brd_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'target_mem_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'scr_datetime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'scr_title' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
        ));
        $this->dbforge->add_key('scr_id', true);
        $this->dbforge->add_key('mem_id');
        $this->dbforge->add_key('post_id');
        if ($this->dbforge->create_table('scrap', true) === false) {
            return false;
        }


        // search_keyword table
        $this->dbforge->add_field(array(
            'sek_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ),
            'sek_keyword' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'sek_datetime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
            'sek_ip' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => '',
            ),
            'mem_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
        ));
        $this->dbforge->add_key('sek_id', true);
        $this->dbforge->add_key(array('sek_keyword', 'sek_datetime', 'sek_ip'));
        if ($this->dbforge->create_table('search_keyword', true) === false) {
            return false;
        }


        // session table
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'VARCHAR',
                'constraint' => '40',
                'default' => '',
            ),
            'ip_address' => array(
                'type' => 'VARCHAR',
                'constraint' => '45',
                'default' => '',
            ),
            'timestamp' => array(
                'type' => 'INT',
                'constraint' => 10,
                'default' => '0',
            ),
            'data' => array(
                'type' => 'BLOB',
            ),
        ));
        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('timestamp');
        if ($this->dbforge->create_table('session', true) === false) {
            return false;
        }


        // stat_count table
        $this->dbforge->add_field(array(
            'sco_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ),
            'sco_ip' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => '',
            ),
            'sco_date' => array(
                'type' => 'DATE',
            ),
            'sco_time' => array(
                'type' => 'TIME',
            ),
            'sco_referer' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'sco_current' => array(
                'type' => 'TEXT',
                'null' => true,
            ),
            'sco_agent' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
        ));
        $this->dbforge->add_key('sco_id', true);
        $this->dbforge->add_key('sco_date');
        if ($this->dbforge->create_table('stat_count', true) === false) {
            return false;
        }


        // stat_count_board table
        $this->dbforge->add_field(array(
            'scb_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ),
            'scb_date' => array(
                'type' => 'DATE',
            ),
            'brd_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'scb_count' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
        ));
        $this->dbforge->add_key('scb_id', true);
        $this->dbforge->add_key(array('scb_date', 'brd_id'));
        if ($this->dbforge->create_table('stat_count_board', true) === false) {
            return false;
        }


        // stat_count_date table
        $this->dbforge->add_field(array(
            'scd_date' => array(
                'type' => 'DATE',
            ),
            'scd_count' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
        ));
        $this->dbforge->add_key('scd_date', true);
        if ($this->dbforge->create_table('stat_count_date', true) === false) {
            return false;
        }


        // tempsave table
        $this->dbforge->add_field(array(
            'tmp_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ),
            'brd_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'tmp_title' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '',
            ),
            'tmp_content' => array(
                'type' => 'MEDIUMTEXT',
                'null' => true,
            ),
            'mem_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => '0',
            ),
            'tmp_ip' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => '',
            ),
            'tmp_datetime' => array(
                'type' => 'DATETIME',
                'null' => true,
            ),
        ));
        $this->dbforge->add_key('tmp_id', true);
        $this->dbforge->add_key('mem_id');
        if ($this->dbforge->create_table('tempsave', true) === false) {
            return false;
        }


        // unique_id table
        $this->dbforge->add_field(array(
            'unq_id' => array(
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
            ),
            'unq_ip' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => '',
            ),
        ));
        $this->dbforge->add_key('unq_id', true);
        if ($this->dbforge->create_table('unique_id', true) === false) {
            return false;
        }

        return true;
    }


    public function _migration_tables()
    {
        $this->load->library('migration');
        $this->config->load('migration');
        $row = $this->db->get('migrations')->row();
        $latest_version = config_item('migration_version');
        if ($latest_version && ! $this->migration->version($latest_version)) {
            return false;
        }

        return true;

    }


    public function _insert_init_data()
    {
        $this->load->library(array('user_agent', 'session'));
        $this->load->model(array(
            'Member_model', 'Member_meta_model', 'Config_model',
            'Member_nickname_model', 'Member_register_model', 'Document_model',
            'Faq_model', 'Faq_group_model', 'Board_model',
            'Board_meta_model', 'Board_group_model', 'Board_group_meta_model',
            'Menu_model'
        ));

        if ( ! function_exists('password_hash')) {
            $this->load->helper('password');
        }

        $this->load->driver('cache', config_item('cache_method'));


        $skin = $this->input->post('skin');
        $skin_mobile = $this->input->post('skin') === 'basic' ? 'mobile' : 'bootstrap';

        $configdata = array(
            'site_title' => '홈페이지',
            'site_logo' => '홈페이지',
            'admin_logo' => 'Admin',
            'spam_word' => '18아,18놈,18새끼,18년,18뇬,18노,18것,18넘,개년,개놈,개뇬,개새,개색끼,개세끼,개세이,개쉐이,개쉑,개쉽,개시키,개자식,개좆,게색기,게색끼,광뇬,뇬,눈깔,뉘미럴,니귀미,니기미,니미,도촬,되질래,뒈져라,뒈진다,디져라,디진다,디질래,병쉰,병신,뻐큐,뻑큐,뽁큐,삐리넷,새꺄,쉬발,쉬밸,쉬팔,쉽알,스패킹,스팽,시벌,시부랄,시부럴,시부리,시불,시브랄,시팍,시팔,시펄,실밸,십8,십쌔,십창,싶알,쌉년,썅놈,쌔끼,쌩쑈,썅,써벌,썩을년,쎄꺄,쎄엑,쓰바,쓰발,쓰벌,쓰팔,씨8,씨댕,씨바,씨발,씨뱅,씨봉알,씨부랄,씨부럴,씨부렁,씨부리,씨불,씨브랄,씨빠,씨빨,씨뽀랄,씨팍,씨팔,씨펄,씹,아가리,아갈이,엄창,접년,잡놈,재랄,저주글,조까,조빠,조쟁이,조지냐,조진다,조질래,존나,존니,좀물,좁년,좃,좆,좇,쥐랄,쥐롤,쥬디,지랄,지럴,지롤,지미랄,쫍빱,凸,퍽큐,뻑큐,빠큐,ㅅㅂㄹㅁ',
            'white_iframe' => 'www.youtube.com
www.youtube-nocookie.com
maps.google.co.kr
maps.google.com
flvs.daum.net
player.vimeo.com
sbsplayer.sbs.co.kr
serviceapi.rmcnmv.naver.com
serviceapi.nmv.naver.com
www.mgoon.com
videofarm.daum.net
player.sbs.co.kr
sbsplayer.sbs.co.kr
www.tagstory.com
play.tagstory.com
flvr.pandora.tv',
            'new_post_second' => '30',
            'open_currentvisitor' => '1',
            'currentvisitor_minute' => '10',
            'use_copy_log' => '1',
            'max_level' => '100',
            'ip_display_style' => '1001',
            'list_count' => '20',
            'site_blacklist_title' => '사이트가 공사중에 있습니다',
            'site_blacklist_content' => '<p>안녕하세요</p><p>블편을 드려 죄송합니다. 지금 이 사이트는 접근이 금지되어있습니다</p><p>감사합니다</p>',
            'use_point' => '1',
            'point_register' => '50',
            'point_login' => '5',
            'point_recommended' => '5',
            'point_recommender' => '5',
            'point_note' => '10',
            'block_download_zeropoint' => '1',
            'block_read_zeropoint' => '1',
            'use_sideview' => '1',
            'use_mobile_sideview' => '1',
            'use_sideview_email' => '1',
            'use_mobile_sideview_email' => '1',
            'post_editor_type' => 'smarteditor',
            'use_document_dhtml' => '1',
            'document_editor_type' => 'smarteditor',
            'document_thumb_width' => '700',
            'document_mobile_thumb_width' => '400',
            'document_content_target_blank' => '1',
            'use_document_auto_url' => '1',
            'use_faq_dhtml' => '1',
            'faq_editor_type' => 'smarteditor',
            'faq_thumb_width' => '700',
            'faq_mobile_thumb_width' => '400',
            'faq_content_target_blank' => '1',
            'use_faq_auto_url' => '1',
            'use_popup_dhtml' => '1',
            'popup_editor_type' => 'smarteditor',
            'popup_thumb_width' => '700',
            'popup_mobile_thumb_width' => '400',
            'popup_content_target_blank' => '1',
            'use_popup_auto_url' => '1',
            'use_formmail_dhtml' => '1',
            'formmail_editor_type' => 'smarteditor',
            'use_note' => '1',
            'note_list_page' => '10',
            'note_mobile_list_page' => '10',
            'use_note_dhtml' => '1',
            'use_note_mobile_dhtml' => '1',
            'note_editor_type' => 'smarteditor',
            'use_notification' => '1',
            'notification_reply' => '1',
            'notification_comment' => '1',
            'notification_comment_comment' => '1',
            'notification_note' => '1',

            'layout_default' => $skin,
            'sidebar_default' => '1',
            'skin_default' => $skin,
            'mobile_layout_default' => $skin_mobile,
            'mobile_skin_default' => $skin_mobile,
            'skin_popup' => 'basic',
            'mobile_skin_popup' => 'basic',
            'skin_emailform' => 'basic',

            'use_login_account' => 'both',
            'password_length' => '4',
            'use_member_photo' => '1',
            'member_photo_width' => '80',
            'member_photo_height' => '80',
            'use_member_icon' => '1',
            'member_icon_width' => '20',
            'member_icon_height' => '20',
            'denied_nickname_list' => 'admin,administrator,관리자,운영자,어드민,주인장,webmaster,웹마스터,sysop,시삽,시샵,manager,매니저,메니저,root,루트,su,guest,방문객',
            'denied_userid_list' => 'admin,administrator,webmaster,sysop,manager,root,su,guest,super',
            'member_register_policy1' => '회원약관을 입력해주세요',
            'member_register_policy2' => '개인정보취급방침을 입력해주세요',
            'register_level' => '1',
            'change_nickname_date' => '60',
            'change_open_profile_date' => '60',
            'change_use_note_date' => '60',
            'change_password_date' => '180',
            'max_login_try_count' => '5',
            'max_login_try_limit_second' => '30',
            'total_rss_feed_count' => '100',
            'site_meta_title_default' => '{홈페이지제목}',
            'site_meta_title_main' => '{홈페이지제목}',
            'site_meta_title_board_list' => '{게시판명} - {홈페이지제목}',
            'site_meta_title_board_post' => '{글제목} > {게시판명} - {홈페이지제목}',
            'site_meta_title_board_write' => '{게시판명} 글쓰기 - {홈페이지제목}',
            'site_meta_title_board_modify' => '{글제목} 글수정 - {홈페이지제목}',
            'site_meta_title_group' => '{그룹명} - {홈페이지제목}',
            'site_meta_title_document' => '{문서제목} - {홈페이지제목}',
            'site_meta_title_faq' => '{FAQ제목} - {홈페이지제목}',
            'site_meta_title_register' => '회원가입 - {홈페이지제목}',
            'site_meta_title_register_form' => '회원가입 - {홈페이지제목}',
            'site_meta_title_register_result' => '회원가입결과 - {홈페이지제목}',
            'site_meta_title_findaccount' => '회원정보찾기 - {홈페이지제목}',
            'site_meta_title_login' => '로그인 - {홈페이지제목}',
            'site_meta_title_mypage' => '{회원닉네임}님의 마이페이지 - {홈페이지제목}',
            'site_meta_title_mypage_post' => '{회원닉네임}님의 작성글 - {홈페이지제목}',
            'site_meta_title_mypage_comment' => '{회원닉네임}님의 작성댓글 - {홈페이지제목}',
            'site_meta_title_mypage_point' => '{회원닉네임}님의 포인트 - {홈페이지제목}',
            'site_meta_title_mypage_followinglist' => '{회원닉네임}님의 팔로우 - {홈페이지제목}',
            'site_meta_title_mypage_followedlist' => '{회원닉네임}님의 팔로우 - {홈페이지제목}',
            'site_meta_title_mypage_like_post' => '{회원닉네임}님의 추천글 - {홈페이지제목}',
            'site_meta_title_mypage_like_comment' => '{회원닉네임}님의 추천댓글 - {홈페이지제목}',
            'site_meta_title_mypage_scrap' => '{회원닉네임}님의 스크랩 - {홈페이지제목}',
            'site_meta_title_mypage_loginlog' => '{회원닉네임}님의 로그인기록 - {홈페이지제목}',
            'site_meta_title_membermodify' => '회원정보수정 - {홈페이지제목}',
            'site_meta_title_membermodify_memberleave' => '회원탈퇴 - {홈페이지제목}',
            'site_meta_title_currentvisitor' => '현재접속자 - {홈페이지제목}',
            'site_meta_title_search' => '{검색어} - {홈페이지제목}',
            'site_meta_title_note_list' => '{회원닉네임}님의 쪽지함 - {홈페이지제목}',
            'site_meta_title_note_view' => '{회원닉네임}님의 쪽지함 - {홈페이지제목}',
            'site_meta_title_note_write' => '{회원닉네임}님의 쪽지함 - {홈페이지제목}',
            'site_meta_title_profile' => '{회원닉네임}님의 프로필 - {홈페이지제목}',
            'site_meta_title_formmail' => '메일발송 - {홈페이지제목}',
            'site_meta_title_notification' => '{회원닉네임}님의 알림 - {홈페이지제목}',

            'send_email_register_admin_title' => '[회원가입알림] {회원닉네임}님이 회원가입하셨습니다',
            'send_email_register_admin_content' => '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-left: 1px solid rgb(226,226,225);border-right: 1px solid rgb(226,226,225);background-color: rgb(255,255,255);border-top:10px solid #348fe2; border-bottom:5px solid #348fe2;border-collapse: collapse;"><tr><td width="200" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;">{홈페이지명}</td><td style="font-size:12px;padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><span style="font-size:14px;font-weight:bold;color:rgb(0,0,0)">안녕하세요 관리자님,</span><br /></td></tr><tr style="border-top:1px solid #e2e2e2; border-bottom:1px solid #e2e2e2;"><td colspan="2" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><p>안녕하세요,</p><p>{회원닉네임} 님이 회원가입 하셨습니다.</p><p>회원아이디 : {회원아이디}</p><p>닉네임 : {회원닉네임}</p><p>이메일 : {회원이메일}</p><p>가입한 곳 IP : {회원아이피}</p><p>감사합니다.</p></td></tr></table>',
            'send_email_register_user_title' => '[{홈페이지명}] {회원닉네임}님의 회원가입을 축하드립니다',
            'send_email_register_user_content' => '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-left: 1px solid rgb(226,226,225);border-right: 1px solid rgb(226,226,225);background-color: rgb(255,255,255);border-top:10px solid #348fe2; border-bottom:5px solid #348fe2;border-collapse: collapse;"><tr><td width="200" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;">{홈페이지명}</td><td style="font-size:12px;padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><span style="font-size:14px;font-weight:bold;color:rgb(0,0,0)">안녕하세요 {회원닉네임}님,</span><br />회원가입을 축하드립니다.</td></tr><tr style="border-top:1px solid #e2e2e2; border-bottom:1px solid #e2e2e2;"><td colspan="2" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><p>안녕하세요 {회원닉네임} 회원님,</p><p>회원가입을 축하드립니다.</p><p>{홈페이지명} 회원으로 가입해주셔서 감사합니다.</p><p>더욱 편리한 서비스를 제공하기 위해 항상 최선을 다하겠습니다.</p><p>&nbsp;</p><p>감사합니다.</p></td></tr></table>',
            'send_email_register_user_verifytitle' => '[{홈페이지명}] {회원닉네임}님의 회원가입을 축하드립니다',
            'send_email_register_user_verifycontent' => '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-left: 1px solid rgb(226,226,225);border-right: 1px solid rgb(226,226,225);background-color: rgb(255,255,255);border-top:10px solid #348fe2; border-bottom:5px solid #348fe2;border-collapse: collapse;"><tr><td width="200" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;">{홈페이지명}</td><td style="font-size:12px;padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><span style="font-size:14px;font-weight:bold;color:rgb(0,0,0)">안녕하세요 {회원닉네임}님,</span><br />회원가입을 축하드립니다.</td></tr><tr style="border-top:1px solid #e2e2e2; border-bottom:1px solid #e2e2e2;"><td colspan="2" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><p>안녕하세요 {회원닉네임} 회원님,</p><p>회원가입을 축하드립니다.</p><p>{홈페이지명} 회원으로 가입해주셔서 감사합니다.</p><p>더욱 편리한 서비스를 제공하기 위해 항상 최선을 다하겠습니다.</p><p>&nbsp;</p><p>아래 링크를 클릭하시면 회원가입이 완료됩니다.</p><p><a href="{메일인증주소}" target="_blank" style="font-weight:bold;">메일인증 받기</a></p><p>&nbsp;</p><p>감사합니다.</p></td></tr></table>',
            'send_note_register_admin_title' => '[회원가입알림] {회원닉네임}님이 회원가입하셨습니다',
            'send_note_register_admin_content' => '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-left: 1px solid rgb(226,226,225);border-right: 1px solid rgb(226,226,225);background-color: rgb(255,255,255);border-top:10px solid #348fe2; border-bottom:5px solid #348fe2;border-collapse: collapse;"><tr><td width="200" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;">{홈페이지명}</td><td style="font-size:12px;padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><span style="font-size:14px;font-weight:bold;color:rgb(0,0,0)">안녕하세요 관리자님,</span><br /></td></tr><tr style="border-top:1px solid #e2e2e2; border-bottom:1px solid #e2e2e2;"><td colspan="2" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><p>안녕하세요,</p><p>{회원닉네임} 님이 회원가입 하셨습니다.</p><p>회원아이디 : {회원아이디}</p><p>닉네임 : {회원닉네임}</p><p>이메일 : {회원이메일}</p><p>가입한 곳 IP : {회원아이피}</p><p>감사합니다.</p></td></tr></table>',
            'send_note_register_user_title' => '회원가입을 축하드립니다',
            'send_note_register_user_content' => '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-left: 1px solid rgb(226,226,225);border-right: 1px solid rgb(226,226,225);background-color: rgb(255,255,255);border-top:10px solid #348fe2; border-bottom:5px solid #348fe2;border-collapse: collapse;"><tr><td width="200" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;">{홈페이지명}</td><td style="font-size:12px;padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><span style="font-size:14px;font-weight:bold;color:rgb(0,0,0)">안녕하세요 {회원닉네임}님,</span><br />회원가입을 축하드립니다.</td></tr><tr style="border-top:1px solid #e2e2e2; border-bottom:1px solid #e2e2e2;"><td colspan="2" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><p>안녕하세요 {회원닉네임} 회원님,</p><p>회원가입을 축하드립니다.</p><p>{홈페이지명} 회원으로 가입해주셔서 감사합니다.</p><p>더욱 편리한 서비스를 제공하기 위해 항상 최선을 다하겠습니다.</p><p>&nbsp;</p><p>감사합니다.</p></td></tr></table>',
            'send_email_changepw_admin_title' => '{회원닉네임}님이 패스워드를 변경하셨습니다',
            'send_email_changepw_admin_content' => '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-left: 1px solid rgb(226,226,225);border-right: 1px solid rgb(226,226,225);background-color: rgb(255,255,255);border-top:10px solid #348fe2; border-bottom:5px solid #348fe2;border-collapse: collapse;"><tr><td width="200" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;">{홈페이지명}</td><td style="font-size:12px;padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><span style="font-size:14px;font-weight:bold;color:rgb(0,0,0)">안녕하세요 관리자님,</span><br /></td></tr><tr style="border-top:1px solid #e2e2e2; border-bottom:1px solid #e2e2e2;"><td colspan="2" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><p>안녕하세요,</p><p>{회원닉네임} 님이 패스워드를 변경하셨습니다.</p><p>회원아이디 : {회원아이디}</p><p>닉네임 : {회원닉네임}</p><p>이메일 : {회원이메일}</p><p>변경한 곳 IP : {회원아이피}</p><p>감사합니다.</p></td></tr></table>',
            'send_email_changepw_user_title' => '[{홈페이지명}] 패스워드가 변경되었습니다',
            'send_email_changepw_user_content' => '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-left: 1px solid rgb(226,226,225);border-right: 1px solid rgb(226,226,225);background-color: rgb(255,255,255);border-top:10px solid #348fe2; border-bottom:5px solid #348fe2;border-collapse: collapse;"><tr><td width="200" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;">{홈페이지명}</td><td style="font-size:12px;padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><span style="font-size:14px;font-weight:bold;color:rgb(0,0,0)">안녕하세요 {회원닉네임}님,</span><br />회원님의 패스워드가 변경되었습니다.</td></tr><tr style="border-top:1px solid #e2e2e2; border-bottom:1px solid #e2e2e2;"><td colspan="2" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><p>안녕하세요 {회원닉네임} 회원님,</p><p>회원님의 패스워드가 변경되었습니다.</p><p>변경한 곳 IP : {회원아이피}</p><p>더욱 편리한 서비스를 제공하기 위해 항상 최선을 다하겠습니다.</p><p>&nbsp;</p><p>감사합니다.</p></td></tr></table>',
            'send_note_changepw_admin_title' => '{회원닉네임}님이 패스워드를 변경하셨습니다',
            'send_note_changepw_admin_content ' => '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-left: 1px solid rgb(226,226,225);border-right: 1px solid rgb(226,226,225);background-color: rgb(255,255,255);border-top:10px solid #348fe2; border-bottom:5px solid #348fe2;border-collapse: collapse;"><tr><td width="200" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;">{홈페이지명}</td><td style="font-size:12px;padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><span style="font-size:14px;font-weight:bold;color:rgb(0,0,0)">안녕하세요 관리자님,</span><br /></td></tr><tr style="border-top:1px solid #e2e2e2; border-bottom:1px solid #e2e2e2;"><td colspan="2" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><p>안녕하세요,</p><p>{회원닉네임} 님이 패스워드를 변경하셨습니다.</p><p>회원아이디 : {회원아이디}</p><p>닉네임 : {회원닉네임}</p><p>이메일 : {회원이메일}</p><p>변경한 곳 IP : {회원아이피}</p><p>감사합니다.</p></td></tr></table>',
            'send_note_changepw_user_title' => '패스워드가 변경되었습니다',
            'send_note_changepw_user_content' => '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-left: 1px solid rgb(226,226,225);border-right: 1px solid rgb(226,226,225);background-color: rgb(255,255,255);border-top:10px solid #348fe2; border-bottom:5px solid #348fe2;border-collapse: collapse;"><tr><td width="200" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;">{홈페이지명}</td><td style="font-size:12px;padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><span style="font-size:14px;font-weight:bold;color:rgb(0,0,0)">안녕하세요 {회원닉네임}님,</span><br />회원님의 패스워드가 변경되었습니다.</td></tr><tr style="border-top:1px solid #e2e2e2; border-bottom:1px solid #e2e2e2;"><td colspan="2" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><p>안녕하세요 {회원닉네임} 회원님,</p><p>회원님의 패스워드가 변경되었습니다.</p><p>변경한 곳 IP : {회원아이피}</p><p>더욱 편리한 서비스를 제공하기 위해 항상 최선을 다하겠습니다.</p><p>&nbsp;</p><p>감사합니다.</p></td></tr></table>',
            'send_email_memberleave_admin_title' => '{회원닉네임}님이 회원탈퇴하셨습니다',
            'send_email_memberleave_admin_content' => '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-left: 1px solid rgb(226,226,225);border-right: 1px solid rgb(226,226,225);background-color: rgb(255,255,255);border-top:10px solid #348fe2; border-bottom:5px solid #348fe2;border-collapse: collapse;"><tr><td width="200" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;">{홈페이지명}</td><td style="font-size:12px;padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><span style="font-size:14px;font-weight:bold;color:rgb(0,0,0)">안녕하세요 관리자님,</span><br /></td></tr><tr style="border-top:1px solid #e2e2e2; border-bottom:1px solid #e2e2e2;"><td colspan="2" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><p>안녕하세요,</p><p>{회원닉네임} 님이 회원탈퇴하셨습니다.</p><p>회원아이디 : {회원아이디}</p><p>닉네임 : {회원닉네임}</p><p>이메일 : {회원이메일}</p><p>탈퇴한 곳 IP : {회원아이피}</p><p>감사합니다.</p></td></tr></table>',
            'send_email_memberleave_user_title' => '[{홈페이지명}] 회원탈퇴가 완료되었습니다',
            'send_email_memberleave_user_content' => '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-left: 1px solid rgb(226,226,225);border-right: 1px solid rgb(226,226,225);background-color: rgb(255,255,255);border-top:10px solid #348fe2; border-bottom:5px solid #348fe2;border-collapse: collapse;"><tr><td width="200" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;">{홈페이지명}</td><td style="font-size:12px;padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><span style="font-size:14px;font-weight:bold;color:rgb(0,0,0)">안녕하세요 {회원닉네임}님,</span><br />회원님의 탈퇴가 처리되었습니다.</td></tr><tr style="border-top:1px solid #e2e2e2; border-bottom:1px solid #e2e2e2;"><td colspan="2" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><p>안녕하세요 {회원닉네임} 회원님,</p><p>그 동안 {홈페이지명} 이용을 해주셔서 감사드립니다</p><p>요청하신대로 회원님의 탈퇴가 정상적으로 처리되었습니다.</p><p>더욱 편리한 서비스를 제공하기 위해 항상 최선을 다하겠습니다.</p><p>&nbsp;</p><p>감사합니다.</p></td></tr></table>',
            'send_note_memberleave_admin_title' => '{회원닉네임}님이 회원탈퇴하셨습니다',
            'send_note_memberleave_admin_content' => '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-left: 1px solid rgb(226,226,225);border-right: 1px solid rgb(226,226,225);background-color: rgb(255,255,255);border-top:10px solid #348fe2; border-bottom:5px solid #348fe2;border-collapse: collapse;"><tr><td width="200" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;">{홈페이지명}</td><td style="font-size:12px;padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><span style="font-size:14px;font-weight:bold;color:rgb(0,0,0)">안녕하세요 관리자님,</span><br /></td></tr><tr style="border-top:1px solid #e2e2e2; border-bottom:1px solid #e2e2e2;"><td colspan="2" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><p>안녕하세요,</p><p>{회원닉네임} 님이 회원탈퇴하셨습니다.</p><p>회원아이디 : {회원아이디}</p><p>닉네임 : {회원닉네임}</p><p>이메일 : {회원이메일}</p><p>탈퇴한 곳 IP : {회원아이피}</p><p>감사합니다.</p></td></tr></table>',
            'send_email_changeemail_user_title' => '[{홈페이지명}] 회원님의 이메일정보가 변경되었습니다',
            'send_email_changeemail_user_content' => '<table width="100%" cellpadding="0" cellspacing="0" style="border-left: 1px solid rgb(226,226,225);border-right: 1px solid rgb(226,226,225);background-color: rgb(255,255,255);border-top:10px solid #348fe2; border-bottom:5px solid #348fe2;border-collapse: collapse;"><tr><td width="200" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;">{홈페이지명}</td><td style="font-size:12px;padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><span style="font-size:14px;font-weight:bold;color:rgb(0,0,0)">안녕하세요 {회원닉네임}님,</span><br />회원님의 이메일 주소가 변경되어 알려드립니다.</td></tr><tr style="border-top:1px solid #e2e2e2; border-bottom:1px solid #e2e2e2;"><td colspan="2" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><p>안녕하세요,</p><p>회원님의 이메일 주소가 변경되었으므로 다시 인증을 받아주시기 바랍니다.</p><p>&nbsp;</p><p>아래 링크를 클릭하시면 주소변경 인증이 완료됩니다.</p><p><a href="{메일인증주소}" target="_blank" style="font-weight:bold;">메일인증 받기</a></p><p>&nbsp;</p><p>감사합니다.</p></td></tr></table>',
            'send_email_findaccount_user_title' => '{회원닉네임}님의 아이디와 패스워드를 보내드립니다',
            'send_email_findaccount_user_content' => '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-left: 1px solid rgb(226,226,225);border-right: 1px solid rgb(226,226,225);background-color: rgb(255,255,255);border-top:10px solid #348fe2; border-bottom:5px solid #348fe2;border-collapse: collapse;"><tr><td width="200" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;">{홈페이지명}</td><td style="font-size:12px;padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><span style="font-size:14px;font-weight:bold;color:rgb(0,0,0)">안녕하세요 {회원닉네임}님,</span><br />회원님의 아이디와 패스워드를 보내드립니다.</td></tr><tr style="border-top:1px solid #e2e2e2; border-bottom:1px solid #e2e2e2;"><td colspan="2" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><p>안녕하세요,</p><p>&nbsp;</p><p>회원님의 아이디는 <strong>{회원아이디}</strong> 입니다.</p><p>아래 링크를 클릭하시면 회원님의 패스워드 변경이 가능합니다.</p><p><a href="{패스워드변경주소}" target="_blank" style="font-weight:bold;">패스워드 변경하기</a></p><p>&nbsp;</p><p>감사합니다.</p></td></tr></table>',
            'send_email_resendverify_user_title' => '{회원닉네임}님의 인증메일이 재발송되었습니다',
            'send_email_resendverify_user_content' => '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-left: 1px solid rgb(226,226,225);border-right: 1px solid rgb(226,226,225);background-color: rgb(255,255,255);border-top:10px solid #348fe2; border-bottom:5px solid #348fe2;border-collapse: collapse;"><tr><td width="200" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;">{홈페이지명}</td><td style="font-size:12px;padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><span style="font-size:14px;font-weight:bold;color:rgb(0,0,0)">안녕하세요 {회원닉네임}님,</span><br />회원님의 인증메일을 다시 보내드립니다..</td></tr><tr style="border-top:1px solid #e2e2e2; border-bottom:1px solid #e2e2e2;"><td colspan="2" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><p>안녕하세요,</p><p>&nbsp;</p><p>아래 링크를 클릭하시면 이메일 인증이 완료됩니다.</p><p><a href="{메일인증주소}" target="_blank" style="font-weight:bold;">메일인증 받기</a></p><p>&nbsp;</p><p>감사합니다.</p></td></tr></table>',
            'send_email_post_admin_title' => '[{게시판명}] {게시글제목}',
            'send_email_post_admin_content' => '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-left: 1px solid rgb(226,226,225);border-right: 1px solid rgb(226,226,225);background-color: rgb(255,255,255);border-top:10px solid #348fe2; border-bottom:5px solid #348fe2;border-collapse: collapse;"><tr><td width="200" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;">{홈페이지명}</td><td style="font-size:12px;padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><span style="font-size:14px;font-weight:bold;color:rgb(0,0,0)">{게시글제목}</span><br />작성자 : {게시글작성자닉네임}</td></tr><tr style="border-top:1px solid #e2e2e2; border-bottom:1px solid #e2e2e2;"><td colspan="2" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><div>{게시글내용}</div><p><a href="{게시글주소}" target="_blank" style="font-weight:bold;">사이트에서 게시글 확인하기</a></p><p>&nbsp;</p></td></tr></table>',
            'send_email_post_writer_title' => '[{게시판명}] {게시글제목}',
            'send_email_post_writer_content' => '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-left: 1px solid rgb(226,226,225);border-right: 1px solid rgb(226,226,225);background-color: rgb(255,255,255);border-top:10px solid #348fe2; border-bottom:5px solid #348fe2;border-collapse: collapse;"><tr><td width="200" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;">{홈페이지명}</td><td style="font-size:12px;padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><span style="font-size:14px;font-weight:bold;color:rgb(0,0,0)">{게시글제목}</span><br />작성자 : {게시글작성자닉네임}</td></tr><tr style="border-top:1px solid #e2e2e2; border-bottom:1px solid #e2e2e2;"><td colspan="2" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><div>{게시글내용}</div><p><a href="{게시글주소}" target="_blank" style="font-weight:bold;">사이트에서 게시글 확인하기</a></p><p>&nbsp;</p></td></tr></table>',
            'send_note_post_admin_title' => '[{게시판명}] {게시글제목}',
            'send_note_post_admin_content' => '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-left: 1px solid rgb(226,226,225);border-right: 1px solid rgb(226,226,225);background-color: rgb(255,255,255);border-top:10px solid #348fe2; border-bottom:5px solid #348fe2;border-collapse: collapse;"><tr><td width="200" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;">{홈페이지명}</td><td style="font-size:12px;padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><span style="font-size:14px;font-weight:bold;color:rgb(0,0,0)">{게시글제목}</span><br />작성자 : {게시글작성자닉네임}</td></tr><tr style="border-top:1px solid #e2e2e2; border-bottom:1px solid #e2e2e2;"><td colspan="2" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><div>{게시글내용}</div><p><a href="{게시글주소}" target="_blank" style="font-weight:bold;">사이트에서 게시글 확인하기</a></p><p>&nbsp;</p></td></tr></table>',
            'send_note_post_writer_title' => '[{게시판명}] {게시글제목}',
            'send_note_post_writer_content' => '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-left: 1px solid rgb(226,226,225);border-right: 1px solid rgb(226,226,225);background-color: rgb(255,255,255);border-top:10px solid #348fe2; border-bottom:5px solid #348fe2;border-collapse: collapse;"><tr><td width="200" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;">{홈페이지명}</td><td style="font-size:12px;padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><span style="font-size:14px;font-weight:bold;color:rgb(0,0,0)">{게시글제목}</span><br />작성자 : {게시글작성자닉네임}</td></tr><tr style="border-top:1px solid #e2e2e2; border-bottom:1px solid #e2e2e2;"><td colspan="2" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><div>{게시글내용}</div><p><a href="{게시글주소}" target="_blank" style="font-weight:bold;">사이트에서 게시글 확인하기</a></p><p>&nbsp;</p></td></tr></table>',
            'send_email_comment_admin_title' => '[{게시판명}] {게시글제목} - 댓글이 등록되었습니다',
            'send_email_comment_admin_content' => '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-left: 1px solid rgb(226,226,225);border-right: 1px solid rgb(226,226,225);background-color: rgb(255,255,255);border-top:10px solid #348fe2; border-bottom:5px solid #348fe2;border-collapse: collapse;"><tr><td width="200" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;">{홈페이지명}</td><td style="font-size:12px;padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><span style="font-size:14px;font-weight:bold;color:rgb(0,0,0)">{게시글제목}</span><br />작성자 : {게시글작성자닉네임}</td></tr><tr style="border-top:1px solid #e2e2e2; border-bottom:1px solid #e2e2e2;"><td colspan="2" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><div>{댓글내용}</div><p><a href="{댓글주소}" target="_blank" style="font-weight:bold;">사이트에서 댓글 확인하기</a></p><p>&nbsp;</p></td></tr></table>',
            'send_email_comment_post_writer_title' => '[{게시판명}] {게시글제목} - 댓글이 등록되었습니다',
            'send_email_comment_post_writer_content' => '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-left: 1px solid rgb(226,226,225);border-right: 1px solid rgb(226,226,225);background-color: rgb(255,255,255);border-top:10px solid #348fe2; border-bottom:5px solid #348fe2;border-collapse: collapse;"><tr><td width="200" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;">{홈페이지명}</td><td style="font-size:12px;padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><span style="font-size:14px;font-weight:bold;color:rgb(0,0,0)">{게시글제목}</span><br />작성자 : {게시글작성자닉네임}</td></tr><tr style="border-top:1px solid #e2e2e2; border-bottom:1px solid #e2e2e2;"><td colspan="2" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><div>{댓글내용}</div><p><a href="{댓글주소}" target="_blank" style="font-weight:bold;">사이트에서 댓글 확인하기</a></p><p>&nbsp;</p></td></tr></table>',
            'send_email_comment_comment_writer_title' => '[{게시판명}] {게시글제목} - 댓글이 등록되었습니다',
            'send_email_comment_comment_writer_content' => '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-left: 1px solid rgb(226,226,225);border-right: 1px solid rgb(226,226,225);background-color: rgb(255,255,255);border-top:10px solid #348fe2; border-bottom:5px solid #348fe2;border-collapse: collapse;"><tr><td width="200" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;">{홈페이지명}</td><td style="font-size:12px;padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><span style="font-size:14px;font-weight:bold;color:rgb(0,0,0)">{게시글제목}</span><br />작성자 : {게시글작성자닉네임}</td></tr><tr style="border-top:1px solid #e2e2e2; border-bottom:1px solid #e2e2e2;"><td colspan="2" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><div>{댓글내용}</div><p><a href="{댓글주소}" target="_blank" style="font-weight:bold;">사이트에서 댓글 확인하기</a></p><p>&nbsp;</p></td></tr></table>',
            'send_note_comment_admin_title' => '[{게시판명}] {게시글제목} - 댓글이 등록되었습니다',
            'send_note_comment_admin_content' => '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-left: 1px solid rgb(226,226,225);border-right: 1px solid rgb(226,226,225);background-color: rgb(255,255,255);border-top:10px solid #348fe2; border-bottom:5px solid #348fe2;border-collapse: collapse;"><tr><td width="200" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;">{홈페이지명}</td><td style="font-size:12px;padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><span style="font-size:14px;font-weight:bold;color:rgb(0,0,0)">{게시글제목}</span><br />작성자 : {게시글작성자닉네임}</td></tr><tr style="border-top:1px solid #e2e2e2; border-bottom:1px solid #e2e2e2;"><td colspan="2" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><div>{댓글내용}</div><p><a href="{댓글주소}" target="_blank" style="font-weight:bold;">사이트에서 댓글 확인하기</a></p><p>&nbsp;</p></td></tr></table>',
            'send_note_comment_post_writer_title' => '[{게시판명}] {게시글제목} - 댓글이 등록되었습니다',
            'send_note_comment_post_writer_content' => '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-left: 1px solid rgb(226,226,225);border-right: 1px solid rgb(226,226,225);background-color: rgb(255,255,255);border-top:10px solid #348fe2; border-bottom:5px solid #348fe2;border-collapse: collapse;"><tr><td width="200" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;">{홈페이지명}</td><td style="font-size:12px;padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><span style="font-size:14px;font-weight:bold;color:rgb(0,0,0)">{게시글제목}</span><br />작성자 : {게시글작성자닉네임}</td></tr><tr style="border-top:1px solid #e2e2e2; border-bottom:1px solid #e2e2e2;"><td colspan="2" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><div>{댓글내용}</div><p><a href="{댓글주소}" target="_blank" style="font-weight:bold;">사이트에서 댓글 확인하기</a></p><p>&nbsp;</p></td></tr></table>',
            'send_note_comment_comment_writer_title' => '[{게시판명}] {게시글제목} - 댓글이 등록되었습니다',
            'send_note_comment_comment_writer_content' => '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-left: 1px solid rgb(226,226,225);border-right: 1px solid rgb(226,226,225);background-color: rgb(255,255,255);border-top:10px solid #348fe2; border-bottom:5px solid #348fe2;border-collapse: collapse;"><tr><td width="200" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;">{홈페이지명}</td><td style="font-size:12px;padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><span style="font-size:14px;font-weight:bold;color:rgb(0,0,0)">{게시글제목}</span><br />작성자 : {게시글작성자닉네임}</td></tr><tr style="border-top:1px solid #e2e2e2; border-bottom:1px solid #e2e2e2;"><td colspan="2" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><div>{댓글내용}</div><p><a href="{댓글주소}" target="_blank" style="font-weight:bold;">사이트에서 댓글 확인하기</a></p><p>&nbsp;</p></td></tr></table>',
            'send_email_blame_admin_title' => '[{게시판명}] {게시글제목} - 신고가접수되었습니다',
            'send_email_blame_admin_content' => '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-left: 1px solid rgb(226,226,225);border-right: 1px solid rgb(226,226,225);background-color: rgb(255,255,255);border-top:10px solid #348fe2; border-bottom:5px solid #348fe2;border-collapse: collapse;"><tr><td width="200" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;">{홈페이지명}</td><td style="font-size:12px;padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><span style="font-size:14px;font-weight:bold;color:rgb(0,0,0)">{게시글제목}</span><br />게시글에 신고가 접수되었습니다</td></tr><tr style="border-top:1px solid #e2e2e2; border-bottom:1px solid #e2e2e2;"><td colspan="2" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><div>{게시글내용}</div><p><a href="{게시글주소}" target="_blank" style="font-weight:bold;">사이트에서 게시글 확인하기</a></p><p>&nbsp;</p></td></tr></table>',
            'send_email_blame_post_writer_title' => '[{게시판명}] {게시글제목} - 신고가접수되었습니다',
            'send_email_blame_post_writer_content' => '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-left: 1px solid rgb(226,226,225);border-right: 1px solid rgb(226,226,225);background-color: rgb(255,255,255);border-top:10px solid #348fe2; border-bottom:5px solid #348fe2;border-collapse: collapse;"><tr><td width="200" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;">{홈페이지명}</td><td style="font-size:12px;padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><span style="font-size:14px;font-weight:bold;color:rgb(0,0,0)">{게시글제목}</span><br />게시글에 신고가 접수되었습니다</td></tr><tr style="border-top:1px solid #e2e2e2; border-bottom:1px solid #e2e2e2;"><td colspan="2" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><div>{게시글내용}</div><p><a href="{게시글주소}" target="_blank" style="font-weight:bold;">사이트에서 게시글 확인하기</a></p><p>&nbsp;</p></td></tr></table>',
            'send_note_blame_admin_title' => '[{게시판명}] {게시글제목} - 신고가접수되었습니다',
            'send_note_blame_admin_content' => '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-left: 1px solid rgb(226,226,225);border-right: 1px solid rgb(226,226,225);background-color: rgb(255,255,255);border-top:10px solid #348fe2; border-bottom:5px solid #348fe2;border-collapse: collapse;"><tr><td width="200" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;">{홈페이지명}</td><td style="font-size:12px;padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><span style="font-size:14px;font-weight:bold;color:rgb(0,0,0)">{게시글제목}</span><br />게시글에 신고가 접수되었습니다</td></tr><tr style="border-top:1px solid #e2e2e2; border-bottom:1px solid #e2e2e2;"><td colspan="2" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><div>{게시글내용}</div><p><a href="{게시글주소}" target="_blank" style="font-weight:bold;">사이트에서 게시글 확인하기</a></p><p>&nbsp;</p></td></tr></table>',
            'send_note_blame_post_writer_title' => '[{게시판명}] {게시글제목} - 신고가접수되었습니다',
            'send_note_blame_post_writer_content' => '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-left: 1px solid rgb(226,226,225);border-right: 1px solid rgb(226,226,225);background-color: rgb(255,255,255);border-top:10px solid #348fe2; border-bottom:5px solid #348fe2;border-collapse: collapse;"><tr><td width="200" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;">{홈페이지명}</td><td style="font-size:12px;padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><span style="font-size:14px;font-weight:bold;color:rgb(0,0,0)">{게시글제목}</span><br />게시글에 신고가 접수되었습니다</td></tr><tr style="border-top:1px solid #e2e2e2; border-bottom:1px solid #e2e2e2;"><td colspan="2" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><div>{게시글내용}</div><p><a href="{게시글주소}" target="_blank" style="font-weight:bold;">사이트에서 게시글 확인하기</a></p><p>&nbsp;</p></td></tr></table>',
            'send_email_comment_blame_admin_title' => '[{게시판명}] {게시글제목} - 댓글에신고가접수되었습니다',
            'send_email_comment_blame_admin_content' => '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-left: 1px solid rgb(226,226,225);border-right: 1px solid rgb(226,226,225);background-color: rgb(255,255,255);border-top:10px solid #348fe2; border-bottom:5px solid #348fe2;border-collapse: collapse;"><tr><td width="200" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;">{홈페이지명}</td><td style="font-size:12px;padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><span style="font-size:14px;font-weight:bold;color:rgb(0,0,0)">{게시글제목}</span><br />댓글에 신고가 접수되었습니다</td></tr><tr style="border-top:1px solid #e2e2e2; border-bottom:1px solid #e2e2e2;"><td colspan="2" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><div>{댓글내용}</div><p><a href="{댓글주소}" target="_blank" style="font-weight:bold;">사이트에서 댓글 확인하기</a></p><p>&nbsp;</p></td></tr></table>',
            'send_email_comment_blame_post_writer_title' => '[{게시판명}] {게시글제목} - 댓글에신고가접수되었습니다',
            'send_email_comment_blame_post_writer_content' => '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-left: 1px solid rgb(226,226,225);border-right: 1px solid rgb(226,226,225);background-color: rgb(255,255,255);border-top:10px solid #348fe2; border-bottom:5px solid #348fe2;border-collapse: collapse;"><tr><td width="200" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;">{홈페이지명}</td><td style="font-size:12px;padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><span style="font-size:14px;font-weight:bold;color:rgb(0,0,0)">{게시글제목}</span><br />댓글에 신고가 접수되었습니다</td></tr><tr style="border-top:1px solid #e2e2e2; border-bottom:1px solid #e2e2e2;"><td colspan="2" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><div>{댓글내용}</div><p><a href="{댓글주소}" target="_blank" style="font-weight:bold;">사이트에서 댓글 확인하기</a></p><p>&nbsp;</p></td></tr></table>',
            'send_email_comment_blame_comment_writer_title' => '[{게시판명}] {게시글제목} - 댓글에신고가접수되었습니다',
            'send_email_comment_blame_comment_writer_content' => '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-left: 1px solid rgb(226,226,225);border-right: 1px solid rgb(226,226,225);background-color: rgb(255,255,255);border-top:10px solid #348fe2; border-bottom:5px solid #348fe2;border-collapse: collapse;"><tr><td width="200" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;">{홈페이지명}</td><td style="font-size:12px;padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><span style="font-size:14px;font-weight:bold;color:rgb(0,0,0)">{게시글제목}</span><br />댓글에 신고가 접수되었습니다</td></tr><tr style="border-top:1px solid #e2e2e2; border-bottom:1px solid #e2e2e2;"><td colspan="2" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><div>{댓글내용}</div><p><a href="{댓글주소}" target="_blank" style="font-weight:bold;">사이트에서 댓글 확인하기</a></p><p>&nbsp;</p></td></tr></table>',
            'send_note_comment_blame_admin_title' => '[{게시판명}] {게시글제목} - 댓글에신고가접수되었습니다',
            'send_note_comment_blame_admin_content' => '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-left: 1px solid rgb(226,226,225);border-right: 1px solid rgb(226,226,225);background-color: rgb(255,255,255);border-top:10px solid #348fe2; border-bottom:5px solid #348fe2;border-collapse: collapse;"><tr><td width="200" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;">{홈페이지명}</td><td style="font-size:12px;padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><span style="font-size:14px;font-weight:bold;color:rgb(0,0,0)">{게시글제목}</span><br />댓글에 신고가 접수되었습니다</td></tr><tr style="border-top:1px solid #e2e2e2; border-bottom:1px solid #e2e2e2;"><td colspan="2" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><div>{댓글내용}</div><p><a href="{댓글주소}" target="_blank" style="font-weight:bold;">사이트에서 댓글 확인하기</a></p><p>&nbsp;</p></td></tr></table>',
            'send_note_comment_blame_post_writer_title' => '[{게시판명}] {게시글제목} - 댓글에신고가접수되었습니다',
            'send_note_comment_blame_post_writer_content' => '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-left: 1px solid rgb(226,226,225);border-right: 1px solid rgb(226,226,225);background-color: rgb(255,255,255);border-top:10px solid #348fe2; border-bottom:5px solid #348fe2;border-collapse: collapse;"><tr><td width="200" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;">{홈페이지명}</td><td style="font-size:12px;padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><span style="font-size:14px;font-weight:bold;color:rgb(0,0,0)">{게시글제목}</span><br />댓글에 신고가 접수되었습니다</td></tr><tr style="border-top:1px solid #e2e2e2; border-bottom:1px solid #e2e2e2;"><td colspan="2" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><div>{댓글내용}</div><p><a href="{댓글주소}" target="_blank" style="font-weight:bold;">사이트에서 댓글 확인하기</a></p><p>&nbsp;</p></td></tr></table>',
            'send_note_comment_blame_comment_writer_title' => '[{게시판명}] {게시글제목} - 댓글에신고가접수되었습니다',
            'send_note_comment_blame_comment_writer_content' => '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-left: 1px solid rgb(226,226,225);border-right: 1px solid rgb(226,226,225);background-color: rgb(255,255,255);border-top:10px solid #348fe2; border-bottom:5px solid #348fe2;border-collapse: collapse;"><tr><td width="200" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;">{홈페이지명}</td><td style="font-size:12px;padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><span style="font-size:14px;font-weight:bold;color:rgb(0,0,0)">{게시글제목}</span><br />댓글에 신고가 접수되었습니다</td></tr><tr style="border-top:1px solid #e2e2e2; border-bottom:1px solid #e2e2e2;"><td colspan="2" style="padding:20px 30px;font-family: Arial,sans-serif;color: rgb(0,0,0);font-size: 14px;line-height: 20px;"><div>{댓글내용}</div><p><a href="{댓글주소}" target="_blank" style="font-weight:bold;">사이트에서 댓글 확인하기</a></p><p>&nbsp;</p></td></tr></table>',
        );
        $registerform = array(
            'mem_userid' => array(
                'field_name' => 'mem_userid',
                'func' => 'basic',
                'display_name' => '아이디',
                'field_type' => 'text',
                'use' => '1',
                'open' => '1',
                'required' => '1',
            ),
            'mem_email' => array(
                'field_name' => 'mem_email',
                'func' => 'basic',
                'display_name' => '이메일주소',
                'field_type' => 'email',
                'use' => '1',
                'open' => '',
                'required' => '1',
            ),
            'mem_password' => array(
                'field_name' => 'mem_password',
                'func' => 'basic',
                'display_name' => '비밀번호',
                'field_type' => 'password',
                'use' => '1',
                'open' => '',
                'required' => '1',
            ),
            'mem_username' => array(
                'field_name' => 'mem_username',
                'func' => 'basic',
                'display_name' => '이름',
                'field_type' => 'text',
                'use' => '',
                'open' => '',
                'required' => '',
            ),
            'mem_nickname' => array(
                'field_name' => 'mem_nickname',
                'func' => 'basic',
                'display_name' => '닉네임',
                'field_type' => 'text',
                'use' => '1',
                'open' => '1',
                'required' => '1',
            ),
            'mem_homepage' => array(
                'field_name' => 'mem_homepage',
                'func' => 'basic',
                'display_name' => '홈페이지',
                'field_type' => 'url',
                'use' => '',
                'open' => '1',
                'required' => '',
            ),
            'mem_phone' => array(
                'field_name' => 'mem_phone',
                'func' => 'basic',
                'display_name' => '전화번호',
                'field_type' => 'phone',
                'use' => '',
                'open' => '',
                'required' => '',
            ),
            'mem_birthday' => array(
                'field_name' => 'mem_birthday',
                'func' => 'basic',
                'display_name' => '생년월일',
                'field_type' => 'date',
                'use' => '',
                'open' => '',
                'required' => '',
            ),
            'mem_sex' => array(
                'field_name' => 'mem_sex',
                'func' => 'basic',
                'display_name' => '성별',
                'field_type' => 'radio',
                'use' => '',
                'open' => '',
                'required' => '',
            ),
            'mem_address' => array(
                'field_name' => 'mem_address',
                'func' => 'basic',
                'display_name' => '주소',
                'field_type' => 'address',
                'use' => '',
                'open' => '',
                'required' => '',
            ),
            'mem_profile_content' => array(
                'field_name' => 'mem_profile_content',
                'func' => 'basic',
                'display_name' => '자기소개',
                'field_type' => 'textarea',
                'use' => '',
                'open' => '1',
                'required' => '',
            ),
            'mem_recommend' => array(
                'field_name' => 'mem_recommend',
                'func' => 'basic',
                'display_name' => '추천인',
                'field_type' => 'text',
                'use' => '',
                'open' => '',
                'required' => '',
            ),
        );
        $configdata['registerform'] = json_encode($registerform);
        $this->cache->delete('config-model-get');
        $this->cache->clean();
        $this->Config_model->save($configdata);


        $hash = password_hash($this->input->post('mem_password'), PASSWORD_BCRYPT);
        $insertdata = array(
            'mem_userid' => $this->input->post('mem_userid'),
            'mem_email' => $this->input->post('mem_email'),
            'mem_password' => $hash,
            'mem_username' => $this->input->post('mem_nickname'),
            'mem_nickname' => $this->input->post('mem_nickname'),
            'mem_level' => 100,
            'mem_receive_email' => 1,
            'mem_use_note' => 1,
            'mem_receive_sms' => 1,
            'mem_open_profile' => 1,
            'mem_email_cert' => 1,
            'mem_register_datetime' => cdate('Y-m-d H:i:s'),
            'mem_register_ip' => $this->input->ip_address(),
            'mem_lastlogin_datetime' => cdate('Y-m-d H:i:s'),
            'mem_lastlogin_ip' => $this->input->ip_address(),
            'mem_is_admin' => 1,
        );
        $mem_id = $this->Member_model->insert($insertdata);

        $membermeta = array(
            'meta_change_pw_datetime' => cdate('Y-m-d H:i:s'),
            'meta_email_cert_datetime' => cdate('Y-m-d H:i:s'),
            'meta_open_profile_datetime' => cdate('Y-m-d H:i:s'),
            'meta_use_note_datetime' => cdate('Y-m-d H:i:s'),
            'meta_nickname_datetime' => cdate('Y-m-d H:i:s'),
        );
        $this->Member_meta_model->save($mem_id, $membermeta);

        $insertdata = array(
            'mem_id' => $mem_id,
            'mni_nickname' => $this->input->post('mem_nickname'),
            'mni_start_datetime' => cdate('Y-m-d H:i:s'),
        );
        $this->Member_nickname_model->insert($insertdata);

        $insertdata = array(
            'mem_id' => $mem_id,
            'mrg_ip' => $this->input->ip_address(),
            'mrg_datetime' => cdate('Y-m-d H:i:s'),
            'mrg_useragent' => $this->agent->agent_string(),
        );
        $this->Member_register_model->insert($insertdata);

        $insertdata = array(
            'doc_key' => 'aboutus',
            'doc_title' => '회사소개',
            'doc_content' => '회사소개 내용을 입력해주세요',
            'doc_content_html_type' => 1,
            'mem_id' => $mem_id,
            'doc_datetime' => cdate('Y-m-d H:i:s'),
            'doc_updated_mem_id' => $mem_id,
            'doc_updated_datetime' => cdate('Y-m-d H:i:s'),
        );
        $this->Document_model->insert($insertdata);

        $insertdata = array(
            'doc_key' => 'provision',
            'doc_title' => '이용약관',
            'doc_content' => '이용약관 내용을 입력해주세요',
            'doc_content_html_type' => 1,
            'mem_id' => $mem_id,
            'doc_datetime' => cdate('Y-m-d H:i:s'),
            'doc_updated_mem_id' => $mem_id,
            'doc_updated_datetime' => cdate('Y-m-d H:i:s'),
        );
        $this->Document_model->insert($insertdata);

        $insertdata = array(
            'doc_key' => 'privacy',
            'doc_title' => '개인정보 취급방침',
            'doc_content' => '개인정보 취급방침 내용을 입력해주세요',
            'doc_content_html_type' => 1,
            'mem_id' => $mem_id,
            'doc_datetime' => cdate('Y-m-d H:i:s'),
            'doc_updated_mem_id' => $mem_id,
            'doc_updated_datetime' => cdate('Y-m-d H:i:s'),
        );
        $this->Document_model->insert($insertdata);

        if ($this->input->post('autocreate')) {

            $insertdata = array(
                'fgr_title' => '자주하는 질문',
                'fgr_key' => 'faq',
                'fgr_datetime' => cdate('Y-m-d H:i:s'),
                'fgr_ip' => $this->input->ip_address(),
                'mem_id' => $mem_id,
            );
            $fgr_id = $this->Faq_group_model->insert($insertdata);

            $insertdata = array(
                'fgr_id' => $fgr_id,
                'faq_title' => '자주하는 질문 제목1 입니다',
                'faq_content' => '자주하는 질문 답변1 입니다',
                'faq_content_html_type' => 1,
                'faq_order' => 1,
                'faq_datetime' => cdate('Y-m-d H:i:s'),
                'faq_ip' => $this->input->ip_address(),
                'mem_id' => $mem_id,
            );
            $this->Faq_model->insert($insertdata);

            $insertdata = array(
                'fgr_id' => $fgr_id,
                'faq_title' => '자주하는 질문 제목2 입니다',
                'faq_content' => '자주하는 질문 답변2 입니다',
                'faq_content_html_type' => 1,
                'faq_order' => 2,
                'faq_datetime' => cdate('Y-m-d H:i:s'),
                'faq_ip' => $this->input->ip_address(),
                'mem_id' => $mem_id,
            );
            $this->Faq_model->insert($insertdata);

            $insertdata = array(
                'fgr_id' => $fgr_id,
                'faq_title' => '자주하는 질문 제목3 입니다',
                'faq_content' => '자주하는 질문 답변3 입니다',
                'faq_content_html_type' => 1,
                'faq_order' => 3,
                'faq_datetime' => cdate('Y-m-d H:i:s'),
                'faq_ip' => $this->input->ip_address(),
                'mem_id' => $mem_id,
            );
            $this->Faq_model->insert($insertdata);


            $metadata = array(
                'header_content' => '',
                'footer_content' => '',
                'mobile_header_content' => '',
                'mobile_footer_content' => '',
            );

            $insertdata = array(
                'bgr_key' => 'g-a',
                'bgr_name' => '그룹 A',
                'bgr_order' => 1,
            );
            $bgr_id_1 = $bgr_id = $this->Board_group_model->insert($insertdata);
            $this->Board_group_meta_model->save($bgr_id, $metadata);

            $insertdata = array(
                'bgr_key' => 'g-b',
                'bgr_name' => '그룹 B',
                'bgr_order' => 2,
            );
            $bgr_id_2 = $bgr_id = $this->Board_group_model->insert($insertdata);
            $this->Board_group_meta_model->save($bgr_id, $metadata);

            $insertdata = array(
                'bgr_key' => 'g-c',
                'bgr_name' => '그룹 C',
                'bgr_order' => 3,
            );
            $bgr_id_3 = $bgr_id = $this->Board_group_model->insert($insertdata);
            $this->Board_group_meta_model->save($bgr_id, $metadata);

            $metadata = array(
                'header_content' => '',
                'footer_content' => '',
                'mobile_header_content' => '',
                'mobile_footer_content' => '',
                'order_by_field' => 'post_num, post_reply',
                'list_count' => '20',
                'mobile_list_count' => '10',
                'page_count' => '5',
                'mobile_page_count' => '3',
                'show_list_from_view' => '1',
                'new_icon_hour' => '24',
                'hot_icon_hit' => '100',
                'hot_icon_day' => '30',
                'subject_length' => '60',
                'mobile_subject_length' => '40',
                'reply_order' => 'asc',
                'gallery_cols' => '4',
                'gallery_image_width' => '120',
                'gallery_image_height' => '90',
                'mobile_gallery_cols' => '2',
                'mobile_gallery_image_width' => '120',
                'mobile_gallery_image_height' => '90',
                'use_scrap' => '1',
                'use_post_like' => '1',
                'use_post_dislike' => '1',
                'use_print' => '1',
                'use_sns' => '1',
                'use_prev_next_post' => '1',
                'use_mobile_prev_next_post' => '1',
                'use_blame' => '1',
                'blame_blind_count' => '3',
                'syntax_highlighter' => '1',
                'comment_syntax_highlighter' => '1',
                'use_autoplay' => '1',
                'post_image_width' => '700',
                'post_mobile_image_width' => '400',
                'content_target_blank' => '1',
                'use_auto_url' => '1',
                'use_mobile_auto_url' => '1',
                'use_post_dhtml' => '1',
                'link_num' => '2',
                'use_upload_file' => '1',
                'upload_file_num' => '2',
                'mobile_upload_file_num' => '2',
                'upload_file_max_size' => '32',
                'comment_count' => '20',
                'mobile_comment_count' => '20',
                'comment_page_count' => '5',
                'mobile_comment_page_count' => '3',
                'use_comment_like' => '1',
                'use_comment_dislike' => '1',
                'use_comment_secret' => '1',
                'comment_order' => 'asc',
                'use_comment_blame' => '1',
                'comment_blame_blind_count' => '3',
                'protect_comment_num' => '5',
                'use_sideview' => '1',
                'use_tempsave' => '1',
            );
            $insertdata = array(
                'bgr_id' => $bgr_id_1,
                'brd_key' => 'b-a-1',
                'brd_name' => '게시판 A-1',
                'brd_order' => 1,
                'brd_search' => 1,
            );
            $brd_id = $this->Board_model->insert($insertdata);
            $this->Board_meta_model->save($brd_id, $metadata);

            $insertdata = array(
                'bgr_id' => $bgr_id_1,
                'brd_key' => 'b-a-2',
                'brd_name' => '게시판 A-2',
                'brd_order' => 2,
                'brd_search' => 1,
            );
            $brd_id = $this->Board_model->insert($insertdata);
            $this->Board_meta_model->save($brd_id, $metadata);

            $insertdata = array(
                'bgr_id' => $bgr_id_1,
                'brd_key' => 'b-a-3',
                'brd_name' => '게시판 A-3',
                'brd_order' => 3,
                'brd_search' => 1,
            );
            $brd_id = $this->Board_model->insert($insertdata);
            $this->Board_meta_model->save($brd_id, $metadata);

            $insertdata = array(
                'bgr_id' => $bgr_id_2,
                'brd_key' => 'b-b-1',
                'brd_name' => '게시판 B-1',
                'brd_order' => 11,
                'brd_search' => 1,
            );
            $brd_id = $this->Board_model->insert($insertdata);
            $this->Board_meta_model->save($brd_id, $metadata);

            $insertdata = array(
                'bgr_id' => $bgr_id_2,
                'brd_key' => 'b-b-2',
                'brd_name' => '게시판 B-2',
                'brd_order' => 12,
                'brd_search' => 1,
            );
            $brd_id = $this->Board_model->insert($insertdata);
            $this->Board_meta_model->save($brd_id, $metadata);

            $insertdata = array(
                'bgr_id' => $bgr_id_2,
                'brd_key' => 'b-b-3',
                'brd_name' => '게시판 B-3',
                'brd_order' => 13,
                'brd_search' => 1,
            );
            $brd_id = $this->Board_model->insert($insertdata);
            $this->Board_meta_model->save($brd_id, $metadata);

            $insertdata = array(
                'bgr_id' => $bgr_id_3,
                'brd_key' => 'b-c-1',
                'brd_name' => '게시판 C-1',
                'brd_order' => 21,
                'brd_search' => 1,
            );
            $brd_id = $this->Board_model->insert($insertdata);
            $this->Board_meta_model->save($brd_id, $metadata);

            $insertdata = array(
                'bgr_id' => $bgr_id_3,
                'brd_key' => 'b-c-2',
                'brd_name' => '게시판 C-2',
                'brd_order' => 22,
                'brd_search' => 1,
            );
            $brd_id = $this->Board_model->insert($insertdata);
            $this->Board_meta_model->save($brd_id, $metadata);

            $insertdata = array(
                'bgr_id' => $bgr_id_3,
                'brd_key' => 'b-c-3',
                'brd_name' => '게시판 C-3',
                'brd_order' => 23,
                'brd_search' => 1,
            );
            $brd_id = $this->Board_model->insert($insertdata);
            $this->Board_meta_model->save($brd_id, $metadata);

            $insertdata = array(
                'men_parent' => 0,
                'men_name' => '그룹A',
                'men_link' => group_url('g-a'),
                'men_desktop' => 1,
                'men_mobile' => 1,
                'men_order' => 10,
            );
            $men_id = $this->Menu_model->insert($insertdata);

            $insertdata = array(
                'men_parent' => $men_id,
                'men_name' => '게시판 A-1',
                'men_link' => board_url('b-a-1'),
                'men_desktop' => 1,
                'men_mobile' => 1,
                'men_order' => 11,
            );
            $this->Menu_model->insert($insertdata);

            $insertdata = array(
                'men_parent' => $men_id,
                'men_name' => '게시판 A-2',
                'men_link' => board_url('b-a-2'),
                'men_desktop' => 1,
                'men_mobile' => 1,
                'men_order' => 12,
            );
            $this->Menu_model->insert($insertdata);

            $insertdata = array(
                'men_parent' => $men_id,
                'men_name' => '게시판 A-3',
                'men_link' => board_url('b-a-3'),
                'men_desktop' => 1,
                'men_mobile' => 1,
                'men_order' => 13,
            );
            $this->Menu_model->insert($insertdata);

            $insertdata = array(
                'men_parent' => 0,
                'men_name' => '그룹B',
                'men_link' => group_url('g-b'),
                'men_desktop' => 1,
                'men_mobile' => 1,
                'men_order' => 20,
            );
            $men_id = $this->Menu_model->insert($insertdata);

            $insertdata = array(
                'men_parent' => $men_id,
                'men_name' => '게시판 B-1',
                'men_link' => board_url('b-b-1'),
                'men_desktop' => 1,
                'men_mobile' => 1,
                'men_order' => 21,
            );
            $this->Menu_model->insert($insertdata);

            $insertdata = array(
                'men_parent' => $men_id,
                'men_name' => '게시판 B-2',
                'men_link' => board_url('b-b-2'),
                'men_desktop' => 1,
                'men_mobile' => 1,
                'men_order' => 22,
            );
            $this->Menu_model->insert($insertdata);

            $insertdata = array(
                'men_parent' => $men_id,
                'men_name' => '게시판 B-3',
                'men_link' => board_url('b-b-3'),
                'men_desktop' => 1,
                'men_mobile' => 1,
                'men_order' => 23,
            );
            $this->Menu_model->insert($insertdata);

            $insertdata = array(
                'men_parent' => 0,
                'men_name' => '그룹C',
                'men_link' => group_url('g-c'),
                'men_desktop' => 1,
                'men_mobile' => 1,
                'men_order' => 30,
            );
            $men_id = $this->Menu_model->insert($insertdata);

            $insertdata = array(
                'men_parent' => $men_id,
                'men_name' => '게시판 C-1',
                'men_link' => board_url('b-c-1'),
                'men_desktop' => 1,
                'men_mobile' => 1,
                'men_order' => 31,
            );
            $this->Menu_model->insert($insertdata);

            $insertdata = array(
                'men_parent' => $men_id,
                'men_name' => '게시판 C-2',
                'men_link' => board_url('b-c-2'),
                'men_desktop' => 1,
                'men_mobile' => 1,
                'men_order' => 32,
            );
            $this->Menu_model->insert($insertdata);

            $insertdata = array(
                'men_parent' => $men_id,
                'men_name' => '게시판 C-3',
                'men_link' => board_url('b-c-3'),
                'men_desktop' => 1,
                'men_mobile' => 1,
                'men_order' => 33,
            );
            $this->Menu_model->insert($insertdata);

            $insertdata = array(
                'men_parent' => 0,
                'men_name' => '자주하는질문',
                'men_link' => faq_url('faq'),
                'men_desktop' => 1,
                'men_mobile' => 1,
                'men_order' => 40,
            );
            $men_id = $this->Menu_model->insert($insertdata);

        }

        $this->session->set_userdata(
            'mem_id',
            $mem_id
        );
    }


    /**
     * 회원가입시 닉네임을 체크하는 함수입니다
     */
    public function _mem_nickname_check($str)
    {
        $this->load->helper('chkstring');
        if (chkstring($str, _HANGUL_ + _ALPHABETIC_ + _NUMERIC_) === false) {
            $this->form_validation->set_message(
                '_mem_nickname_check',
                '닉네임은 공백없이 한글, 영문, 숫자만 입력 가능합니다'
            );
            return false;
        }
    }
}
