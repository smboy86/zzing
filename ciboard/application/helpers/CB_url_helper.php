<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Url libraries helper
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

if ( ! function_exists('current_full_url'))
{
	/**
	  * query string 을 포함한 현재페이지 주소 전체를 return 합니다
	  */
	function current_full_url()
	{
		$CI =& get_instance();

		$url = $CI->config->site_url($CI->uri->uri_string());
		return ( isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING']) ? $url . '?' . $_SERVER['QUERY_STRING'] : $url;

	}
}

if ( ! function_exists('goto_url'))
{
	/**
	  * 페이지 이동시 이 함수를 이용하면, gotourl 페이지를 거쳐가므로 referer 를 숨길 수 있습니다
	  */
	function goto_url($url='')
	{

		if ( ! $url) return FALSE;
		$result = site_url('gotourl/?url=' . urlencode($url));
		return $result;

	}
}

if ( ! function_exists('admin_url'))
{
	/**
	  * Admin 페이지 주소를 return 합니다
	  */
	function admin_url($url = '')
	{
		$url = trim($url , '/');
		return site_url(config_item('uri_segment_admin') . '/' . $url);
	}
}

if ( ! function_exists('board_url'))
{
	/**
	  * 게시판 목록 주소를 return 합니다
	  */
	function board_url($key = '')
	{
		$key = trim($key , '/');
		return site_url(config_item('uri_segment_board') . '/' . $key);
	}
}

if ( ! function_exists('post_url'))
{
	/**
	  * 게시물 열람 페이지 주소를 return 합니다
	  */
	function post_url($key = '', $post_id = '')
	{
		
		$key = trim($key , '/');
		$post_id = trim($post_id , '/');
		
		$post_url = '';
		if (strtoupper(config_item('uri_segment_post_type')) == 'B') {
			
			$post_url = site_url($key . '/' . config_item('uri_segment_post') . '/' . $post_id);
		
		} else if (strtoupper(config_item('uri_segment_post_type')) == 'C') {
			
			$post_url = site_url(config_item('uri_segment_post') . '/' . $key . '/' . $post_id);
		
		} else {
			
			$post_url = site_url(config_item('uri_segment_post') . '/' . $post_id);
		
		}

		return $post_url;
	}
}

if ( ! function_exists('write_url'))
{
	/**
	  * 게시물 작성 페이지 주소를 return 합니다
	  */
	function write_url($key = '')
	{
		$key = trim($key , '/');
		return site_url(config_item('uri_segment_write') . '/' . $key);
	}
}

if ( ! function_exists('reply_url'))
{
	/**
	  * 게시물 답변 페이지 주소를 return 합니다
	  */
	function reply_url($key = '')
	{
		$key = trim($key , '/');
		return site_url(config_item('uri_segment_reply') . '/' . $key);
	}
}

if ( ! function_exists('modify_url'))
{
	/**
	  * 게시물 수정 페이지 주소를 return 합니다
	  */
	function modify_url($key = '')
	{
		$key = trim($key , '/');
		return site_url(config_item('uri_segment_modify') . '/' . $key);
	}
}

if ( ! function_exists('group_url'))
{
	/**
	  * 게시물 그룹 페이지 주소를 return 합니다
	  */
	function group_url($key = '')
	{
		$key = trim($key , '/');
		return site_url(config_item('uri_segment_group') . '/' . $key);
	}
}

if ( ! function_exists('rss_url'))
{
	/**
	  * RSS 페이지 주소를 return 합니다
	  */
	function rss_url($key = '')
	{
		$key = trim($key , '/');
		return site_url(config_item('uri_segment_rss') . '/' . $key);
	}
}

if ( ! function_exists('faq_url'))
{
	/**
	  * FAQ 페이지 주소를 return 합니다
	  */
	function faq_url($key = '')
	{
		$key = trim($key , '/');
		return site_url(config_item('uri_segment_faq') . '/' . $key);
	}
}

if ( ! function_exists('document_url'))
{
	/**
	  * 일반문서 페이지 주소를 return 합니다
	  */
	function document_url($key = '')
	{
		$key = trim($key , '/');
		return site_url(config_item('uri_segment_document') . '/' . $key);
	}
}
