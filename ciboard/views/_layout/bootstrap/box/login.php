<?php if ($this->member->is_member() == FALSE) { ?>
<!-- login start -->
<?php
	$attributes = array('class' => 'form-horizontal', 'name' => 'fsidelogin', 'id' => 'fsidelogin');
	echo form_open(site_url('login'), $attributes);
?>
<input type="hidden" name="url" value="<?php echo $this->uri->uri_string(); ?>" />
<input type="hidden" name="returnurl" value="<?php echo $this->uri->uri_string(); ?>" />
<div class="loginbox mb10">
	<div class="headline">
		<h3>로그인</h3>
	</div>
<?php echo $this->session->flashdata('loginvalidationmessage'); ?>
	<input type="text" class="form-control mb10" name="mem_userid" placeholder="Enter User ID" value="<?php echo $this->session->flashdata('loginuserid'); ?>" />
	<input type="password" class="form-control mb10" name="mem_password" placeholder="Enter Password" />
	<button class="btn btn-primary btn-sm pull-left" type="submit">로그인</button>
	<ul class="text pull-right">
	  <li><a href="<?php echo site_url('register'); ?>" title="회원가입">회원가입</a></li>
	  <li>|</li>
	  <li><a href="<?php echo site_url('findaccount'); ?>" title="회원정보찾기">회원정보찾기</a></li>
	</ul>
</div>
<?php echo form_close(); ?>
<script type='text/javascript'>
//<![CDATA[
	$(function() {
		$("#fsidelogin").validate({
			rules: {
				mem_userid: {required:true, minlength:3},
				mem_password: {required:true, minlength:4}
			}
		});
	});
//]]>
</script>
<!-- login end -->
<?php } else { ?>
<!-- welcome start -->
<div class="welcome mb10">
	  <div class="headline">
		<h3><?php echo html_escape($this->member->item('mem_nickname')); ?>님 어서오세요.</h3>
	  </div>
	  <div class="mb10">
<?php if($this->cbconfig->item('use_note') && $this->member->item('mem_use_note')) { ?>
		<p><strong>쪽지</strong> : <a href="javascript:;" onClick="note_list();" title="나의 쪽지"><?php echo number_format($this->member->item('meta_unread_note_num')+0); ?> 개</a></p>
<?php } ?>
<?php if($this->cbconfig->item('use_point')) { ?>
		<p><strong>포인트</strong> :<a href="<?php echo site_url('mypage/point'); ?>" title="나의 포인트"><?php echo number_format($this->member->item('mem_point')); ?> 점</a></p>
<?php } ?>
	  </div>
	  <ul class="mt20">
		<li><a href="javascript:;" onClick="open_profile('<?php echo $this->member->item('mem_userid'); ?>');"  class="btn btn-default btn-xs" title="나의 프로필">프로필</a></li>
		<li><a href="<?php echo site_url('mypage'); ?>" class="btn btn-default btn-xs" title="마이페이지">마이페이지</a></li>
		<li><a href="<?php echo site_url('mypage/scrap'); ?>" class="btn btn-default btn-xs" title="나의 스크랩">스크랩</a></li>
		<li><a href="<?php echo site_url('membermodify'); ?>" class="btn btn-default btn-xs" title="정보수정">정보수정</a></li>
		<li><a href="<?php echo site_url('login/logout?url=' . $this->uri->uri_string()); ?>" class="btn btn-default btn-xs" title="로그아웃">로그아웃</a></li>
	  </ul>
</div>
<!-- welcome end -->
<?php } ?>