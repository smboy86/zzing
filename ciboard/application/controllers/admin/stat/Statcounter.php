<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Statcounter class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

/**
 * 관리자>통계관리>접속자통계 controller 입니다.
 */
class Statcounter extends CB_Controller
{

    /**
     * 관리자 페이지 상의 현재 디렉토리입니다
     * 페이지 이동시 필요한 정보입니다
     */
    public $pagedir = 'stat/statcounter';

    /**
     * 모델을 로딩합니다
     */
    protected $models = array('Stat_count', 'Stat_count_date');

    /**
     * 이 컨트롤러의 메인 모델 이름입니다
     */
    protected $modelname = 'Stat_count_model';

    /**
     * 헬퍼를 로딩합니다
     */
    protected $helpers = array('form', 'array');

    function __construct()
    {
        parent::__construct();

        /**
         * 라이브러리를 로딩합니다
         */
        $this->load->library(array('pagination', 'querystring'));
    }

    /**
     * 방문자 로그 목록을 가져옵니다
     */
    public function index()
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_admin_stat_statcounter_index';
        $this->load->event($eventname);

        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        /**
         * 페이지에 숫자가 아닌 문자가 입력되거나 1보다 작은 숫자가 입력되면 에러 페이지를 보여줍니다.
         */
        $param =& $this->querystring;
        $page = (((int) $this->input->get('page')) > 0) ? ((int) $this->input->get('page')) : 1;
        $findex = $this->input->get('findex') ? $this->input->get('findex') : $this->{$this->modelname}->primary_key;
        $forder = $this->input->get('forder', null, 'desc');
        $sfield = $this->input->get('sfield', null, '');
        $skeyword = $this->input->get('skeyword', null, '');

        $per_page = admin_listnum();
        $offset = ($page - 1) * $per_page;

        /**
         * 게시판 목록에 필요한 정보를 가져옵니다.
         */
        $this->{$this->modelname}->allow_search_field = array('sco_ip', 'sco_date', 'sco_referer', 'sco_current', 'sco_agent'); // 검색이 가능한 필드
        $this->{$this->modelname}->search_field_equal = array(); // 검색중 like 가 아닌 = 검색을 하는 필드
        $result = $this->{$this->modelname}
            ->get_admin_list($per_page, $offset, '', '', $findex, $forder, $sfield, $skeyword);

        if (element('list', $result)) {
            foreach (element('list', $result) as $key => $val) {
                if (element('sco_agent', $val)) {
                    $userAgent = get_useragent_info(element('sco_agent', $val));
                    $result['list'][$key]['browsername'] = $userAgent['browsername'];
                    $result['list'][$key]['browserversion'] = $userAgent['browserversion'];
                    $result['list'][$key]['os'] = $userAgent['os'];
                    $result['list'][$key]['engine'] = $userAgent['engine'];
                }
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
        $search_option = array('sco_ip' => 'IP', 'sco_date' => '날짜', 'sco_referer' => '이전주소', 'sco_current' => '현재주소', 'sco_agent' => 'OS/Browser');
        $view['view']['skeyword'] = ($sfield && array_key_exists($sfield, $search_option)) ? $skeyword : '';
        $view['view']['search_option'] = search_option($search_option, $sfield);
        $view['view']['listall_url'] = admin_url($this->pagedir);

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

        /**
         * 어드민 레이아웃을 정의합니다
         */
        $layoutconfig = array('layout' => 'layout', 'skin' => 'index');
        $view['layout'] = $this->managelayout->admin($layoutconfig, $this->cbconfig->get_device_view_type());
        $this->data = $view;
        $this->layout = element('layout_skin_file', element('layout', $view));
        $this->view = element('view_skin_file', element('layout', $view));
    }

    /**
     * 방문자 기간별 그래프를 가져옵니다
     */
    public function visit()
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_admin_stat_statcounter_visit';
        $this->load->event($eventname);

        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        /**
         * 페이지에 숫자가 아닌 문자가 입력되거나 1보다 작은 숫자가 입력되면 에러 페이지를 보여줍니다.
         */
        $param =& $this->querystring;
        $page = (((int) $this->input->get('page')) > 0) ? ((int) $this->input->get('page')) : 1;
        $findex = $this->input->get('findex') ? $this->input->get('findex') : $this->{$this->modelname}->primary_key;
        $forder = $this->input->get('forder', null, 'desc');

        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : cdate('Y-m-01');
        $end_date = $this->input->get('end_date') ? $this->input->get('end_date') : cdate('Y-m-') . cdate('t');

        $per_page = admin_listnum();
        $offset = ($page - 1) * $per_page;

        /**
         * 게시판 목록에 필요한 정보를 가져옵니다.
         */
        $where = array(
            'sco_date >=' => $start_date,
            'sco_date <=' => $end_date,
        );
        $result = $this->{$this->modelname}
            ->get_admin_list($per_page, $offset, $where, '', $findex, $forder);

        if (element('list', $result)) {
            foreach (element('list', $result) as $key => $val) {
                if (element('sco_agent', $val)) {
                    $userAgent = get_useragent_info(element('sco_agent', $val));
                    $result['list'][$key]['browsername'] = $userAgent['browsername'];
                    $result['list'][$key]['browserversion'] = $userAgent['browserversion'];
                    $result['list'][$key]['os'] = $userAgent['os'];
                    $result['list'][$key]['engine'] = $userAgent['engine'];
                }
            }
        }
        $view['view']['data'] = $result;
        $view['view']['start_date'] = $start_date;
        $view['view']['end_date'] = $end_date;

        /**
         * primary key 정보를 저장합니다
         */
        $view['view']['primary_key'] = $this->{$this->modelname}->primary_key;

        /**
         * 페이지네이션을 생성합니다
         */
        $config['base_url'] = admin_url($this->pagedir) . '/visit?' . $param->replace('page');
        $config['total_rows'] = $result['total_rows'];
        $config['per_page'] = $per_page;
        $this->pagination->initialize($config);
        $view['view']['paging'] = $this->pagination->create_links();
        $view['view']['page'] = $page;

        /**
         * 쓰기 주소, 삭제 주소등 필요한 주소를 구합니다
         */
        $view['view']['listall_url'] = admin_url($this->pagedir . '/visit');

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

        /**
         * 어드민 레이아웃을 정의합니다
         */
        $layoutconfig = array('layout' => 'layout', 'skin' => 'visit');
        $view['layout'] = $this->managelayout->admin($layoutconfig, $this->cbconfig->get_device_view_type());
        $this->data = $view;
        $this->layout = element('layout_skin_file', element('layout', $view));
        $this->view = element('view_skin_file', element('layout', $view));
    }

    /**
     * 접속자통계 >도메인 을 가져옵니다
     */
    public function domain()
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_admin_stat_statcounter_domain';
        $this->load->event($eventname);

        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        $param =& $this->querystring;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : cdate('Y-m-01');
        $end_date = $this->input->get('end_date') ? $this->input->get('end_date') : cdate('Y-m-') . cdate('t');

        $result = $this->{$this->modelname}->get_by_date($start_date, $end_date);
        $sum_count = 0;
        $arr = array();
        $max = 0;
        if ($result && is_array($result)) {
            foreach ($result as $key => $value) {
                preg_match("/^http[s]*:\/\/([\.\-\_0-9a-zA-Z]*)\//", element('sco_referer', $value), $match);

                $s = $match ? $match[1] : '-';
                $s = preg_replace(
                    "/^(www\.|search\.|dirsearch\.|dir\.search\.|dir\.|kr\.search\.|myhome\.)(.*)/",
                    "\\2",
                    $s
                );
                if ( ! isset($arr[$s])) {
                    $arr[$s] = 0;
                }
                $arr[$s]++;

                if ($arr[$s] > $max) {
                    $max = $arr[$s];
                }
                $sum_count++;
            }
        }

        $view['view']['list'] = array();
        $i = 0;
        $k = 0;
        $save_count = -1;
        $tot_count = 0;

        if (count($arr)) {
            arsort($arr);
            foreach ($arr as $key => $value) {
                $count = (int) $arr[$key];
                $view['view']['list'][$k]['count'] = $count;
                $i++;
                if ($save_count !== $count) {
                    $no = $i;
                    $save_count = $count;
                }
                $view['view']['list'][$k]['no'] = $no;

                if ($key === '-') {
                    $key = '직접';
                }
                $view['view']['list'][$k]['key'] = $key;
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

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

        /**
         * 어드민 레이아웃을 정의합니다
         */
        $layoutconfig = array('layout' => 'layout', 'skin' => 'domain');
        $view['layout'] = $this->managelayout->admin($layoutconfig, $this->cbconfig->get_device_view_type());
        $this->data = $view;
        $this->layout = element('layout_skin_file', element('layout', $view));
        $this->view = element('view_skin_file', element('layout', $view));
    }

    /**
     * 접속자통계 >브라우저 를 가져옵니다
     */
    public function browser()
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_admin_stat_statcounter_browser';
        $this->load->event($eventname);

        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        $param =& $this->querystring;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : cdate('Y-m-01');
        $end_date = $this->input->get('end_date') ? $this->input->get('end_date') : cdate('Y-m-') . cdate('t');

        $result = $this->{$this->modelname}->get_by_date($start_date, $end_date);
        $sum_count = 0;
        $arr = array();
        $max = 0;
        if ($result && is_array($result)) {
            foreach ($result as $key => $value) {
                $userAgent = get_useragent_info(element('sco_agent', $val));
                $s = $userAgent['browsername'];
                if (empty($s)) {
                    $s = '-';
                }
                if ( ! isset($arr[$s])) {
                    $arr[$s] = 0;
                }
                $arr[$s]++;

                if ($arr[$s] > $max) {
                    $max = $arr[$s];
                }

                $sum_count++;
            }
        }

        $view['view']['list'] = array();
        $i = 0;
        $k = 0;
        $save_count = -1;
        $tot_count = 0;

        if (count($arr)) {
            arsort($arr);
            foreach ($arr as $key => $value) {
                $count = (int) $arr[$key];
                $view['view']['list'][$k]['count'] = $count;
                $i++;
                if ($save_count !== $count) {
                    $no = $i;
                    $save_count = $count;
                }
                $view['view']['list'][$k]['no'] = $no;

                if ($key === '-') {
                    $key = '알수없음';
                }
                $view['view']['list'][$k]['key'] = $key;
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

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

        /**
         * 어드민 레이아웃을 정의합니다
         */
        $layoutconfig = array('layout' => 'layout', 'skin' => 'browser');
        $view['layout'] = $this->managelayout->admin($layoutconfig, $this->cbconfig->get_device_view_type());
        $this->data = $view;
        $this->layout = element('layout_skin_file', element('layout', $view));
        $this->view = element('view_skin_file', element('layout', $view));
    }

    /**
     * 접속자통계 >운영체제 를 가져옵니다
     */
    public function os()
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_admin_stat_statcounter_os';
        $this->load->event($eventname);

        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        $param =& $this->querystring;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : cdate('Y-m-01');
        $end_date = $this->input->get('end_date') ? $this->input->get('end_date') : cdate('Y-m-') . cdate('t');

        $result = $this->{$this->modelname}->get_by_date($start_date, $end_date);
        $sum_count = 0;
        $arr = array();
        $max = 0;
        if ($result && is_array($result)) {
            foreach ($result as $key => $value) {
                $userAgent = get_useragent_info(element('sco_agent', $val));
                $s = $userAgent['os'];
                if (empty($s)) {
                    $s = '-';
                }
                if ( ! isset($arr[$s])) {
                    $arr[$s] = 0;
                }
                $arr[$s]++;

                if ($arr[$s] > $max) {
                    $max = $arr[$s];
                }

                $sum_count++;
            }
        }

        $view['view']['list'] = array();
        $i = 0;
        $k = 0;
        $save_count = -1;
        $tot_count = 0;

        if (count($arr)) {
            arsort($arr);
            foreach ($arr as $key => $value) {
                $count = (int) $arr[$key];
                $view['view']['list'][$k]['count'] = $count;
                $i++;
                if ($save_count !== $count) {
                    $no = $i;
                    $save_count = $count;
                }
                $view['view']['list'][$k]['no'] = $no;

                if ($key === '-') {
                    $key = '알수없음';
                }
                $view['view']['list'][$k]['key'] = $key;
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

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

        /**
         * 어드민 레이아웃을 정의합니다
         */
        $layoutconfig = array('layout' => 'layout', 'skin' => 'os');
        $view['layout'] = $this->managelayout->admin($layoutconfig, $this->cbconfig->get_device_view_type());
        $this->data = $view;
        $this->layout = element('layout_skin_file', element('layout', $view));
        $this->view = element('view_skin_file', element('layout', $view));
    }

    /**
     * 접속자통계 >시간 을 가져옵니다
     */
    public function hour()
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_admin_stat_statcounter_hour';
        $this->load->event($eventname);

        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        $param =& $this->querystring;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : cdate('Y-m-01');
        $end_date = $this->input->get('end_date') ? $this->input->get('end_date') : cdate('Y-m-') . cdate('t');

        $result = $this->{$this->modelname}->get_by_time_hour($start_date, $end_date);
        $arr = array();
        $max = 0;
        $sum_count = 0;

        $k= 0;
        if ($result && is_array($result)) {
            foreach ($result as $key => $value) {
                $arr[$value['time']] = element('cnt', $value);

                if (element('cnt', $value) > $max) {
                    $max = element('cnt', $value);
                }
                $sum_count += element('cnt', $value);
            }
        }
        if ($result && is_array($result)) {
            foreach ($result as $key => $value) {
                $count = element('cnt', $value);
                $result[$k]['count'] = $count;

                $rate = ($count / $sum_count * 100);
                $result[$k]['rate'] = $rate;
                $s_rate = number_format($rate, 1);
                $result[$k]['s_rate'] = $s_rate;

                $bar = (int)($count / $max * 100);
                $result[$k]['bar'] = $bar;

                $k++;
            }

            $view['view']['max_value'] = $max;
            $view['view']['sum_count'] = $sum_count;
        }

        $view['view']['list'] = $result;

        $view['view']['start_date'] = $start_date;
        $view['view']['end_date'] = $end_date;
        $view['view']['sum_count'] = $sum_count;

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

        /**
         * 어드민 레이아웃을 정의합니다
         */
        $layoutconfig = array('layout' => 'layout', 'skin' => 'hour');
        $view['layout'] = $this->managelayout->admin($layoutconfig, $this->cbconfig->get_device_view_type());
        $this->data = $view;
        $this->layout = element('layout_skin_file', element('layout', $view));
        $this->view = element('view_skin_file', element('layout', $view));
    }

    /**
     * 접속자통계 >요일 을 가져옵니다
     */
    public function week()
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_admin_stat_statcounter_week';
        $this->load->event($eventname);

        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        $param =& $this->querystring;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : cdate('Y-m-01');
        $end_date = $this->input->get('end_date') ? $this->input->get('end_date') : cdate('Y-m-') . cdate('t');

        $result = $this->Stat_count_date_model->get_by_time_week($start_date, $end_date);
        $arr = array();
        $max = 0;
        $sum_count = 0;
        $week_korean = array('월', '화', '수', '목', '금', '토', '일');

        $k= 0;
        if ($result && is_array($result)) {
            foreach ($result as $key => $value) {
                $arr[$value['date']] = $value['scd_count'];

                if ($value['scd_count'] > $max) {
                    $max = $value['scd_count'];
                }
                $sum_count += $value['scd_count'];
            }
        }
        if ($result && is_array($result)) {
            foreach ($result as $key => $value) {
                $count = $value['scd_count'];
                $result[$k]['count'] = $count;

                $result[$k]['week'] = $week_korean[$value['date']];

                $rate = ($count / $sum_count * 100);
                $result[$k]['rate'] = $rate;
                $s_rate = number_format($rate, 1);
                $result[$k]['s_rate'] = $s_rate;

                $bar = (int)($count / $max * 100);
                $result[$k]['bar'] = $bar;

                $k++;
            }

            $view['view']['max_value'] = $max;
            $view['view']['sum_count'] = $sum_count;
        }

        $view['view']['list'] = $result;

        $view['view']['start_date'] = $start_date;
        $view['view']['end_date'] = $end_date;
        $view['view']['sum_count'] = $sum_count;

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

        /**
         * 어드민 레이아웃을 정의합니다
         */
        $layoutconfig = array('layout' => 'layout', 'skin' => 'week');
        $view['layout'] = $this->managelayout->admin($layoutconfig, $this->cbconfig->get_device_view_type());
        $this->data = $view;
        $this->layout = element('layout_skin_file', element('layout', $view));
        $this->view = element('view_skin_file', element('layout', $view));
    }

    /**
     * 접속자통계 >일 을 가져옵니다
     */
    public function day()
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_admin_stat_statcounter_day';
        $this->load->event($eventname);

        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        $param =& $this->querystring;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : cdate('Y-m-01');
        $end_date = $this->input->get('end_date') ? $this->input->get('end_date') : cdate('Y-m-') . cdate('t');

        $result = $this->Stat_count_date_model->get_by_time_day($start_date, $end_date);
        $arr = array();
        $max = 0;
        $sum_count = 0;

        $k= 0;
        if ($result && is_array($result)) {
            foreach ($result as $key => $value) {
                $arr[$value['scd_date']] = $value['scd_count'];

                if ($value['scd_count'] > $max) {
                    $max = $value['scd_count'];
                }

                $sum_count += $value['scd_count'];
            }
        }
        if ($result && is_array($result)) {
            foreach ($result as $key => $value) {
                $count = $value['scd_count'];
                $result[$k]['count'] = $count;

                $rate = ($count / $sum_count * 100);
                $result[$k]['rate'] = $rate;
                $s_rate = number_format($rate, 1);
                $result[$k]['s_rate'] = $s_rate;

                $bar = (int)($count / $max * 100);
                $result[$k]['bar'] = $bar;

                $k++;
            }

            $view['view']['max_value'] = $max;
            $view['view']['sum_count'] = $sum_count;
        }

        $view['view']['list'] = $result;

        $view['view']['start_date'] = $start_date;
        $view['view']['end_date'] = $end_date;
        $view['view']['sum_count'] = $sum_count;

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

        /**
         * 어드민 레이아웃을 정의합니다
         */
        $layoutconfig = array('layout' => 'layout', 'skin' => 'day');
        $view['layout'] = $this->managelayout->admin($layoutconfig, $this->cbconfig->get_device_view_type());
        $this->data = $view;
        $this->layout = element('layout_skin_file', element('layout', $view));
        $this->view = element('view_skin_file', element('layout', $view));
    }

    /**
     * 접속자통계 >월 을 가져옵니다
     */
    public function month()
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_admin_stat_statcounter_month';
        $this->load->event($eventname);

        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        $param =& $this->querystring;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : cdate('Y-m-01');
        $end_date = $this->input->get('end_date') ? $this->input->get('end_date') : cdate('Y-m-') . cdate('t');

        $result = $this->Stat_count_date_model->get_by_time_month($start_date, $end_date);
        $arr = array();
        $max = 0;
        $sum_count = 0;

        $k= 0;
        if ($result && is_array($result)) {
            foreach ($result as $key => $value) {
                $arr[$value['date']] = $value['scd_count'];

                if ($value['scd_count'] > $max) {
                    $max = $value['scd_count'];
                }

                $sum_count += $value['scd_count'];
            }
        }
        if ($result && is_array($result)) {
            foreach ($result as $key => $value) {
                $count = $value['scd_count'];
                $result[$k]['count'] = $count;

                $rate = ($count / $sum_count * 100);
                $result[$k]['rate'] = $rate;
                $s_rate = number_format($rate, 1);
                $result[$k]['s_rate'] = $s_rate;

                $bar = (int)($count / $max * 100);
                $result[$k]['bar'] = $bar;

                $k++;
            }

            $view['view']['max_value'] = $max;
            $view['view']['sum_count'] = $sum_count;
        }

        $view['view']['list'] = $result;

        $view['view']['start_date'] = $start_date;
        $view['view']['end_date'] = $end_date;
        $view['view']['sum_count'] = $sum_count;

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

        /**
         * 어드민 레이아웃을 정의합니다
         */
        $layoutconfig = array('layout' => 'layout', 'skin' => 'month');
        $view['layout'] = $this->managelayout->admin($layoutconfig, $this->cbconfig->get_device_view_type());
        $this->data = $view;
        $this->layout = element('layout_skin_file', element('layout', $view));
        $this->view = element('view_skin_file', element('layout', $view));
    }

    /**
     * 접속자통계 >년 을 가져옵니다
     */
    public function year()
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_admin_stat_statcounter_year';
        $this->load->event($eventname);

        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        $param =& $this->querystring;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : cdate('Y-m-01');
        $end_date = $this->input->get('end_date') ? $this->input->get('end_date') : cdate('Y-m-') . cdate('t');

        $result = $this->Stat_count_date_model->get_by_time_year($start_date, $end_date);
        $arr = array();
        $max = 0;
        $sum_count = 0;

        $k= 0;
        if ($result && is_array($result)) {
            foreach ($result as $key => $value) {
                $arr[$value['date']] = $value['scd_count'];

                if ($value['scd_count'] > $max) {
                    $max = $value['scd_count'];
                }

                $sum_count += $value['scd_count'];
            }
        }
        if ($result && is_array($result)) {
            foreach ($result as $key => $value) {
                $count = $value['scd_count'];
                $result[$k]['count'] = $count;

                $rate = ($count / $sum_count * 100);
                $result[$k]['rate'] = $rate;
                $s_rate = number_format($rate, 1);
                $result[$k]['s_rate'] = $s_rate;

                $bar = (int)($count / $max * 100);
                $result[$k]['bar'] = $bar;

                $k++;
            }

            $view['view']['max_value'] = $max;
            $view['view']['sum_count'] = $sum_count;
        }

        $view['view']['list'] = $result;

        $view['view']['start_date'] = $start_date;
        $view['view']['end_date'] = $end_date;
        $view['view']['sum_count'] = $sum_count;

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

        /**
         * 어드민 레이아웃을 정의합니다
         */
        $layoutconfig = array('layout' => 'layout', 'skin' => 'year');
        $view['layout'] = $this->managelayout->admin($layoutconfig, $this->cbconfig->get_device_view_type());
        $this->data = $view;
        $this->layout = element('layout_skin_file', element('layout', $view));
        $this->view = element('view_skin_file', element('layout', $view));
    }

    /**
     * 오래된로그 삭제>방문자로그삭제 페이지입니다
     */
    public function cleanlog()
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_admin_stat_statcounter_cleanlog';
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
            array(
                'field' => 'day',
                'label' => '기간',
                'rules' => 'trim|required|numeric|is_natural',
            ),
        );
        $this->form_validation->set_rules($config);

        /**
         * 유효성 검사를 하지 않는 경우, 또는 유효성 검사에 실패한 경우입니다.
         * 즉 글쓰기나 수정 페이지를 보고 있는 경우입니다
         */
        if ($this->form_validation->run() === false) {

            // 이벤트가 존재하면 실행합니다
            $view['view']['event']['formrunfalse'] = Events::trigger('formrunfalse', $eventname);

        } else {
            /**
             * 유효성 검사를 통과한 경우입니다.
             * 즉 데이터의 insert 나 update 의 process 처리가 필요한 상황입니다
             */

            // 이벤트가 존재하면 실행합니다
            $view['view']['event']['formruntrue'] = Events::trigger('formruntrue', $eventname);

            if ($this->input->post('criterion') && $this->input->post('day')) {
                $deletewhere = array(
                    'sco_date <=' => $this->input->post('criterion'),
                );
                $this->Stat_count_model->delete_where($deletewhere);

                $deletewhere = array(
                    'scd_date <=' => $this->input->post('criterion'),
                );
                $this->Stat_count_date_model->delete_where($deletewhere);

                $view['view']['alert_message'] = '총 ' . number_format($this->input->post('log_count'))
                    . ' 건의 ' . $this->input->post('day') . '일 이상된 방문자로그가 모두 삭제되었습니다';
            } else {
                $criterion = cdate('Y-m-d', ctimestamp() - $this->input->post('day') * 24 * 60 * 60);
                $countwhere = array(
                    'sco_date <=' => $criterion,
                );
                $log_count = $this->Stat_count_model->count_by($countwhere);
                $view['view']['criterion'] = $criterion;
                $view['view']['day'] = $this->input->post('day');
                $view['view']['log_count'] = $log_count;
                if ($log_count > 0) {
                    $view['view']['msg'] = '총 ' . number_format($log_count) . ' 건의 ' . $this->input->post('day')
                        . '일 이상된 방문자로그가 발견되었습니다. 이를 모두 삭제하시겠습니까?';
                } else {
                    $view['view']['alert_message'] = $this->input->post('day') . '일 이상된 방문자로그가 발견되지 않았습니다';
                }
            }
        }

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before_layout'] = Events::trigger('before_layout', $eventname);

        /**
         * 어드민 레이아웃을 정의합니다
         */
        $layoutconfig = array('layout' => 'layout', 'skin' => 'cleanlog');
        $view['layout'] = $this->managelayout->admin($layoutconfig, $this->cbconfig->get_device_view_type());
        $this->data = $view;
        $this->layout = element('layout_skin_file', element('layout', $view));
        $this->view = element('view_skin_file', element('layout', $view));
    }
}
