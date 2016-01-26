<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Comment Meta model class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

class Comment_meta_model extends CB_Model
{

    /**
     * 테이블명
     */
    public $_table = 'comment_meta';

    /**
     * 사용되는 테이블의 프라이머리키
     */
    public $parent_key = 'cmt_id';

    public $meta_key = 'cme_key';

    public $meta_value = 'cme_value';

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


    public function save($parentkey = '', $savedata = '')
    {
        if (empty($parentkey)) {
            return false;
        }
        if ($savedata && is_array($savedata)) {
            foreach ($savedata as $column => $value) {
                $this->meta_update($parentkey, $column, $value);
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


    public function meta_update($parentkey = '', $column = '', $value = false)
    {
        if (empty($parentkey)) {
            return false;
        }
        $column = trim($column);
        if (empty($column)) {
            return false;
        }

        $old_value = $this->item($parentkey, $column);
        if (empty($value)) {
            $value = '';
        }
        if ($value === $old_value) {
            return false;
        }
        if (false === $old_value) {
            return $this->add_meta($parentkey, $column, $value);
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


    public function add_meta($parentkey = '', $column = '', $value = '')
    {
        if (empty($parentkey)) {
            return false;
        }
        $column = trim($column);
        if (empty($column)) {
            return false;
        }

        $updatedata = array(
            'cmt_id' => $parentkey,
            'cme_key' => $column,
            'cme_value' => $value,
        );
        $this->db->replace($this->_table, $updatedata);

        return true;
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


    public function delete_meta_column($parentkey = '', $column = '')
    {
        if (empty($parentkey)) {
            return false;
        }
        $column = trim($column);
        if (empty($column)) {
            return false;
        }

        $this->delete_where(array($this->parent_key, $parentkey, $this->meta_key => $column));

        return true;
    }
}
