<div class="box">
    <div class="box-header">
        <h4 class="pb10"><?php echo html_escape($this->board->item_id('brd_name', element('brd_id', element('data', $view)))); ?> <a href="<?php echo goto_url(board_url(html_escape($this->board->item_id('brd_key', element('brd_id', element('data', $view)))))); ?>" class="btn-xs" target="_blank"><span class="glyphicon glyphicon-new-window"></span></a></h4>
        <ul class="nav nav-tabs">
            <li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/write/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">기본정보</a></li>
            <li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/write_list/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">목록페이지</a></li>
            <li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/write_post/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">게시물열람</a></li>
            <li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/write_write/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">게시물작성</a></li>
            <li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/write_category/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">카테고리</a></li>
            <li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/write_comment/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">댓글기능</a></li>
            <li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/write_general/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">일반기능</a></li>
            <li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/write_point/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">포인트기능</a></li>
            <li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/write_alarm/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">메일/쪽지/문자</a></li>
            <li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/write_rss/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">RSS 설정</a></li>
            <li role="presentation" class="active"><a href="<?php echo admin_url($this->pagedir . '/write_access/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">권한관리</a></li>
            <li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/write_extravars/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">사용자정의</a></li>
            <li role="presentation"><a href="<?php echo admin_url($this->pagedir . '/write_admin/' . element('brd_id', element('data', $view))); ?>" onclick="return check_form_changed();">게시판관리자</a></li>
        </ul>
    </div>
    <div class="box-table">
        <?php
        echo validation_errors('<div class="alert alert-warning" role="alert">', '</div>');
        echo show_alert_message(element('alert_message', $view), '<div class="alert alert-auto-close alert-dismissible alert-info"><button type="button" class="close alertclose" >&times;</button>', '</div>');
        $attributes = array('class' => 'form-horizontal', 'name' => 'fadminwrite', 'id' => 'fadminwrite');
        echo form_open(current_full_url(), $attributes);
        ?>
            <input type="hidden" name="is_submit" value="1" />
            <input type="hidden" name="<?php echo element('primary_key', $view); ?>"    value="<?php echo element(element('primary_key', $view), element('data', $view)); ?>" />
            <div class="form-horizontal">
                <div class="form-group">
                    <label class="col-sm-2 control-label">목록</label>
                    <div class="col-sm-8 form-inline">
                        <?php
                        $config = array(
                            'column_name' => 'access_list',
                            'column_level_name' => 'access_list_level',
                            'column_value' => element('access_list', element('data', $view)),
                            'column_level_value' => element('access_list_level', element('data', $view)),
                            'max_level' => element('config_max_level', element('data', $view)),
                            'mgroup' => element('mgroup', element('data', $view)),
                            );
                        echo get_access_selectbox($config);
                        ?>
                    </div>
                    <div class="col-sm-2">
                        <label for="grp_access_list" class="checkbox-inline">
                            <input type="checkbox" name="grp[access_list]" id="grp_access_list" value="1" /> 그룹적용
                        </label>
                        <label for="all_access_list" class="checkbox-inline">
                            <input type="checkbox" name="all[access_list]" id="all_access_list" value="1" /> 전체적용
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">글열람</label>
                    <div class="col-sm-8 form-inline">
                        <?php
                        $config = array(
                            'column_name' => 'access_view',
                            'column_level_name' => 'access_view_level',
                            'column_value' => element('access_view', element('data', $view)),
                            'column_level_value' => element('access_view_level', element('data', $view)),
                            'max_level' => element('config_max_level', element('data', $view)),
                            'mgroup' => element('mgroup', element('data', $view)),
                            );
                        echo get_access_selectbox($config);
                        ?>
                    </div>
                    <div class="col-sm-2">
                        <label for="grp_access_view" class="checkbox-inline">
                            <input type="checkbox" name="grp[access_view]" id="grp_access_view" value="1" /> 그룹적용
                        </label>
                        <label for="all_access_view" class="checkbox-inline">
                            <input type="checkbox" name="all[access_view]" id="all_access_view" value="1" /> 전체적용
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">글 작성</label>
                    <div class="col-sm-8 form-inline">
                        <?php
                        $config = array(
                            'column_name' => 'access_write',
                            'column_level_name' => 'access_write_level',
                            'column_value' => element('access_write', element('data', $view)),
                            'column_level_value' => element('access_write_level', element('data', $view)),
                            'max_level' => element('config_max_level', element('data', $view)),
                            'mgroup' => element('mgroup', element('data', $view)),
                            );
                        echo get_access_selectbox($config);
                        ?>
                    </div>
                    <div class="col-sm-2">
                        <label for="grp_access_write" class="checkbox-inline">
                            <input type="checkbox" name="grp[access_write]" id="grp_access_write" value="1" /> 그룹적용
                        </label>
                        <label for="all_access_write" class="checkbox-inline">
                            <input type="checkbox" name="all[access_write]" id="all_access_write" value="1" /> 전체적용
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">답변 작성</label>
                    <div class="col-sm-8 form-inline">
                        <?php
                        $config = array(
                            'column_name' => 'access_reply',
                            'column_level_name' => 'access_reply_level',
                            'column_value' => element('access_reply', element('data', $view)),
                            'column_level_value' => element('access_reply_level', element('data', $view)),
                            'max_level' => element('config_max_level', element('data', $view)),
                            'mgroup' => element('mgroup', element('data', $view)),
                            );
                        echo get_access_selectbox($config);
                        ?>
                    </div>
                    <div class="col-sm-2">
                        <label for="grp_access_reply" class="checkbox-inline">
                            <input type="checkbox" name="grp[access_reply]" id="grp_access_reply" value="1" /> 그룹적용
                        </label>
                        <label for="all_access_reply" class="checkbox-inline">
                            <input type="checkbox" name="all[access_reply]" id="all_access_reply" value="1" /> 전체적용
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">댓글 작성</label>
                    <div class="col-sm-8 form-inline">
                        <?php
                        $config = array(
                            'column_name' => 'access_comment',
                            'column_level_name' => 'access_comment_level',
                            'column_value' => element('access_comment', element('data', $view)),
                            'column_level_value' => element('access_comment_level', element('data', $view)),
                            'max_level' => element('config_max_level', element('data', $view)),
                            'mgroup' => element('mgroup', element('data', $view)),
                            );
                        echo get_access_selectbox($config);
                        ?>
                    </div>
                    <div class="col-sm-2">
                        <label for="grp_access_comment" class="checkbox-inline">
                            <input type="checkbox" name="grp[access_comment]" id="grp_access_comment" value="1" /> 그룹적용
                        </label>
                        <label for="all_access_comment" class="checkbox-inline">
                            <input type="checkbox" name="all[access_comment]" id="all_access_comment" value="1" /> 전체적용
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">파일업로드</label>
                    <div class="col-sm-8 form-inline">
                    <?php
                        $config = array(
                            'column_name' => 'access_upload',
                            'column_level_name' => 'access_upload_level',
                            'column_value' => element('access_upload', element('data', $view)),
                            'column_level_value' => element('access_upload_level', element('data', $view)),
                            'max_level' => element('config_max_level', element('data', $view)),
                            'mgroup' => element('mgroup', element('data', $view)),
                            );
                        echo get_access_selectbox($config);
                        ?>
                    </div>
                    <div class="col-sm-2">
                        <label for="grp_access_upload" class="checkbox-inline">
                            <input type="checkbox" name="grp[access_upload]" id="grp_access_upload" value="1" /> 그룹적용
                        </label>
                        <label for="all_access_upload" class="checkbox-inline">
                            <input type="checkbox" name="all[access_upload]" id="all_access_upload" value="1" /> 전체적용
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">파일다운로드</label>
                    <div class="col-sm-8 form-inline">
                        <?php
                        $config = array(
                            'column_name' => 'access_download',
                            'column_level_name' => 'access_download_level',
                            'column_value' => element('access_download', element('data', $view)),
                            'column_level_value' => element('access_download_level', element('data', $view)),
                            'max_level' => element('config_max_level', element('data', $view)),
                            'mgroup' => element('mgroup', element('data', $view)),
                            );
                        echo get_access_selectbox($config);
                        ?>
                    </div>
                    <div class="col-sm-2">
                        <label for="grp_access_download" class="checkbox-inline">
                            <input type="checkbox" name="grp[access_download]" id="grp_access_download" value="1" /> 그룹적용
                        </label>
                        <label for="all_access_download" class="checkbox-inline">
                            <input type="checkbox" name="all[access_download]" id="all_access_download" value="1" /> 전체적용
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">원글 DHTML 에디터 사용</label>
                    <div class="col-sm-8 form-inline">
                        <?php
                        $config = array(
                            'column_name' => 'access_dhtml',
                            'column_level_name' => 'access_dhtml_level',
                            'column_value' => element('access_dhtml', element('data', $view)),
                            'column_level_value' => element('access_dhtml_level', element('data', $view)),
                            'max_level' => element('config_max_level', element('data', $view)),
                            'mgroup' => element('mgroup', element('data', $view)),
                            );
                        echo get_access_selectbox($config);
                        ?>
                    </div>
                    <div class="col-sm-2">
                        <label for="grp_access_dhtml" class="checkbox-inline">
                            <input type="checkbox" name="grp[access_dhtml]" id="grp_access_dhtml" value="1" /> 그룹적용
                        </label>
                        <label for="all_access_dhtml" class="checkbox-inline">
                            <input type="checkbox" name="all[access_dhtml]" id="all_access_dhtml" value="1" /> 전체적용
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">게시물신고기능</label>
                    <div class="col-sm-8 form-inline">
                        <?php
                        $config = array(
                            'column_name' => 'access_blame',
                            'column_level_name' => 'access_blame_level',
                            'column_value' => element('access_blame', element('data', $view)),
                            'column_level_value' => element('access_blame_level', element('data', $view)),
                            'max_level' => element('config_max_level', element('data', $view)),
                            'mgroup' => element('mgroup', element('data', $view)),
                            );
                        echo get_access_selectbox($config, true);
                        ?>
                    </div>
                    <div class="col-sm-2">
                        <label for="grp_access_blame" class="checkbox-inline">
                            <input type="checkbox" name="grp[access_blame]" id="grp_access_blame" value="1" /> 그룹적용
                        </label>
                        <label for="all_access_blame" class="checkbox-inline">
                            <input type="checkbox" name="all[access_blame]" id="all_access_blame" value="1" /> 전체적용
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
var form_original_data = $('#fadminwrite').serialize();
function check_form_changed() {
    if ($('#fadminwrite').serialize() !== form_original_data) {
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
