	<div class="box">
		<div class="box-table">
				  <div class="box-table-header">
					  <ul class="nav nav-pills">
						  <li role="presentation"><a href="<?php echo admin_url($this->pagedir); ?>">신고현황</a></li>
						  <li role="presentation" class="active"><a href="<?php echo admin_url($this->pagedir . '/graph'); ?>">기간별 그래프</a></li>
						</ul>
<form class="form-inline" name="flist" action="<?php echo current_url(); ?>" method="get" >
<input type="hidden" name="datetype" value="<?php echo html_escape($this->input->get('datetype')); ?>" />
						<div class="box-table-button">
							<?php if(element('boardlist', $view)) { ?>
							<span class="mr10">
								<select name="brd_id" class="form-control">
									<option value="">전체게시판</option>
								<?php foreach(element('boardlist', $view) as $key => $value) { ?>
									<option value="<?php echo element('brd_id', $value); ?>" <?php echo set_select('brd_id', element('brd_id', $value), ($this->input->get('brd_id') == element('brd_id', $value)?TRUE:FALSE)); ?>><?php echo html_escape(element('brd_name', $value)); ?></option>
								<?php } ?>
							</select>
							</span>
							<?php } ?>
							<span class="mr10">기간 : <input type="text" class="form-control input-small datepicker " name="start_date" value="<?php echo element('start_date', $view); ?>" readonly="readonly" /> - <input type="text" class="form-control input-small datepicker" name="end_date" value="<?php echo element('end_date', $view); ?>" readonly="readonly" />
							</span>
							<div class="btn-group" role="group" aria-label="...">
							  <button type="button" class="btn <?php echo ($this->input->get('datetype') != 'y' && $this->input->get('datetype') != 'm')?'btn-success':'btn-default'; ?> btn-sm" onclick="fdate_submit('d');">일별보기</button>
							  <button type="button" class="btn <?php echo ($this->input->get('datetype') == 'm')?'btn-success':'btn-default'; ?> btn-sm" onclick="fdate_submit('m');">월별보기</button>
							  <button type="button" class="btn <?php echo ($this->input->get('datetype') == 'y')?'btn-success':'btn-default'; ?> btn-sm" onclick="fdate_submit('y');">년별보기</button>
							</div>
						  </div>
</form>
<script type="text/javascript">
//<![CDATA[
	function fdate_submit(datetype)
	{
		var f = document.flist;
		f.datetype.value = datetype;
		f.submit();
	}
//]]>
</script>
					  </div>
				  <div class="table-responsive">
				  	<table class="table table-hover table-striped table-bordered">
					  <colgroup>
						  <col class="col-md-2">
						  <col class="col-md-2">
						  <col class="col-md-2">
						  <col class="col-md-6">
					  </colgroup>
					  <thead>
					  	<tr>
							<th>기간</th>
							<th>신고수</th>
							<th>비율</th>
							<th>그래프</th>
						</tr>
					  </thead>
					  <tbody>
<?php
if (element('list', $view))
{
	foreach (element('list', $view) as $result)
	{
?>
						  <tr>
							  <td><?php echo element('key', $result); ?></td>
							  <td><?php echo element('count', $result); ?></td>
							  <td><?php echo element('s_rate', $result); ?>%</td>
							  <td>
							  	<div class="progress">
								  <div class="progress-bar progress-bar-warning progress-bar-striped" role="progressbar" aria-valuenow="<?php echo element('s_rate', $result); ?>" aria-valuemin="0" aria-valuemax="<?php echo element('max_value' , $view); ?>" style="width: <?php echo element('bar', $result); ?>%">
									<span class="sr-only"><?php echo element('s_rate', $result); ?>% Complete</span>
								  </div>
								</div>
							  </td>
						  </tr>

<?php
	}
}
if ( ! element('list', $view))
{
?>
						  <tr>
							  <td colspan="4" class="nopost">자료가 없습니다</td>
						  </tr>
<?php
}
?>

					  </tbody>
<?php
if (element('list', $view))
{
?>
					  <tfoot>
					  	<tr class="warning">
							<td>전체</td>
							<td><?php echo element('sum_count', $view); ?></td>
							<td></td>
							<td></td>
						</tr>
					  </tfoot>
<?php
}
?>
				  </table>
				  </div>
				</div>
			</div>
