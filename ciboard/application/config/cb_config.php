<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
 * CiBoard 주 : true 일 경우 관리자페이지의 기본환경설정 > 접근기능 기능을 사용합니다.
 * 만약 사용을 원치 않는 경우는 false 로 변경해주시면 됩니다.
 */
$config['use_lock_ip'] = true;



/**
 * CiBoard 주 : install 되었는지를 매번 체크하고 install 되어있지 않으면 install page 로 이동합니다.
 * 사이트 install 한 후에는 값을 false 로 변경해주세요.
 */
$config['chk_installed'] = true;
$config['install_ip'] = '';

/**
 * 설치를 진행하기 위해서는 현재 접속하고 계신 remote_addr 를 입력하셔야 합니다.
 * 설치가 끝난 후에는 다시 빈 값으로 변경해주시고, 바로 위에 chk_installed 의 값을 false 로 변경해주시면 매번 install 되었는지를 체크하지 않으므로 속도 향상에 도움이 됩니다.
 */


/**
 * profiler 를 활성화할지 결정합니다.
 * 사이트를 개발시에는 profiler 를 활성화하여놓고 개발하시면 각페이지에서 profiler 를 확인할 수 있습니다.
 * profiler 활성화 선언은 application/core/CB_Controller.php 의 __construct() 함수에 있습니다,
 */
$config['enable_profiler'] = false;


/**
 * user agent parser 를 선택합니다
 * phpuseragent , browscap 둘 중에 선택, browscap 를 선택시 메모리 부족 현상이 발생할 수 있습니다. 그리고
 * browscap 을 사용시에는 관리자페이지 > 환경설정 > Browscap 업데이트 페이지에서 캐시 업데이트를 해주셔야 합니다.
 */
$config['user_agent_parser'] = 'phpuseragent';  // phpuseragent , browscap 둘 중에 선택


/**
 * CiBoard 주 : smpt email 을 사용하시는 경우 세팅해주세요
 */
$config['email_protocal'] = 'mail'; // mail/sendmail/smtp
$config['email_smtp_host'] = '';
$config['email_smtp_user'] = '';
$config['email_smtp_pass'] = '';
$config['email_smtp_port'] = '25';
$config['email_smtp_crypto'] = 'ssl'; // SMTP Encryption. Can be null, tls or ssl.


/**
 * CiBoard 주 : 캐시 기능 사용시, 우선순위를 결정합니다
 */
$config['cache_method'] = array('adapter' => 'file', 'backup' => 'file');
