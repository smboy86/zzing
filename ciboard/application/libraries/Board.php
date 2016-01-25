<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Board class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

/**
 * board table 을 주로 관리하는 class 입니다.
 */
class Board extends CI_Controller
{

	private $CI;
	private $board_id;
	private $board_key;
	private $group;
	private $admin;
	private $call_admin;

	function __construct()
	{
		$this->CI = & get_instance();
		$this->CI->load->model(array('Post_model', 'Board_model', 'Board_admin_model', 'Board_meta_model'));
	}

	/**
	* board table 의 정보를 얻습니다
	*/
	public function get_baord($brd_id='', $brd_key ='')
	{

		if ( ! $brd_id && ! $brd_key) return FALSE;

		if ($brd_id) {
			$board = $this->CI->Board_model->get_one($brd_id);
		} else if ($brd_key) {
			$board = $this->CI->Board_model->get_one('', '', array('brd_key' => $brd_key));
		} else {
			return FALSE;
		}
		$board['board_name'] = ($this->CI->cbconfig->get_device_view_type() == 'mobile' && $board['brd_mobile_name']) ? $board['brd_mobile_name'] : $board['brd_name'];
		if (element('brd_id', $board))
			$board = array_merge($board, $this->get_all_meta(element('brd_id', $board)));

		if (element('brd_id', $board))
			$this->board_id[element('brd_id', $board)] = $board;
		if (element('brd_key', $board))
			$this->board_key[element('brd_key', $board)] = $board;

	}

	/**
	* board meta table 의 정보를 얻습니다
	*/
	public function get_all_meta($brd_id='')
	{
		if ( ! $brd_id) return FALSE;
		$result = $this->CI->Board_meta_model->get_all_meta($brd_id);
		return $result;
	}

	/**
	* board group meta table 의 정보를 얻습니다
	*/
	public function get_all_group_meta($bgr_id='')
	{
		if ( ! $bgr_id) return FALSE;
		$result = $this->CI->Board_group_meta_model->get_all_meta($bgr_id);
		return $result;
	}

	/**
	* item 을 brd_id 에 기반하여 얻습니다
	*/
	public function item_id($column='', $brd_id='')
	{

		if ( ! $column) return FALSE;
		if ( ! $brd_id) return FALSE;
		if ( ! isset($this->board_id[$brd_id]) OR $this->board_id[$brd_id] == FALSE) {
			$this->get_baord($brd_id, '');
		}
		if ($this->board_id[$brd_id] == FALSE) return FALSE;
		$board = $this->board_id[$brd_id];
		return isset($board[$column]) ? $board[$column] : FALSE;

	}

	/**
	* item 을 brd_key 에 기반하여 얻습니다
	*/
	public function item_key($column='', $brd_key='')
	{

		if ( ! $column) return FALSE;
		if ( ! $brd_key) return FALSE;
		if ( ! isset($this->board_key[$brd_key]) OR $this->board_key[$brd_key] == FALSE) {
			$this->get_baord('', $brd_key);
		}
		if ($this->board_key[$brd_key] == FALSE) return FALSE;
		$board = $this->board_key[$brd_key];
		return isset($board[$column]) ? $board[$column] : FALSE;

	}

	/**
	* 모든 item 을 brd_id 에 기반하여 얻습니다
	*/
	public function item_all($brd_id = '')
	{

		if ( ! $brd_id) return FALSE;
		if ( ! isset($this->board_id[$brd_id]) OR $this->board_id[$brd_id] == FALSE) {
			$this->get_baord($brd_id, '');
		}
		if ( ! isset($this->board_id[$brd_id]) OR $this->board_id[$brd_id] == FALSE) return FALSE;
		return $this->board_id[$brd_id];

	}

	/**
	* 그룹 정보를 얻습니다
	*/
	public function get_group($bgr_id='')
	{

		if ( ! $bgr_id) return FALSE;

		if ($bgr_id) {
			$group = $this->CI->Board_group_model->get_one($bgr_id);
		} else {
			return FALSE;
		}
		$group = array_merge($group, $this->get_all_group_meta($bgr_id));

		$this->group[$bgr_id] = $group;

	}

	/**
	* 그룹의 item 정보를 얻습니다
	*/
	public function group_item($column='', $bgr_id='')
	{

		if ( ! $column) return FALSE;
		if ( ! $bgr_id) return FALSE;
		if ($this->group[$bgr_id] == FALSE) {
			$this->get_group($bgr_id, '');
		}
		if ($this->group[$bgr_id] == FALSE) return FALSE;
		$group = $this->group[$bgr_id];
		return isset($group[$column]) ? $group[$column] : FALSE;

	}

	/**
	* 게시글 삭제시 삭제되어야하는 모든 테이블데이터입니다
	*/
	public function delete_post($post_id='')
	{

		if ( ! $post_id) return FALSE;
		if ( ! is_numeric($post_id) OR $post_id < 1) return FALSE;

		$this->CI->load->model( array('Post_model', 'Blame_model', 'Like_model', 'Post_extra_vars_model', 'Post_file_model', 'Post_link_model', 'Post_meta_model', 'Scrap_model', 'Comment_model'));

		$post = $this->CI->Post_model->get_one($post_id);

		$this->CI->Post_model->delete($post_id);
		$this->CI->Blame_model->delete('', array('target_id' => $post_id , 'target_type' => '1'));
		$this->CI->Like_model->delete('', array('target_id' => $post_id , 'target_type' => '1'));
		$this->CI->Post_extra_vars_model->delete('', array('post_id' => $post_id));
		$this->CI->Post_file_model->delete('', array('post_id' => $post_id));
		$this->CI->Post_link_model->delete('', array('post_id' => $post_id));
		$this->CI->Post_meta_model->deletemeta($post_id);
		$this->CI->Scrap_model->delete('', array('post_id' => $post_id));

		$comment = $this->CI->Comment_model->get('', 'cmt_id', array('post_id' => $post_id));
		if ($comment && is_array($comment)) {
			foreach ($comment as $cval) {
				if (element('cmt_id', $cval)) {
					$this->delete_comment(element('cmt_id', $cval));
				}
			}
		}
		$this->CI->load->library('point');
		$this->CI->point->delete_point(element('mem_id', $post), 'post' , $post_id, '게시글 작성');
		if(element('point_post_delete', $board) && $this->CI->member->item('mem_id') == element('mem_id', $post)) {
			$point_delete = 0 - abs(element('point_post_delete', $board));
			$this->CI->point->insert_point(element('mem_id', $post), $point_delete, element('brd_name', $board) . ' ' . $post_id . ' 게시글 삭제', 'post_delete' , $post_id, '게시글 삭제');
		} else if(element('point_admin_post_delete', $board) && $this->CI->member->item('mem_id') != element('mem_id', $post)) {
			$point_delete = 0 - abs(element('point_admin_post_delete', $board));
			$this->CI->point->insert_point(element('mem_id', $post), $point_delete, element('brd_name', $board) . ' ' . $post_id . ' 게시글 삭제', 'admin_post_delete' , $post_id, '게시글 삭제');
		}

		return TRUE;

	}

	/**
	* 코멘트 삭제시 삭제되어야하는 모든 테이블데이터입니다
	*/
	public function delete_comment($cmt_id='')
	{

		if ( ! $cmt_id) return FALSE;
		if ( ! is_numeric($cmt_id) OR $cmt_id < 1) return FALSE;

		$this->CI->load->model( array('Post_model', 'Blame_model', 'Like_model', 'Comment_model', 'Comment_meta_model'));

		$comment = $this->CI->Comment_model->get_one($cmt_id);

		if( ! element('cmt_id', $comment)) return FALSE;

		$post = $this->CI->Post_model->get_one(element('post_id', $comment));
		$board = $this->CI->board->item_all(element('brd_id', $post));

		$this->CI->Comment_model->delete($cmt_id);
		$this->CI->Blame_model->delete('', array('target_id' => $cmt_id , 'target_type' => '2'));
		$this->CI->Comment_meta_model->delete('', array('cmt_id' => $cmt_id));
		$this->CI->Like_model->delete('', array('target_id' => $cmt_id , 'target_type' => '2'));
		$this->CI->Post_model->update_plus(element('post_id', $comment), 'post_comment_count', '-1');

		$this->CI->load->library('point');
		$this->CI->point->delete_point(element('mem_id', $comment), 'comment' , $cmt_id, '댓글 작성');

		if(element('point_comment_delete', $board) && $this->CI->member->item('mem_id') == element('mem_id', $comment)) {
			$point_delete = 0 - abs(element('point_comment_delete', $board));
			$this->CI->point->insert_point(element('mem_id', $comment), $point_delete, element('brd_name', $board) . ' ' . $cmt_id . ' 댓글 삭제', 'comment_delete' , $cmt_id, '댓글 삭제');
		} else if(element('point_admin_comment_delete', $board) && $this->CI->member->item('mem_id') != element('mem_id', $comment)) {
			$point_delete = 0 - abs(element('point_admin_comment_delete', $board));
			$this->CI->point->insert_point(element('mem_id', $comment), $point_delete, element('brd_name', $board) . ' ' . $cmt_id . ' 댓글 삭제', 'admin_comment_delete' , $cmt_id, '댓글 삭제');
		}

		return TRUE;

	}

	/**
	* 댓글 삭제시 삭제가능한 권한이 있는지 체크합니다
	*/
	public function delete_comment_check($cmt_id='', $password='', $realdelete=FALSE)
	{
		$this->CI->load->model( array('Post_model', 'Comment_model', 'Comment_meta_model'));

		$comment = $this->CI->Comment_model->get_one($cmt_id);
		$post = $this->CI->Post_model->get_one(element('post_id', $comment));
		$board = $this->CI->board->item_all(element('brd_id', $post));

		if ( ! $this->CI->session->userdata('post_id_' . element('post_id', $post))) {
			$result = array('error' => '해당 게시물에서만 접근 가능합니다');
			return json_encode($result);
		}

		$is_admin = $this->CI->member->is_admin(array('board_id' => element('brd_id', $board), 'group_id' => element('bgr_id', $board)));
		$can_delete_comment = FALSE;

		if (element('protect_comment_day', $board) >0 && $is_admin == FALSE) {
			if (ctimestamp() - strtotime(element('cmt_datetime', $comment)) >= element('protect_comment_day', $board) * 86400) {
				$result = array('error' => '이 게시판은 ' . element('protect_comment_day', $board) . '일 이상된 댓글의 삭제를 금지합니다');
				return json_encode($result);
			}
		}

		if($is_admin == FALSE) {
			$count_comment_reply = $this->CI->Comment_model->count_reply_comment(element('cmt_id', $comment), element('post_id', $comment), element('cmt_num', $comment), element('cmt_reply', $comment));

			if($count_comment_reply > 0) {
				$result = array('error' => '이 댓글에 답변댓글이 있으므로 댓글을 삭제할 수 없습니다');
				return json_encode($result);
			}
		}

		if (element('mem_id', $comment)) {
			if ($is_admin == FALSE && $this->CI->member->item('mem_id') != element('mem_id', $comment)) {
				$result = array('error' => '회원님은 이 글을 삭제할 권한이 없습니다');
				return json_encode($result);
			}
		} else {

			$this->CI->session->keep_flashdata('can_delete_comment_' . element('cmt_id', $comment));
			if ($is_admin) {
				$this->CI->session->set_flashdata('can_delete_comment_' . element('cmt_id', $comment), '1');
			}
			if ( ! $this->CI->session->flashdata('can_delete_comment_' . element('cmt_id', $comment)) && $password) {

				if ( ! function_exists('password_hash')) {
					$this->CI->load->helper('password');
				}
				if (password_verify($password, element('cmt_password', $comment))) {
					$can_delete_comment = TRUE;
					$this->CI->session->set_flashdata('can_delete_comment_' . element('cmt_id', $comment), '1');
				} else {
					$result = array('error' => '패스워드가 잘못 입력되었습니다');
					return json_encode($result);
				}
			}
			if ( ! $this->CI->session->flashdata('can_delete_comment_' . element('cmt_id', $comment)) && ! $can_delete_comment) {

				$result = array('password' => '패스워드가 확인이 필요합니다');
				return json_encode($result);
			}
		}

		if (element('use_comment_delete_log', $board) && ! $realdelete) {
			$this->CI->Comment_model->update(element('cmt_id', $comment), array('cmt_del' => 1));
			$metadata = array(
				'delete_mem_id' => $this->CI->member->item('mem_id'),
				'delete_mem_nickname' => $this->CI->member->item('mem_nickname'),
				'delete_datetime' => cdate('Y-m-d H:i:s'),
				'delete_ip' => $this->CI->input->ip_address(),
			);
			$this->CI->Comment_meta_model->save(element('cmt_id', $comment), $metadata);
		} else {
			$this->CI->board->delete_comment($cmt_id);
		}
		$result = array('success' => '댓글이 삭제되었습니다');
		return json_encode($result);
	}

	/**
	* 최근게시물을 가져옵니다
	*/
	public function latest($config)
	{
		$this->CI->load->model( array('Board_category_model', 'Post_file_model'));

		$skin = element('skin', $config);
		$brd_id = element('brd_id', $config);
		$brd_key = element('brd_key', $config);
		$exclude_brd_id = element('exclude_brd_id', $config);
		$exclude_brd_key = element('exclude_brd_key', $config);
		$findex = element('findex',$config) ? element('findex',$config) : 'post_id';
		$forder = element('forder',$config) ? element('forder',$config) : 'DESC';
		$limit = element('limit', $config);
		$length = element('length', $config);
		$is_gallery = element('is_gallery', $config);
		$image_width = element('image_width', $config);
		$image_height = element('image_height', $config);
		$period_second = element('period_second', $config);
		$cache_minute = element('cache_minute', $config);

		if ($limit <= 0) return FALSE;

		if ($cache_minute>0) {
			$cache_brd_id = is_array($brd_id) ? implode('-', $brd_id) : $brd_id;
			$cache_brd_key = is_array($brd_key) ? implode('-', $brd_key) : $brd_key;
			$cache_exclude_brd_id = is_array($exclude_brd_id) ? implode('-', $exclude_brd_id) : $exclude_brd_id;
			$cache_exclude_brd_key = is_array($exclude_brd_key) ? implode('-', $exclude_brd_key) : $exclude_brd_key;
			$cachename = 'latest-s-' . $skin . '-i-' . $cache_brd_id . '-k-' . $cache_brd_key . '-l-' . $cache_exclude_brd_id . '-k-' . $cache_exclude_brd_key . '-l-' . $limit . '-t-' . $length . '-g-' . $is_gallery . '-w-' . $image_width . '-h-' . $image_height . '-p-' . $period_second;
			$html = $this->CI->cache->get($cachename);
			if ($html) return $html;
		}

		if ( ! $skin) $skin = 'basic';
		$view['view']['config'] = $config;
		$view['view']['length'] = $length;
		if ($brd_key) {
			if(is_array($brd_key)) {
				foreach($brd_key as $v) {
					$brd_id[] = $this->CI->board->item_key('brd_id', $v);
				}
			} else {
				$brd_id = $this->CI->board->item_key('brd_id', $brd_key);
			}
		}
		if ($exclude_brd_key) {
			if(is_array($exclude_brd_key)) {
				foreach($exclude_brd_key as $v) {
					$exclude_brd_id[] = $this->CI->board->item_key('brd_id', $v);
				}
			} else {
				$exclude_brd_id = $this->CI->board->item_key('brd_id', $exclude_brd_key);
			}
		}
		if ($brd_id && ! is_array($brd_id)) {
			$view['view']['board'] = $this->CI->board->item_all($brd_id);
		}
		$where = array();
		$where['post_del'] = 0;
		$where['post_secret'] = 0;
		
		$this->CI->db->from('post');
		$this->CI->db->where($where);

		if ($brd_id) {
			if(is_array($brd_id)) {
				$this->CI->db->group_start();
					foreach($brd_id as $v) {
						$this->CI->db->or_where('brd_id', $v);
					}
				$this->CI->db->group_end();
			} else {
				$this->CI->db->where('brd_id', $brd_id);
			}
		}

		if ($exclude_brd_id) {
			if(is_array($exclude_brd_id)) {
				foreach($exclude_brd_id as $v) {
					$this->CI->db->where('brd_id <>', $v);
				}
			} else {
				$this->CI->db->where('brd_id <>', $exclude_brd_id);
			}
		}

		if($period_second) {
			$post_start_datetime = cdate('Y-m-d H:i:s', ctimestamp() - $period_second);
			$this->CI->db->where('post_datetime >=', $post_start_datetime);
		}

		if ($findex && $forder) {
			$forder = ( strtoupper($forder) == 'ASC') ?  'ASC' : 'DESC';
			$this->CI->db->order_by($findex, $forder);
		}
		if (is_numeric($limit)) $this->CI->db->limit($limit);
		$result = $this->CI->db->get();
		$view['view']['latest'] = $latest = $result->result_array();

		$view['view']['latest_limit'] = $limit;
		if ($latest && is_array($latest)) {
			foreach ($latest as $key => $value) {
				$view['view']['latest'][$key]['name'] = display_username(element('post_userid', $value), element('post_nickname', $value));
				$brd_key = $this->CI->board->item_id('brd_key', element('brd_id', $value));
				$view['view']['latest'][$key]['url'] = post_url($brd_key, element('post_id', $value));
				$view['view']['latest'][$key]['title'] = $length ? cut_str(element('post_title', $value), $length) : element('post_title', $value);
				$view['view']['latest'][$key]['display_datetime'] = display_datetime(element('post_datetime', $value), '');
				$view['view']['latest'][$key]['category'] = '';
				if (element('post_category', $value)) {
						$view['view']['latest'][$key]['category'] = $this->CI->Board_category_model->get_category_info(element('brd_id', $value), element('post_category', $value));
				}
				if ($is_gallery) {
					if(element('post_image', $value)) {
						$file = $this->CI->Post_file_model->get_one('', '', array('post_id' => element('post_id', $value), 'pfi_is_image'=> 1), '', '', 'pfi_id', 'ASC');
						if (element('pfi_filename', $file)) {
							$view['view']['latest'][$key]['thumb_url'] = thumb_url('post' , element('pfi_filename', $file) , $image_width, $image_height);
						}
					} else {
						$thumb_url = get_post_image_url(element('post_content', $value), $image_width, $image_height);
						$view['view']['latest'][$key]['thumb_url'] = $thumb_url ? $thumb_url : thumb_url('', '' , $image_width, $image_height);
					}
				}
			}
		}
		$view['view']['skinurl'] = base_url( VIEW_DIR . 'latest/' . $skin);
		$html = $this->CI->load->view('latest/' . $skin . '/latest', $view, TRUE);

		if ($cache_minute>0) {
			$this->CI->cache->save($cachename, $html, $cache_minute);
		}

		return $html;
	}

	/**
	* 최근 댓글을 가져옵니다
	*/
	public function latest_comment($config)
	{
		$this->CI->load->model( array('Comment_model'));

		$skin = element('skin', $config);
		$brd_id = element('brd_id', $config);
		$brd_key = element('brd_key', $config);
		$exclude_brd_id = element('exclude_brd_id', $config);
		$exclude_brd_key = element('exclude_brd_key', $config);
		$findex = element('findex',$config) ? element('findex',$config) : 'cmt_id';
		$forder = element('forder',$config) ? element('forder',$config) : 'DESC';
		$limit = element('limit', $config);
		$length = element('length', $config);
		$period_second = element('period_second', $config);
		$cache_minute = element('cache_minute', $config);

		if ($limit <= 0) return FALSE;

		if ($cache_minute>0) {
			$cache_brd_id = is_array($brd_id) ? implode('-', $brd_id) : $brd_id;
			$cache_brd_key = is_array($brd_key) ? implode('-', $brd_key) : $brd_key;
			$cache_exclude_brd_id = is_array($exclude_brd_id) ? implode('-', $exclude_brd_id) : $exclude_brd_id;
			$cache_exclude_brd_key = is_array($exclude_brd_key) ? implode('-', $exclude_brd_key) : $exclude_brd_key;
			$cachename = 'latest-comment-s-' . $skin . '-i-' . $cache_brd_id . '-k-' . $cache_brd_key . '-l-' . $cache_exclude_brd_id . '-k-' . $cache_exclude_brd_key . '-l-' . $limit . '-t-' . $length . '-p-' . $period_second;
			$html = $this->CI->cache->get($cachename);
			if ($html) return $html;
		}

		if ( ! $skin) $skin = 'basic';
		$view['view']['config'] = $config;
		$view['view']['length'] = $length;
		if ($brd_key) {
			if(is_array($brd_key)) {
				foreach($brd_key as $v) {
					$brd_id[] = $this->CI->board->item_key('brd_id', $v);
				}
			} else {
				$brd_id = $this->CI->board->item_key('brd_id', $brd_key);
			}
		}
		if ($exclude_brd_key) {
			if(is_array($exclude_brd_key)) {
				foreach($exclude_brd_key as $v) {
					$exclude_brd_id[] = $this->CI->board->item_key('brd_id', $v);
				}
			} else {
				$exclude_brd_id = $this->CI->board->item_key('brd_id', $exclude_brd_key);
			}
		}
		if ($brd_id && ! is_array($brd_id)) {
			$view['view']['board'] = $this->CI->board->item_all($brd_id);
		}
		$where = array();
		$where['cmt_del'] = 0;
		$where['cmt_secret'] = 0;
		$where['post_secret'] = 0;
		$where['post_del'] = 0;

		$this->CI->db->from('comment');
		$this->CI->db->join('post', 'post.post_id=comment.post_id', 'inner');
		$this->CI->db->where($where);

		if ($brd_id) {
			if(is_array($brd_id)) {
				$this->CI->db->group_start();
					foreach($brd_id as $v) {
						$this->CI->db->or_where('brd_id', $v);
					}
				$this->CI->db->group_end();
			} else {
				$this->CI->db->where('brd_id', $brd_id);
			}
		}

		if ($exclude_brd_id) {
			if(is_array($exclude_brd_id)) {
				foreach($exclude_brd_id as $v) {
					$this->CI->db->where('brd_id <>', $v);
				}
			} else {
				$this->CI->db->where('brd_id <>', $exclude_brd_id);
			}
		}

		if($period_second) {
			$comment_start_datetime = cdate('Y-m-d H:i:s', ctimestamp() - $period_second);
			$this->CI->db->where('cmt_datetime >=', $comment_start_datetime);
		}

		if ($findex && $forder) {
			$forder = ( strtoupper($forder) == 'ASC') ?  'ASC' : 'DESC';
			$this->CI->db->order_by($findex, $forder);
		}
		if (is_numeric($limit)) $this->CI->db->limit($limit);
		$result = $this->CI->db->get();
		$view['view']['latest'] = $latest = $result->result_array();

		$view['view']['latest_limit'] = $limit;
		if ($latest && is_array($latest)) {
			foreach ($latest as $key => $value) {
				$view['view']['latest'][$key]['name'] = display_username(element('cmt_userid', $value), element('cmt_nickname', $value));
				$brd_key = $this->CI->board->item_id('brd_key', element('brd_id', $value));
				$view['view']['latest'][$key]['url'] = post_url($brd_key, element('post_id', $value)) . '#comment_' . element('cmt_id', $value);
				$view['view']['latest'][$key]['title'] = $length ? cut_str(element('cmt_content', $value), $length) : element('cmt_content', $value);
				$view['view']['latest'][$key]['display_datetime'] = display_datetime(element('cmt_datetime', $value), '');
			}
		}
		$view['view']['skinurl'] = base_url( VIEW_DIR . 'latest/' . $skin);
		$html = $this->CI->load->view('latest/' . $skin . '/latest', $view, TRUE);

		if ($cache_minute>0) {
			$this->CI->cache->save($cachename, $html, $cache_minute);
		}

		return $html;
	}

	/**
	* 어드민인지 체크합니다
	*/
	public function is_admin($brd_id = '')
	{
		if ( ! $brd_id) return FALSE;
		if ( ! $this->CI->member->item('mem_id')) return FALSE;
		if ($this->call_admin) return $this->admin;
		$this->call_admin = TRUE;
		$count = $this->CI->Board_admin_model->count_by( array('brd_id' => $brd_id, 'mem_id' => $this->CI->member->item('mem_id')));
		if ($count) {
			$this->admin = TRUE;
		} else {
			$this->admin = FALSE;
		}
		return $this->admin;

	}

}
