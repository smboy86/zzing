<div class="box">
<?php
if (element('brd_id', element('data', $view)))
{
?>
				<div class="box-header">
				<h4 class="pb10"><?php echo html_escape($this->board->item_id('brd_name', element('brd_id', element('data', $view)))); ?> <a href="<?php echo goto_url(board_url(html_escape($this->board->item_id('brd_key', element('brd_id', element('data', $view)))))); ?>" class="btn-xs" target="_blank"><span class="glyphicon glyphicon-new-window"></span></a></h4>
					  <ul class="nav nav-tabs">
						<li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/write/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">기본정보</a></li>
						<li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/write_list/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">목록페이지</a></li>
						<li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/write_post/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">게시물열람</a></li>
						<li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/write_write/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">게시물작성</a></li>
						<li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/write_category/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">카테고리</a></li>
						<li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/write_comment/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">댓글기능</a></li>
						<li role="presentation" class="active"><a href="<?php echo admin_url($this->pagedir . '/write_general/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">일반기능</a></li>
						<li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/write_point/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">포인트기능</a></li>
						<li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/write_alarm/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">메일/쪽지/문자</a></li>
						<li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/write_rss/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">RSS 설정</a></li>
						<li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/write_access/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">권한관리</a></li>
						<li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/write_extravars/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">사용자정의</a></li>
						<li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/write_admin/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">게시판관리자</a></li>
					  </ul>
				</div>
<?php
}
?>
			 <div class="box-table">

<?php echo validation_errors('<div class="alert alert-warning" role="alert">', '</div>'); ?>
<?php echo show_alert_message(element('alert_message', $view), '<div class="alert alert-auto-close alert-dismissible alert-info"><button type="button" class="close alertclose" >&times;</button>', '</div>'); ?>
<?php
	$attributes = array('class' => 'form-horizontal', 'name' => 'fadminwrite', 'id' => 'fadminwrite');
	echo form_open(current_full_url(), $attributes);
?>
<input type="hidden" name="is_submit" value="1" />
<input type="hidden" name="<?php echo element('primary_key', $view); ?>"	 value="<?php echo element(element('primary_key', $view), element('data', $view)); ?>" />

			<div class="form-horizontal">
			  <div class="form-group">
				<label class="col-sm-2 control-label">원글 수정 및 삭제 금지 기간</label>
				<div class="col-sm-8">
						<input type="number" class="form-control" name="protect_post_day" value="<?php echo set_value('protect_post_day', element('protect_post_day', element('data', $view))+0); ?>" />일이 지난 원글은 수정 및 삭제가 불가합니다, 0 이면 항상 수정 및 삭제 가능
				</div>
				<div class="col-sm-2">
					  <label for="grp_protect_post_day"  class="checkbox-inline">
						  <input type="checkbox" name="grp[protect_post_day]" id="grp_protect_post_day" value="1"  /> 그룹적용
					  </label>
					  <label for="all_protect_post_day"  class="checkbox-inline">
						  <input type="checkbox" name="all[protect_post_day]" id="all_protect_post_day" value="1"  /> 전체적용
					  </label>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label">댓글 수정 및 삭제 금지 기간</label>
				<div class="col-sm-8">
						<input type="number" class="form-control" name="protect_comment_day" value="<?php echo set_value('protect_comment_day', element('protect_comment_day', element('data', $view))+0); ?>" />일이 지난 댓글은 수정 및 삭제가 불가합니다, 0 이면 항상 수정 및 삭제 가능
				</div>
				<div class="col-sm-2">
					  <label for="grp_protect_comment_day"  class="checkbox-inline">
						  <input type="checkbox" name="grp[protect_comment_day]" id="grp_protect_comment_day" value="1"  /> 그룹적용
					  </label>
					  <label for="all_protect_comment_day"  class="checkbox-inline">
						  <input type="checkbox" name="all[protect_comment_day]" id="all_protect_comment_day" value="1"  /> 전체적용
					  </label>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label">원글 수정 및 삭제 댓글수</label>
				<div class="col-sm-8">
						<input type="number" class="form-control" name="protect_comment_num" value="<?php echo set_value('protect_comment_num', element('protect_comment_num', element('data', $view))+0); ?>" /> 댓글이 해당 개수 이상 달린 원글은 수정 및 삭제가 불가합니다, 0 이면 댓글수에 상관없이 항상 원글 수정 및 삭제가 가능
				</div>
				<div class="col-sm-2">
					  <label for="grp_protect_comment_num"  class="checkbox-inline">
						  <input type="checkbox" name="grp[protect_comment_num]" id="grp_protect_comment_num" value="1"  /> 그룹적용
					  </label>
					  <label for="all_protect_comment_num"  class="checkbox-inline">
						  <input type="checkbox" name="all[protect_comment_num]" id="all_protect_comment_num" value="1"  /> 전체적용
					  </label>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label">글쓴이 사이드뷰</label>
				<div class="col-sm-8">
					  <label for="use_sideview"  class="checkbox-inline">
						  <input type="checkbox" name="use_sideview" id="use_sideview" value="1"  <?php echo set_checkbox('use_sideview', '1', (element('use_sideview', element('data', $view))?TRUE:FALSE)); ?> /> PC
					  </label>
					  <label for="use_mobile_sideview"  class="checkbox-inline">
						  <input type="checkbox" name="use_mobile_sideview" id="use_mobile_sideview" value="1"  <?php echo set_checkbox('use_mobile_sideview', '1', (element('use_mobile_sideview', element('data', $view))?TRUE:FALSE)); ?> /> 모바일
					  </label>
				</div>
				<div class="col-sm-2">
					  <label for="grp_use_sideview"  class="checkbox-inline">
						  <input type="checkbox" name="grp[use_sideview]" id="grp_use_sideview" value="1"  /> 그룹적용
					  </label>
					  <label for="all_use_sideview"  class="checkbox-inline">
						  <input type="checkbox" name="all[use_sideview]" id="all_use_sideview" value="1"  /> 전체적용
					  </label>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label">카테고리 기능</label>
				<div class="col-sm-8">
					  <label for="use_category"  class="checkbox-inline">
						  <input type="checkbox" name="use_category" id="use_category" value="1"  <?php echo set_checkbox('use_category', '1', (element('use_category', element('data', $view))?TRUE:FALSE)); ?> /> 사용합니다
					  </label>
				</div>
				<div class="col-sm-2">
					  <label for="grp_use_category"  class="checkbox-inline">
						  <input type="checkbox" name="grp[use_category]" id="grp_use_category" value="1"  /> 그룹적용
					  </label>
					  <label for="all_use_category"  class="checkbox-inline">
						  <input type="checkbox" name="all[use_category]" id="all_use_category" value="1"  /> 전체적용
					  </label>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label">카테고리 목록모양</label>
				<div class="col-sm-8 form-inline">
						<select name="category_display_style" class="form-control">
							<option value="" <?php echo set_select('category_display_style', '', (element('category_display_style', element('data', $view)) == ''?TRUE:FALSE)); ?>>셀렉트박스</option>
							<option value="tab" <?php echo set_select('category_display_style', 'tab', (element('category_display_style', element('data', $view)) == 'tab'?TRUE:FALSE)); ?>>탭메뉴형태</option>
						</select>
						<div class="help-inline">
							카테고리 기능을 사용시 목록에 보이는 카테고리 선택 메뉴가 보이는 형식을 결정합니다. 탭메뉴형태를 선택시 1차카테고리만 보이게 됩니다.
						</div>
				</div>
				<div class="col-sm-2">
					  <label for="grp_category_display_style"  class="checkbox-inline">
						  <input type="checkbox" name="grp[category_display_style]" id="grp_category_display_style" value="1"  /> 그룹적용
					  </label>
					  <label for="all_category_display_style"  class="checkbox-inline">
						  <input type="checkbox" name="all[category_display_style]" id="all_category_display_style" value="1"  /> 전체적용
					  </label>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label">카테고리 목록모양 (모바일)</label>
				<div class="col-sm-8 form-inline">
						<select name="mobile_category_display_style" class="form-control">
							<option value="" <?php echo set_select('mobile_category_display_style', '', (element('mobile_category_display_style', element('data', $view)) == ''?TRUE:FALSE)); ?>>셀렉트박스</option>
							<option value="tab" <?php echo set_select('mobile_category_display_style', 'tab', (element('mobile_category_display_style', element('data', $view)) == 'tab'?TRUE:FALSE)); ?>>탭메뉴형태</option>
						</select>
						<div class="help-inline">
							카테고리 기능을 사용시 목록에 보이는 카테고리 선택 메뉴가 보이는 형식을 결정합니다. 탭메뉴형태를 선택시 1차카테고리만 보이게 됩니다.
						</div>
				</div>
				<div class="col-sm-2">
					  <label for="grp_mobile_category_display_style"  class="checkbox-inline">
						  <input type="checkbox" name="grp[mobile_category_display_style]" id="grp_mobile_category_display_style" value="1"  /> 그룹적용
					  </label>
					  <label for="all_mobile_category_display_style"  class="checkbox-inline">
						  <input type="checkbox" name="all[mobile_category_display_style]" id="all_mobile_category_display_style" value="1"  /> 전체적용
					  </label>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label">네이버 신디케이션</label>
				<div class="col-sm-8">
					  <label for="use_naver_syndi"  class="checkbox-inline">
						  <input type="checkbox" name="use_naver_syndi" id="use_naver_syndi" value="1"  <?php echo set_checkbox('use_naver_syndi', '1', (element('use_naver_syndi', element('data', $view))?TRUE:FALSE)); ?> /> 사용합니다
						  <span class="help-inline">신디케이션 기능을 사용하기 위해서는 환경설정 &gt; 기본환경설정 메뉴에서 네이버 신디케이션 연동키를 입력하셔야 합니다</span>
					  </label>
				</div>
				<div class="col-sm-2">
					  <label for="grp_use_naver_syndi"  class="checkbox-inline">
						  <input type="checkbox" name="grp[use_naver_syndi]" id="grp_use_naver_syndi" value="1"  /> 그룹적용
					  </label>
					  <label for="all_use_naver_syndi"  class="checkbox-inline">
						  <input type="checkbox" name="all[use_naver_syndi]" id="all_use_naver_syndi" value="1"  /> 전체적용
					  </label>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label">1:1 게시판</label>
				<div class="col-sm-8">
					  <label for="use_personal"  class="checkbox-inline">
						  <input type="checkbox" name="use_personal" id="use_personal" value="1"  <?php echo set_checkbox('use_personal', '1', (element('use_personal', element('data', $view))?TRUE:FALSE)); ?> /> 사용합니다
					  </label>
				</div>
				<div class="col-sm-2">
					  <label for="grp_use_personal"  class="checkbox-inline">
						  <input type="checkbox" name="grp[use_personal]" id="grp_use_personal" value="1"  /> 그룹적용
					  </label>
					  <label for="all_use_personal"  class="checkbox-inline">
						  <input type="checkbox" name="all[use_personal]" id="all_use_personal" value="1"  /> 전체적용
					  </label>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label">임시 저장기능</label>
				<div class="col-sm-8">
					  <label for="use_tempsave"  class="checkbox-inline">
						  <input type="checkbox" name="use_tempsave" id="use_tempsave" value="1"  <?php echo set_checkbox('use_tempsave', '1', (element('use_tempsave', element('data', $view))?TRUE:FALSE)); ?> /> 사용합니다 	<span class="help-inline">게시글을 작성하다가 다른 페이지로 이동시 내용을 임시서장하여 다음번에 다시 글쓰기를 하려고 할 때 불러옵니다</span>
					  </label>
				</div>
				<div class="col-sm-2">
					  <label for="grp_use_tempsave"  class="checkbox-inline">
						  <input type="checkbox" name="grp[use_tempsave]" id="grp_use_tempsave" value="1"  /> 그룹적용
					  </label>
					  <label for="all_use_tempsave"  class="checkbox-inline">
						  <input type="checkbox" name="all[use_tempsave]" id="all_use_tempsave" value="1"  /> 전체적용
					  </label>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label">삭제글 남김(원글)</label>
				<div class="col-sm-8">
					  <label for="use_post_delete_log"  class="checkbox-inline">
						  <input type="checkbox" name="use_post_delete_log" id="use_post_delete_log" value="1"  <?php echo set_checkbox('use_post_delete_log', '1', (element('use_post_delete_log', element('data', $view))?TRUE:FALSE)); ?> /> 사용합니다
						  <span class="help-inline">글을 삭제하면 "이 글은 000님에 의해 0000년 00월 00일 00시 00분에 삭제되었습니다" 라고 표시됨</span>
					  </label>
				</div>
				<div class="col-sm-2">
					  <label for="grp_use_post_delete_log"  class="checkbox-inline">
						  <input type="checkbox" name="grp[use_post_delete_log]" id="grp_use_post_delete_log" value="1"  /> 그룹적용
					  </label>
					  <label for="all_use_post_delete_log"  class="checkbox-inline">
						  <input type="checkbox" name="all[use_post_delete_log]" id="all_use_post_delete_log" value="1"  /> 전체적용
					  </label>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label">삭제글 남김(댓글)</label>
				<div class="col-sm-8">
					  <label for="use_comment_delete_log"  class="checkbox-inline">
						  <input type="checkbox" name="use_comment_delete_log" id="use_comment_delete_log" value="1"  <?php echo set_checkbox('use_comment_delete_log', '1', (element('use_comment_delete_log', element('data', $view))?TRUE:FALSE)); ?> /> 사용합니다
						  <span class="help-inline">글을 삭제하면 "이 글은 000님에 의해 0000년 00월 00일 00시 00분에 삭제되었습니다" 라고 표시됨</span>
					  </label>
				</div>
				<div class="col-sm-2">
					  <label for="grp_use_comment_delete_log"  class="checkbox-inline">
						  <input type="checkbox" name="grp[use_comment_delete_log]" id="grp_use_comment_delete_log" value="1"  /> 그룹적용
					  </label>
					  <label for="all_use_comment_delete_log"  class="checkbox-inline">
						  <input type="checkbox" name="all[use_comment_delete_log]" id="all_use_comment_delete_log" value="1"  /> 전체적용
					  </label>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label">목록 날짜 표시 방법 (PC)</label>
				<div class="col-sm-8 form-inline">
						<select name="list_date_style" class="form-control select-date-style" data-display-target="list_date_style_manual_wrapper">
							<option value="" <?php echo set_select('list_date_style', '', (element('list_date_style', element('data', $view)) == ''?TRUE:FALSE)); ?>>기본</option>
							<option value="sns" <?php echo set_select('list_date_style', 'sns', (element('list_date_style', element('data', $view)) == 'sns'?TRUE:FALSE)); ?>>SNS 스타일</option>
							<option value="user" <?php echo set_select('list_date_style', 'user', (element('list_date_style', element('data', $view)) == 'user'?TRUE:FALSE)); ?>>사용자정의</option>
						</select>
						<span id="list_date_style_manual_wrapper" style="display:<?php echo (element('list_date_style', element('data', $view)) == 'user') ? 'inline' : 'none'; ?>">
						&lt;&#x0003F;php echo date("<input type="text" class="form-control" name="list_date_style_manual" value="<?php echo set_value('list_date_style_manual', element('list_date_style_manual', element('data', $view))); ?>" />", $posttime); &#x0003F;&gt;
						</span>
				</div>
				<div class="col-sm-2">
					  <label for="grp_list_date_style"  class="checkbox-inline">
						  <input type="checkbox" name="grp[list_date_style]" id="grp_list_date_style" value="1"  /> 그룹적용
					  </label>
					  <label for="all_list_date_style"  class="checkbox-inline">
						  <input type="checkbox" name="all[list_date_style]" id="all_list_date_style" value="1"  /> 전체적용
					  </label>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label">본문 날짜 표시 방법 (PC)</label>
				<div class="col-sm-8 form-inline">
						<select name="view_date_style" class="form-control select-date-style"  data-display-target="view_date_style_manual_wrapper">
							<option value="" <?php echo set_select('view_date_style', '', (element('view_date_style', element('data', $view)) == ''?TRUE:FALSE)); ?>>기본</option>
							<option value="sns" <?php echo set_select('view_date_style', 'sns', (element('view_date_style', element('data', $view)) == 'sns'?TRUE:FALSE)); ?>>SNS 스타일</option>
							<option value="user" <?php echo set_select('view_date_style', 'user', (element('view_date_style', element('data', $view)) == 'user'?TRUE:FALSE)); ?>>사용자정의</option>
						</select>
						<span id="view_date_style_manual_wrapper" style="display:<?php echo (element('view_date_style', element('data', $view)) == 'user') ? 'inline' : 'none'; ?>">
						&lt;&#x0003F;php echo date("<input type="text" class="form-control" name="view_date_style_manual" value="<?php echo set_value('view_date_style_manual', element('view_date_style_manual', element('data', $view))); ?>" />", $posttime); &#x0003F;&gt;
						</span>
				</div>
				<div class="col-sm-2">
					  <label for="grp_view_date_style"  class="checkbox-inline">
						  <input type="checkbox" name="grp[view_date_style]" id="grp_view_date_style" value="1"  /> 그룹적용
					  </label>
					  <label for="all_view_date_style"  class="checkbox-inline">
						  <input type="checkbox" name="all[view_date_style]" id="all_view_date_style" value="1"  /> 전체적용
					  </label>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label">댓글 날짜 표시 방법 (PC)</label>
				<div class="col-sm-8 form-inline">
						<select name="comment_date_style" class="form-control select-date-style"  data-display-target="comment_date_style_manual_wrapper" >
							<option value="" <?php echo set_select('comment_date_style', '', (element('comment_date_style', element('data', $view)) == ''?TRUE:FALSE)); ?>>기본</option>
							<option value="sns" <?php echo set_select('comment_date_style', 'sns', (element('comment_date_style', element('data', $view)) == 'sns'?TRUE:FALSE)); ?>>SNS 스타일</option>
							<option value="user" <?php echo set_select('comment_date_style', 'user', (element('comment_date_style', element('data', $view)) == 'user'?TRUE:FALSE)); ?>>사용자정의</option>
						</select>
						<span id="comment_date_style_manual_wrapper" style="display:<?php echo (element('comment_date_style', element('data', $view)) == 'user') ? 'inline' : 'none'; ?>">
						&lt;&#x0003F;php echo date("<input type="text" class="form-control" name="comment_date_style_manual" value="<?php echo set_value('comment_date_style_manual', element('comment_date_style_manual', element('data', $view))); ?>" />", $posttime); &#x0003F;&gt;
						</span>
				</div>
				<div class="col-sm-2">
					  <label for="grp_comment_date_style"  class="checkbox-inline">
						  <input type="checkbox" name="grp[comment_date_style]" id="grp_comment_date_style" value="1"  /> 그룹적용
					  </label>
					  <label for="all_comment_date_style"  class="checkbox-inline">
						  <input type="checkbox" name="all[comment_date_style]" id="all_comment_date_style" value="1"  /> 전체적용
					  </label>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label">목록 날짜 표시 방법 (모바일)</label>
				<div class="col-sm-8 form-inline">
						<select name="mobile_list_date_style" class="form-control select-date-style" data-display-target="mobile_list_date_style_manual_wrapper">
							<option value="" <?php echo set_select('mobile_list_date_style', '', (element('mobile_list_date_style', element('data', $view)) == ''?TRUE:FALSE)); ?>>기본</option>
							<option value="sns" <?php echo set_select('mobile_list_date_style', 'sns', (element('mobile_list_date_style', element('data', $view)) == 'sns'?TRUE:FALSE)); ?>>SNS 스타일</option>
							<option value="user" <?php echo set_select('mobile_list_date_style', 'user', (element('mobile_list_date_style', element('data', $view)) == 'user'?TRUE:FALSE)); ?>>사용자정의</option>
						</select>
						<span id="mobile_list_date_style_manual_wrapper" style="display:<?php echo (element('mobile_list_date_style', element('data', $view)) == 'user') ? 'inline' : 'none'; ?>">
						&lt;&#x0003F;php echo date("<input type="text" class="form-control" name="mobile_list_date_style_manual" value="<?php echo set_value('mobile_list_date_style_manual', element('mobile_list_date_style_manual', element('data', $view))); ?>" />", $posttime); &#x0003F;&gt;
						</span>
				</div>
				<div class="col-sm-2">
					  <label for="grp_mobile_list_date_style"  class="checkbox-inline">
						  <input type="checkbox" name="grp[mobile_list_date_style]" id="grp_mobile_list_date_style" value="1"  /> 그룹적용
					  </label>
					  <label for="all_mobile_list_date_style"  class="checkbox-inline">
						  <input type="checkbox" name="all[mobile_list_date_style]" id="all_mobile_list_date_style" value="1"  /> 전체적용
					  </label>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label">본문 날짜 표시 방법 (모바일)</label>
				<div class="col-sm-8 form-inline">
						<select name="mobile_view_date_style" class="form-control select-date-style"  data-display-target="mobile_view_date_style_manual_wrapper">
							<option value="" <?php echo set_select('mobile_view_date_style', '', (element('mobile_view_date_style', element('data', $view)) == ''?TRUE:FALSE)); ?>>기본</option>
							<option value="sns" <?php echo set_select('mobile_view_date_style', 'sns', (element('mobile_view_date_style', element('data', $view)) == 'sns'?TRUE:FALSE)); ?>>SNS 스타일</option>
							<option value="user" <?php echo set_select('mobile_view_date_style', 'user', (element('mobile_view_date_style', element('data', $view)) == 'user'?TRUE:FALSE)); ?>>사용자정의</option>
						</select>
						<span id="mobile_view_date_style_manual_wrapper" style="display:<?php echo (element('mobile_view_date_style', element('data', $view)) == 'user') ? 'inline' : 'none'; ?>">
						&lt;&#x0003F;php echo date("<input type="text" class="form-control" name="mobile_view_date_style_manual" value="<?php echo set_value('mobile_view_date_style_manual', element('mobile_view_date_style_manual', element('data', $view))); ?>" />", $posttime); &#x0003F;&gt;
						</span>
				</div>
				<div class="col-sm-2">
					  <label for="grp_mobile_view_date_style"  class="checkbox-inline">
						  <input type="checkbox" name="grp[mobile_view_date_style]" id="grp_mobile_view_date_style" value="1"  /> 그룹적용
					  </label>
					  <label for="all_mobile_view_date_style"  class="checkbox-inline">
						  <input type="checkbox" name="all[mobile_view_date_style]" id="all_mobile_view_date_style" value="1"  /> 전체적용
					  </label>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label">댓글 날짜 표시 방법 (모바일)</label>
				<div class="col-sm-8 form-inline">
						<select name="mobile_comment_date_style" class="form-control select-date-style"  data-display-target="mobile_comment_date_style_manual_wrapper" >
							<option value="" <?php echo set_select('mobile_comment_date_style', '', (element('mobile_comment_date_style', element('data', $view)) == ''?TRUE:FALSE)); ?>>기본</option>
							<option value="sns" <?php echo set_select('mobile_comment_date_style', 'sns', (element('mobile_comment_date_style', element('data', $view)) == 'sns'?TRUE:FALSE)); ?>>SNS 스타일</option>
							<option value="user" <?php echo set_select('mobile_comment_date_style', 'user', (element('mobile_comment_date_style', element('data', $view)) == 'user'?TRUE:FALSE)); ?>>사용자정의</option>
						</select>
						<span id="mobile_comment_date_style_manual_wrapper" style="display:<?php echo (element('mobile_comment_date_style', element('data', $view)) == 'user') ? 'inline' : 'none'; ?>">
						&lt;&#x0003F;php echo date("<input type="text" class="form-control" name="mobile_comment_date_style_manual" value="<?php echo set_value('mobile_comment_date_style_manual', element('mobile_comment_date_style_manual', element('data', $view))); ?>" />", $posttime); &#x0003F;&gt;
						</span>
				</div>
				<div class="col-sm-2">
					  <label for="grp_mobile_comment_date_style"  class="checkbox-inline">
						  <input type="checkbox" name="grp[mobile_comment_date_style]" id="grp_mobile_comment_date_style" value="1"  /> 그룹적용
					  </label>
					  <label for="all_mobile_comment_date_style"  class="checkbox-inline">
						  <input type="checkbox" name="all[mobile_comment_date_style]" id="all_mobile_comment_date_style" value="1"  /> 전체적용
					  </label>
				</div>
			  </div>

				<div class="btn-group pull-right" role="group" aria-label="...">
						<a href="<?php echo admin_url($this->pagedir); ?>" class="btn btn-default btn-sm">목록으로</a>
						<button type="submit" class="btn btn-success btn-sm">저장하기</button>
				</div>
			</div>

<?php echo form_close(); ?>

			</div>
		</div>

<script type="text/javascript">
//<![CDATA[
	$(document).on("change", "select.select-date-style", function() {
		if ($(this).val() == 'user') {
			$("#" + $(this).attr('data-display-target')).css("display", "inline");
		} else {
			$("#" + $(this).attr('data-display-target')).css("display", "none");
		}
	});
//]]>
</script>

<script type='text/javascript'>
//<![CDATA[
$(function() {
	$("#fadminwrite").validate({
		rules: {
			protect_post_day: {required :true, number:true, min:0 },
			protect_comment_day: {required :true, number:true , min:0},
			protect_comment_num: {required :true, number:true, min:0 }
		}
	});
});

var form_original_data = $("#fadminwrite").serialize(); 
function check_form_changed() { 
        if ($("#fadminwrite").serialize() != form_original_data) {
			if (confirm('저장하지 않은 정보가 있습니다. 저장하지 않은 상태로 이동하시겠습니까?')) {
				return true;
			} else {
				return false;
			}
        }
		return true;
}

//]]>
</script>
