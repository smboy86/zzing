<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_20160123000000 extends CI_Migration
{

    public function up()
    {
        $fields = array(
            'bgm_value' => array(
                'name' => 'bgm_value',
                'type' => 'TEXT',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column('board_group_meta', $fields);

        $fields = array(
            'bmt_value' => array(
                'name' => 'bmt_value',
                'type' => 'TEXT',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column('board_meta', $fields);

        $fields = array(
            'cmt_content' => array(
                'name' => 'cmt_content',
                'type' => 'TEXT',
                'null' => true,
            ),
            'cmt_homepage' => array(
                'name' => 'cmt_homepage',
                'type' => 'TEXT',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column('comment', $fields);

        $fields = array(
            'cme_value' => array(
                'name' => 'cme_value',
                'type' => 'TEXT',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column('comment_meta', $fields);

        $fields = array(
            'cfg_value' => array(
                'name' => 'cfg_value',
                'type' => 'TEXT',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column('config', $fields);

        $fields = array(
            'cur_page' => array(
                'name' => 'cur_page',
                'type' => 'TEXT',
                'null' => true,
            ),
            'cur_url' => array(
                'name' => 'cur_url',
                'type' => 'TEXT',
                'null' => true,
            ),
            'cur_referer' => array(
                'name' => 'cur_referer',
                'type' => 'TEXT',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column('currentvisitor', $fields);

        $fields = array(
            'doc_content' => array(
                'name' => 'doc_content',
                'type' => 'TEXT',
                'null' => true,
            ),
            'doc_mobile_content' => array(
                'name' => 'doc_mobile_content',
                'type' => 'TEXT',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column('document', $fields);

        $fields = array(
            'faq_title' => array(
                'name' => 'faq_title',
                'type' => 'TEXT',
                'null' => true,
            ),
            'faq_content' => array(
                'name' => 'faq_content',
                'type' => 'TEXT',
                'null' => true,
            ),
            'faq_mobile_content' => array(
                'name' => 'faq_mobile_content',
                'type' => 'TEXT',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column('faq', $fields);

        $fields = array(
            'mem_homepage' => array(
                'name' => 'mem_homepage',
                'type' => 'TEXT',
                'null' => true,
            ),
            'mem_profile_content' => array(
                'name' => 'mem_profile_content',
                'type' => 'TEXT',
                'null' => true,
            ),
            'mem_adminmemo' => array(
                'name' => 'mem_adminmemo',
                'type' => 'TEXT',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column('member', $fields);

        $fields = array(
            'mce_content' => array(
                'name' => 'mce_content',
                'type' => 'TEXT',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column('member_certify', $fields);

        $fields = array(
            'mev_value' => array(
                'name' => 'mev_value',
                'type' => 'TEXT',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column('member_extra_vars', $fields);

        $fields = array(
            'mll_url' => array(
                'name' => 'mll_url',
                'type' => 'TEXT',
                'null' => true,
            ),
            'mll_referer' => array(
                'name' => 'mll_referer',
                'type' => 'TEXT',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column('member_login_log', $fields);

        $fields = array(
            'mmt_value' => array(
                'name' => 'mmt_value',
                'type' => 'TEXT',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column('member_meta', $fields);

        $fields = array(
            'mrg_referer' => array(
                'name' => 'mrg_referer',
                'type' => 'TEXT',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column('member_register', $fields);

        $fields = array(
            'men_link' => array(
                'name' => 'men_link',
                'type' => 'TEXT',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column('menu', $fields);

        $fields = array(
            'nte_content' => array(
                'name' => 'nte_content',
                'type' => 'TEXT',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column('note', $fields);

        $fields = array(
            'pop_content' => array(
                'name' => 'pop_content',
                'type' => 'TEXT',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column('popup', $fields);

        $fields = array(
            'post_content' => array(
                'name' => 'post_content',
                'type' => 'TEXT',
                'null' => true,
            ),
            'post_homepage' => array(
                'name' => 'post_homepage',
                'type' => 'TEXT',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column('post', $fields);

        $fields = array(
            'pev_value' => array(
                'name' => 'pev_value',
                'type' => 'TEXT',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column('post_extra_vars', $fields);

        $fields = array(
            'pln_url' => array(
                'name' => 'pln_url',
                'type' => 'TEXT',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column('post_link', $fields);

        $fields = array(
            'pmt_value' => array(
                'name' => 'pmt_value',
                'type' => 'TEXT',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column('post_meta', $fields);

        $fields = array(
            'sco_referer' => array(
                'name' => 'sco_referer',
                'type' => 'TEXT',
                'null' => true,
            ),
            'sco_current' => array(
                'name' => 'sco_current',
                'type' => 'TEXT',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column('stat_count', $fields);

        $fields = array(
            'tmp_content' => array(
                'name' => 'tmp_content',
                'type' => 'TEXT',
                'null' => true,
            ),
        );
        $this->dbforge->modify_column('tempsave', $fields);

    }

    public function down()
    {

    }
}
