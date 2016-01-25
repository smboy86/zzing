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
						<li role="presentation" class="active"><a href="<?php echo admin_url($this->pagedir . '/write_post/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">게시물열람</a></li>
						<li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/write_write/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">게시물작성</a></li>
						<li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/write_category/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">카테고리</a></li>
						<li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/write_comment/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">댓글기능</a></li>
						<li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/write_general/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">일반기능</a></li>
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
				<label class="col-sm-2 control-label">스크랩 기능</label>
				<div class="col-sm-8">
					  <label for="use_scrap"  class="checkbox-inline">
						  <input type="checkbox" name="use_scrap" id="use_scrap" value="1"  <?php echo set_checkbox('use_scrap', '1', (element('use_scrap', element('data', $view))?TRUE:FALSE)); ?> /> 사용합니다
					  </label>
				</div>
				<div class="col-sm-2">
					  <label for="grp_use_scrap"  class="checkbox-inline">
						  <input type="checkbox" name="grp[use_scrap]" id="grp_use_scrap" value="1"  /> 그룹적용
					  </label>
					  <label for="all_use_scrap"  class="checkbox-inline">
						  <input type="checkbox" name="all[use_scrap]" id="all_use_scrap" value="1"  /> 전체적용
					  </label>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label">추천기능</label>
				<div class="col-sm-8">
					  <label for="use_post_like"  class="checkbox-inline">
						  <input type="checkbox" name="use_post_like" id="use_post_like" value="1"  <?php echo set_checkbox('use_post_like', '1', (element('use_post_like', element('data', $view))?TRUE:FALSE)); ?> /> 사용합니다
					  </label>
				</div>
				<div class="col-sm-2">
					  <label for="grp_use_post_like"  class="checkbox-inline">
						  <input type="checkbox" name="grp[use_post_like]" id="grp_use_post_like" value="1"  /> 그룹적용
					  </label>
					  <label for="all_use_post_like"  class="checkbox-inline">
						  <input type="checkbox" name="all[use_post_like]" id="all_use_post_like" value="1"  /> 전체적용
					  </label>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label">비추천 기능</label>
				<div class="col-sm-8">
					  <label for="use_post_dislike"  class="checkbox-inline">
						  <input type="checkbox" name="use_post_dislike" id="use_post_dislike" value="1"  <?php echo set_checkbox('use_post_dislike', '1', (element('use_post_dislike', element('data', $view))?TRUE:FALSE)); ?> /> 사용합니다
					  </label>
				</div>
				<div class="col-sm-2">
					  <label for="grp_use_post_dislike"  class="checkbox-inline">
						  <input type="checkbox" name="grp[use_post_dislike]" id="grp_use_post_dislike" value="1"  /> 그룹적용
					  </label>
					  <label for="all_use_post_dislike"  class="checkbox-inline">
						  <input type="checkbox" name="all[use_post_dislike]" id="all_use_post_dislike" value="1"  /> 전체적용
					  </label>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label">본문 인쇄 기능</label>
				<div class="col-sm-8">
					  <label for="use_print"  class="checkbox-inline">
						  <input type="checkbox" name="use_print" id="use_print" value="1"  <?php echo set_checkbox('use_print', '1', (element('use_print', element('data', $view))?TRUE:FALSE)); ?> /> 사용합니다
					  </label>
				</div>
				<div class="col-sm-2">
					  <label for="grp_use_print"  class="checkbox-inline">
						  <input type="checkbox" name="grp[use_print]" id="grp_use_print" value="1"  /> 그룹적용
					  </label>
					  <label for="all_use_print"  class="checkbox-inline">
						  <input type="checkbox" name="all[use_print]" id="all_use_print" value="1"  /> 전체적용
					  </label>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label">SNS 보내기 버튼</label>
				<div class="col-sm-8">
					  <label for="use_sns"  class="checkbox-inline">
						  <input type="checkbox" name="use_sns" id="use_sns" value="1"  <?php echo set_checkbox('use_sns', '1', (element('use_sns', element('data', $view))?TRUE:FALSE)); ?> /> PC,
					  </label>
					  <label for="use_mobile_sns"  class="checkbox-inline">
						  <input type="checkbox" name="use_mobile_sns" id="use_mobile_sns" value="1"  <?php echo set_checkbox('use_mobile_sns', '1', (element('use_mobile_sns', element('data', $view))?TRUE:FALSE)); ?> /> 모바일
					  </label>
					  <span class="help-inline">뷰페이지 하단에 소셜링크 버튼이 생성됩니다</span>
				</div>
				<div class="col-sm-2">
					  <label for="grp_use_sns"  class="checkbox-inline">
						  <input type="checkbox" name="grp[use_sns]" id="grp_use_sns" value="1"  /> 그룹적용
					  </label>
					  <label for="all_use_sns"  class="checkbox-inline">
						  <input type="checkbox" name="all[use_sns]" id="all_use_sns" value="1"  /> 전체적용
					  </label>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label">이전글, 다음글 버튼</label>
				<div class="col-sm-8">
					  <label for="use_prev_next_post"  class="checkbox-inline">
						  <input type="checkbox" name="use_prev_next_post" id="use_prev_next_post" value="1"  <?php echo set_checkbox('use_prev_next_post', '1', (element('use_prev_next_post', element('data', $view))?TRUE:FALSE)); ?> /> PC,
					  </label>
					  <label for="use_mobile_prev_next_post"  class="checkbox-inline">
						  <input type="checkbox" name="use_mobile_prev_next_post" id="use_mobile_prev_next_post" value="1"  <?php echo set_checkbox('use_mobile_prev_next_post', '1', (element('use_mobile_prev_next_post', element('data', $view))?TRUE:FALSE)); ?> /> 모바일
					  </label>
					  <span class="help-inline">뷰페이지 하단에 이전글, 다음글 버튼이 생성됩니다</span>
				</div>
				<div class="col-sm-2">
					  <label for="grp_use_prev_next_post"  class="checkbox-inline">
						  <input type="checkbox" name="grp[use_prev_next_post]" id="grp_use_prev_next_post" value="1"  /> 그룹적용
					  </label>
					  <label for="all_use_prev_next_post"  class="checkbox-inline">
						  <input type="checkbox" name="all[use_prev_next_post]" id="all_use_prev_next_post" value="1"  /> 전체적용
					  </label>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label">게시물 신고기능</label>
				<div class="col-sm-8">
					  <label for="use_blame"  class="checkbox-inline">
						  <input type="checkbox" name="use_blame" id="use_blame" value="1"  <?php echo set_checkbox('use_blame', '1', (element('use_blame', element('data', $view))?TRUE:FALSE)); ?> /> 사용합니다
					  </label>
				</div>
				<div class="col-sm-2">
					  <label for="grp_use_blame"  class="checkbox-inline">
						  <input type="checkbox" name="grp[use_blame]" id="grp_use_blame" value="1"  /> 그룹적용
					  </label>
					  <label for="all_use_blame"  class="checkbox-inline">
						  <input type="checkbox" name="all[use_blame]" id="all_use_blame" value="1"  /> 전체적용
					  </label>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label">신고시 블라인드</label>
				<div class="col-sm-8">
						<input type="number" class="form-control" name="blame_blind_count" value="<?php echo set_value('blame_blind_count', element('blame_blind_count', element('data', $view))+0); ?>" />회 이상 신고가 발생하면 게시물을 블라인드 처리합니다. 블라인드된 게시물은 관리자와 본인만 열람이 가능합니다.
				</div>
				<div class="col-sm-2">
					  <label for="grp_blame_blind_count"  class="checkbox-inline">
						  <input type="checkbox" name="grp[blame_blind_count]" id="grp_blame_blind_count" value="1"  /> 그룹적용
					  </label>
					  <label for="all_blame_blind_count"  class="checkbox-inline">
						  <input type="checkbox" name="all[blame_blind_count]" id="all_blame_blind_count" value="1"  /> 전체적용
					  </label>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label">Syntax Highlighter 기능 사용</label>
				<div class="col-sm-8">
					  <label for="syntax_highlighter"  class="checkbox-inline">
						  <input type="checkbox" name="syntax_highlighter" id="syntax_highlighter" value="1"  <?php echo set_checkbox('syntax_highlighter', '1', (element('syntax_highlighter', element('data', $view))?TRUE:FALSE)); ?> /> 원글
					  </label>
					  <label for="comment_syntax_highlighter"  class="checkbox-inline">
						  <input type="checkbox" name="comment_syntax_highlighter" id="comment_syntax_highlighter" value="1"  <?php echo set_checkbox('comment_syntax_highlighter', '1', (element('comment_syntax_highlighter', element('data', $view))?TRUE:FALSE)); ?> /> 댓글
					  </label>
					  <span class="help-inline">사용하시면syntax highlight 적용 (사용방법: [code] 소스코드 [/code])</span>
				</div>
				<div class="col-sm-2">
					  <label for="grp_syntax_highlighter"  class="checkbox-inline">
						  <input type="checkbox" name="grp[syntax_highlighter]" id="grp_syntax_highlighter" value="1"  /> 그룹적용
					  </label>
					  <label for="all_syntax_highlighter"  class="checkbox-inline">
						  <input type="checkbox" name="all[syntax_highlighter]" id="all_syntax_highlighter" value="1"  /> 전체적용
					  </label>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label">자동실행</label>
				<div class="col-sm-8">
					  <label for="use_autoplay"  class="checkbox-inline">
						  <input type="checkbox" name="use_autoplay" id="use_autoplay" value="1"  <?php echo set_checkbox('use_autoplay', '1', (element('use_autoplay', element('data', $view))?TRUE:FALSE)); ?> /> 첨부파일의 동영상, 오디오 및 링크주소(유투브, 비메오) 등을 자동실행합니다.
					  </label>
				</div>
				<div class="col-sm-2">
					  <label for="grp_use_autoplay"  class="checkbox-inline">
						  <input type="checkbox" name="grp[use_autoplay]" id="grp_use_autoplay" value="1"  /> 그룹적용
					  </label>
					  <label for="all_use_autoplay"  class="checkbox-inline">
						  <input type="checkbox" name="all[use_autoplay]" id="all_use_autoplay" value="1"  /> 전체적용
					  </label>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label">이미지 폭 크기</label>
				<div class="col-sm-8">
						PC - <input type="number" class="form-control" name="post_image_width" value="<?php echo set_value('post_image_width', element('post_image_width', element('data', $view))+0); ?>" />px,
						모바일 - <input type="number" class="form-control" name="post_mobile_image_width" value="<?php echo set_value('post_mobile_image_width', element('post_mobile_image_width', element('data', $view))+0); ?>" />px,
						<span class="help-inline">게시판 본문에 이미지 가로값</span>
				</div>
				<div class="col-sm-2">
					  <label for="grp_post_image_width"  class="checkbox-inline">
						  <input type="checkbox" name="grp[post_image_width]" id="grp_post_image_width" value="1"  /> 그룹적용
					  </label>
					  <label for="all_post_image_width"  class="checkbox-inline">
						  <input type="checkbox" name="all[post_image_width]" id="all_post_image_width" value="1"  /> 전체적용
					  </label>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label">IP 보이기</label>
				<div class="col-sm-8 form-inline">
						PC - <select name="show_ip" class="form-control" >
							<option value="" <?php echo set_select('show_ip', '', (element('show_ip', element('data', $view)) == ''?TRUE:FALSE)); ?>>공개하지 않음</option>
							<option value="1" <?php echo set_select('show_ip', '1', (element('show_ip', element('data', $view)) == '1'?TRUE:FALSE)); ?>>일부 공개(기본환경설정에 정한방법)</option>
							<option value="2" <?php echo set_select('show_ip', '2', (element('show_ip', element('data', $view)) == '2'?TRUE:FALSE)); ?>>전체 공개</option>
						</select>,
						모바일 - <select name="show_mobile_ip" class="form-control" >
							<option value="" <?php echo set_select('show_mobile_ip', '', (element('show_mobile_ip', element('data', $view)) == ''?TRUE:FALSE)); ?>>공개하지 않음</option>
							<option value="1" <?php echo set_select('show_mobile_ip', '1', (element('show_mobile_ip', element('data', $view)) == '1'?TRUE:FALSE)); ?>>일부 공개(기본환경설정에 정한방법)</option>
							<option value="2" <?php echo set_select('show_mobile_ip', '2', (element('show_mobile_ip', element('data', $view)) == '2'?TRUE:FALSE)); ?>>전체 공개</option>
						</select>
						<span class="help-block">관리자에게는 IP 가 항상 보입니다</span>
				</div>
				<div class="col-sm-2">
					  <label for="grp_show_ip"  class="checkbox-inline">
						  <input type="checkbox" name="grp[show_ip]" id="grp_show_ip" value="1"  /> 그룹적용
					  </label>
					  <label for="all_show_ip"  class="checkbox-inline">
						  <input type="checkbox" name="all[show_ip]" id="all_show_ip" value="1"  /> 전체적용
					  </label>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label">링크 새창</label>
				<div class="col-sm-8">
					  <label for="content_target_blank"  class="checkbox-inline">
						  <input type="checkbox" name="content_target_blank" id="content_target_blank" value="1"  <?php echo set_checkbox('content_target_blank', '1', (element('content_target_blank', element('data', $view))?TRUE:FALSE)); ?> /> PC
					  </label>
					  <label for="mobile_content_target_blank"  class="checkbox-inline">
						  <input type="checkbox" name="mobile_content_target_blank" id="mobile_content_target_blank" value="1"  <?php echo set_checkbox('mobile_content_target_blank', '1', (element('mobile_content_target_blank', element('data', $view))?TRUE:FALSE)); ?> /> 모바일
					  </label>
					  <span class="help-inline">본문 안의 링크가 무조건 새창으로 열립니다</span>
				</div>
				<div class="col-sm-2">
					  <label for="grp_content_target_blank"  class="checkbox-inline">
						  <input type="checkbox" name="grp[content_target_blank]" id="grp_content_target_blank" value="1"  /> 그룹적용
					  </label>
					  <label for="all_content_target_blank"  class="checkbox-inline">
						  <input type="checkbox" name="all[content_target_blank]" id="all_content_target_blank" value="1"  /> 전체적용
					  </label>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label">본문 안의 URL 자동 링크</label>
				<div class="col-sm-8">
					  <label for="use_auto_url"  class="checkbox-inline">
						  <input type="checkbox" name="use_auto_url" id="use_auto_url" value="1"  <?php echo set_checkbox('use_auto_url', '1', (element('use_auto_url', element('data', $view))?TRUE:FALSE)); ?> /> PC
						</label>
					  <label for="use_mobile_auto_url"  class="checkbox-inline">
						  <input type="checkbox" name="use_mobile_auto_url" id="use_mobile_auto_url" value="1"  <?php echo set_checkbox('use_mobile_auto_url', '1', (element('use_mobile_auto_url', element('data', $view))?TRUE:FALSE)); ?> /> 모바일
					  </label>
					  <span class="help-inline">본문 내용 중 URL은 무조건 자동으로 링크를 생성합니다</span>
				</div>
				<div class="col-sm-2">
					  <label for="grp_use_auto_url"  class="checkbox-inline">
						  <input type="checkbox" name="grp[use_auto_url]" id="grp_use_auto_url" value="1"  /> 그룹적용
					  </label>
					  <label for="all_use_auto_url"  class="checkbox-inline">
						  <input type="checkbox" name="all[use_auto_url]" id="all_use_auto_url" value="1"  /> 전체적용
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

<script type='text/javascript'>
//<![CDATA[
$(function() {
	$("#fadminwrite").validate({
		rules: {
			blame_blind_count: {required :true, number:true, min:0 },
			post_image_width: {required :true, number:true, min:0 },
			post_mobile_image_width: {required :true, number:true, min:0 }
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
