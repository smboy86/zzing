<div class="box">
		 <div class="box-table">

<?php echo validation_errors('<div class="alert alert-warning" role="alert">', '</div>'); ?>
<?php echo show_alert_message(element('alert_message', $view), '<div class="alert alert-auto-close alert-dismissible alert-info"><button type="button" class="close alertclose" >&times;</button>', '</div>'); ?>
<?php
	$attributes = array('class' => 'form-horizontal', 'name' => 'fadminwrite', 'id' => 'fadminwrite');
	echo form_open(current_full_url(), $attributes);
?>
<input type="hidden" name="is_submit" value="1" />
			<div class="form-horizontal">
			  <div class="form-group">
				<label class="col-sm-2 control-label">통합 RSS 주소</label>
				<div class="col-sm-10" style="padding-top:7px;">
					<label class=" form-inline"><span class="fa fa-rss"></span>
					<a href="<?php echo rss_url(); ?>" target="_blank"><?php echo rss_url(); ?></a></label>

				</div>
			   </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label">포함된 게시판 목록</label>
				<div class="col-sm-10" style="padding-top:7px;">
					<?php
					if (element('rssboard', $view)) {
						foreach (element('rssboard', $view) as $rval) {
							echo '<a href="'.board_url(element('brd_key', $rval)).'" target="_blank">';
							echo html_escape(element('brd_name', $rval));
							echo '</a> , ';
						}
					}
					?>
				</div>
			   </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label">통합 RSS 피드 사용</label>
				<div class="col-sm-10">
					  <label for="use_total_rss_feed"  class="checkbox-inline">
					  <input type="checkbox" name="use_total_rss_feed" id="use_total_rss_feed" value="1"  <?php echo set_checkbox('use_total_rss_feed', '1', (element('use_total_rss_feed', element('data', $view))?TRUE:FALSE)); ?> /> 사용합니다
					  </label>
				 </div>
			   </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label">내용공개</label>
				<div class="col-sm-10 form-inline">
						<select name="total_rss_feed_content" class="form-control" >
							<option value="" <?php echo set_select('total_rss_feed_content', '', (element('total_rss_feed_content', element('data', $view)) == ''?TRUE:FALSE)); ?>>공개하지 않음</option>
							<option value="1" <?php echo set_select('total_rss_feed_content', '1', (element('total_rss_feed_content', element('data', $view)) == '1'?TRUE:FALSE)); ?>>HTML 태그 제외 공개</option>
							<option value="2" <?php echo set_select('total_rss_feed_content', '2', (element('total_rss_feed_content', element('data', $view)) == '2'?TRUE:FALSE)); ?>>전부공개</option>
						</select>
						<span class="help-inline">RSS 페이지에 본문 내용을 얼마나 공개할 것인지 설정합니다</span>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label">통합 RSS 제목</label>
				<div class="col-sm-10">
					   <input type="text" class="form-control" name="total_rss_feed_title" id="total_rss_feed_title" value="<?php echo set_value('total_rss_feed_title', element('total_rss_feed_title', element('data', $view))); ?>" />
				 </div>
			   </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label">통합 RSS 설명</label>
				<div class="col-sm-10">
					   <textarea class="form-control" rows="5" name="total_rss_feed_description"><?php echo set_value('total_rss_feed_description', element('total_rss_feed_description', element('data', $view))); ?></textarea>
				 </div>
			   </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label">RSS 표시 저작권</label>
				<div class="col-sm-10">
					  <input type="text" class="form-control" name="total_rss_feed_copyright" id="total_rss_feed_copyright" value="<?php echo set_value('total_rss_feed_copyright', element('total_rss_feed_copyright', element('data', $view))); ?>" />
				 </div>
			   </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label">RSS 출력 게시물수</label>
				<div class="col-sm-10">
					  <input type="number" class="form-control" name="total_rss_feed_count" id="total_rss_feed_count" value="<?php echo set_value('total_rss_feed_count', element('total_rss_feed_count', element('data', $view))); ?>" />
				 </div>
			   </div>
				<div class="btn-group pull-right" role="group" aria-label="...">
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
				total_rss_feed_title: {required :"#use_total_rss_feed:checked"},
				total_rss_feed_description: {required :"#use_total_rss_feed:checked"},
				total_rss_feed_copyright: {required :"#use_total_rss_feed:checked"},
				total_rss_feed_count: {required :"#use_total_rss_feed:checked" ,number:true, min:0, max:1000}
			}
		});
	});
//]]>
</script>
