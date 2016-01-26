<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Member class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

/**
 * member table 을 관리하는 class 입니다.
 */
class Member extends CI_Controller
{

    private $CI;

    private $mb;


    function __construct()
    {
        $this->CI = & get_instance();
        $this->CI->load->model( array('Member_model', 'Member_meta_model', 'Member_extra_vars_model'));
        $this->CI->load->helper( array('array'));
    }


    /**
     * 접속한 유저가 회원인지 아닌지를 판단합니다
     */
    public function is_member()
    {
        if ($this->CI->session->userdata('mem_id')) {
            return $this->CI->session->userdata('mem_id');
        } else {
            return false;
        }
    }


    /**
     * 접속한 유저가 관리자인지 아닌지를 판단합니다
     */
    public function is_admin($check = array())
    {
        if ($this->item('mem_is_admin')) {
            return 'super';
        } elseif (element('group_id', $check)) {
            $this->CI->load->library('board_group');
            return $this->CI->board_group->is_admin(element('group_id', $check)) ? 'group' : false;
        } elseif (element('board_id', $check)) {
            $this->CI->load->library('board');
            return $this->CI->board->is_admin(element('board_id', $check)) ? 'board' : false;
        } else {
            return false;
        }
    }


    /**
     * member, member_extra_vars, member_meta 테이블에서 정보를 가져옵니다
     */
    public function get_member()
    {
        if ($this->is_member()) {
            if (empty($this->mb)) {
                $member = $this->CI->Member_model->get_by_memid($this->is_member());
                $extras = $this->get_all_extras(element('mem_id', $member));
                if (is_array($extras)) {
                    $member = array_merge($member, $extras);
                }
                $metas = $this->get_all_meta(element('mem_id', $member));
                if (is_array($metas)) {
                    $member = array_merge($member, $metas);
                }
                $this->mb = $member;
            }
            return $this->mb;
        } else {
            return false;
        }
    }


    /**
     * get_member 에서 가져온 데이터의 item 을 보여줍니다
     */
    public function item($column = '')
    {
        if (empty($column)) {
            return false;
        }
        if (empty($this->mb)) {
            $this->get_member();
        }
        if (empty($this->mb)) {
            return false;
        }
        $member = $this->mb;

        return isset($member[$column]) ? $member[$column] : false;
    }


    /**
     * member_extra_vars 테이블에서 가져옵니다
     */
    public function get_all_extras($mem_id = 0)
    {
        if (empty($mem_id)) {
            return false;
        }

        $result = array();
        $where = array(
            'mem_id' => $mem_id,
        );
        $res = $this->CI->Member_extra_vars_model->get('', '', $where);
        if ($res && is_array($res)) {
            foreach ($res as $val) {
                $result[$val['mev_key']] = $val['mev_value'];
            }
        }

        return $result;
    }


    /**
     * member_meta 테이블에서 가져옵니다
     */
    public function get_all_meta($mem_id = 0)
    {
        $mem_id = (int) $mem_id;
        if (empty($mem_id) OR $mem_id < 1) {
            return false;
        }

        $result = array();
        $where = array(
            'mem_id' => $mem_id,
        );
        $res = $this->CI->Member_meta_model->get('', '', $where);
        if ($res && is_array($res)) {
            foreach ($res as $val) {
                if (element('mmt_key', $val)) {
                    $result[element('mmt_key', $val)] = element('mmt_value', $val);
                }
            }
        }
        return $result;
    }


    /**
     * 로그인 기록을 남깁니다
     */
    public function update_login_log($mem_id= 0, $success= 0, $reason = '')
    {
        $success = $success ? 1 : 0;
        $mem_id = (int) $mem_id ? (int) $mem_id : 0;
        $reason = isset($reason) ? $reason : '';
        $referer = $this->CI->input->get_post('url', null, '');
        $loginlog = array(
            'mll_success' => $success,
            'mem_id' => $mem_id,
            'mll_datetime' => cdate('Y-m-d H:i:s'),
            'mll_ip' => $this->CI->input->ip_address(),
            'mll_reason' => $reason,
            'mll_useragent' => $this->CI->agent->agent_string(),
            'mll_url' => current_full_url(),
            'mll_referer' => $referer,
        );
        $this->CI->Member_login_log_model->insert($loginlog);

        return true;
    }
}
