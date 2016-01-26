<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Post Extra Vars model class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

class Post_extra_vars_model extends CB_Model
{

    /**
     * 테이블명
     */
    public $_table = 'post_extra_vars';

    /**
     * 사용되는 테이블의 프라이머리키
     */
    public $parent_key = 'post_id';

    public $meta_key = 'pev_key';

    public $meta_value = 'pev_value';

    function __construct()
    {
        parent::__construct();
    }


    public function get_all_meta($parent_value = '')
    {
        if (empty($parent_value)) {
            return false;
        }

        $result = array();
        $res = $this->get($primary_value = '', $select = '', array($this->parent_key => $parent_value));
        if ($res && is_array($res)) {
            foreach ($res as $val) {
                $result[$val[$this->meta_key]] = $val[$this->meta_value];
            }
        }
        return $result;
    }


    public function save($parentkey = '', $brd_id = 0, $savedata = '')
    {
        if (empty($parentkey)) {
            return false;
        }
        $brd_id = (int) $brd_id;
        if (empty($brd_id) OR $brd_id < 1) {
            return false;
        }

        if ($savedata && is_array($savedata)) {
            foreach ($savedata as $column => $value) {
                $this->meta_update($parentkey, $brd_id, $column, $value);
            }
        }
    }


    public function deletemeta($parentkey = '')
    {
        if (empty($parentkey)) {
            return false;
        }
        $this->delete_where(array($this->parent_key => $parentkey));
    }


    public function meta_update($parentkey = '', $brd_id = 0, $column = '', $value = false)
    {
        if (empty($parentkey)) {
            return false;
        }
        $column = trim($column);
        if (empty($column)) {
            return false;
        }
        $brd_id = (int) $brd_id;
        if (empty($brd_id) OR $brd_id < 1) {
            return false;
        }

        $old_value = $this->item($parentkey, $column);
        if (empty($value)) {
            $value = '';
        }
        if ($value === $old_value) {
            return false;
        }

        if (is_array($value)) {
            $value = json_encode($value);
        }
        if (false === $old_value) {
            return $this->add_meta($parentkey, $brd_id, $column, $value);
        }

        return $this->update_meta($parentkey, $column, $value);
    }


    public function item($parentkey = '', $column = '')
    {
        if (empty($parentkey)) {
            return false;
        }
        if (empty($column)) {
            return false;
        }

        $result = $this->get_all_meta($parentkey);

        return isset($result[ $column ]) ? $result[ $column ] : false;
    }


    public function add_meta($parentkey = '', $brd_id = 0, $column = '', $value = '')
    {
        if (empty($parentkey)) {
            return false;
        }
        $column = trim($column);
        if (empty($column)) {
            return false;
        }
        $brd_id = (int) $brd_id;
        if (empty($brd_id) OR $brd_id < 1) {
            return false;
        }

        $updatedata = array(
            'post_id' => $parentkey,
            'brd_id' => $brd_id,
            'pev_key' => $column,
            'pev_value' => $value,
        );
        $this->db->replace($this->_table, $updatedata);

        return true;
    }


    public function deletemeta_item($column = '')
    {
        if (empty($column)) {
            return false;
        }
        $this->delete_where(array($this->meta_key => $column));
    }


    public function update_meta($parentkey = '', $column = '', $value = '')
    {
        if (empty($parentkey)) {
            return false;
        }
        $column = trim($column);
        if (empty($column)) {
            return false;
        }

        $this->db->where($this->parent_key, $parentkey);
        $this->db->where($this->meta_key, $column);
        $data = array($this->meta_value => $value);
        $this->db->update($this->_table, $data);

        return true;
    }
}
