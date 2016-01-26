<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Registercounter class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

/**
 * 관리자>통계관리>회원가입통계 controller 입니다.
 */
class Registercounter extends CB_Controller
{

    /**
     * 관리자 페이지 상의 현재 디렉토리입니다
     * 페이지 이동시 필요한 정보입니다
     */
    public $pagedir = 'stat/registercounter';

    /**
     * 모델을 로딩합니다
     */
    protected $models = array('Member');

    /**
     * 이 컨트롤러의 메인 모델 이름입니다
     */
    protected $modelname = 'Member_model';

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
        $this->load->library(array('querystring'));
    }

    /**
     * 목록을 가져오는 메소드입니다
     */
    public function index()
    {
        // 이벤트 라이브러리를 로딩합니다
        $eventname = 'event_admin_stat_registercounter_index';
        $this->load->event($eventname);

        $view = array();
        $view['view'] = array();

        // 이벤트가 존재하면 실행합니다
        $view['view']['event']['before'] = Events::trigger('before', $eventname);

        $param =& $this->querystring;
        $datetype = $this->input->get('datetype', null, 'd');
        if ($datetype !== 'm' && $datetype !== 'y') {
            $datetype = 'd';
        }
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : cdate('Y-m-01');
        $end_date = $this->input->get('end_date') ? $this->input->get('end_date') : cdate('Y-m-') . cdate('t');

        $result = $this->{$this->modelname}->get_register_count($datetype, $start_date, $end_date);
        $sum_count = 0;
        $arr = array();
        $max = 0;

        if ($result && is_array($result)) {
            foreach ($result as $key => $value) {
                $s = element('day', $value);
                if ( ! isset($arr[$s])) {
                    $arr[$s] = 0;
                }
                $arr[$s] += element('cnt', $value);

                if ($arr[$s] > $max) {
                    $max = $arr[$s];
                }
                $sum_count += element('cnt', $value);
            }
        }

        $view['view']['list'] = array();
        $i = 0;
        $k = 0;
        $save_count = -1;
        $tot_count = 0;

        if (count($arr)) {
            foreach ($arr as $key => $value) {
                $count = (int) $arr[$key];
                $view['view']['list'][$k]['count'] = $count;
                $i++;
                if ($save_count !== $count) {
                    $no = $i;
                    $save_count = $count;
                }
                $view['view']['list'][$k]['no'] = $no;

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
        $layoutconfig = array('layout' => 'layout', 'skin' => 'index');
        $view['layout'] = $this->managelayout->admin($layoutconfig, $this->cbconfig->get_device_view_type());
        $this->data = $view;
        $this->layout = element('layout_skin_file', element('layout', $view));
        $this->view = element('view_skin_file', element('layout', $view));
    }
}
