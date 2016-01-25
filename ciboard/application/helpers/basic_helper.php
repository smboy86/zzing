<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Baisc helper
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

if ( ! function_exists('alert'))
{
	// Alert 띄우기
	function alert($msg = '', $url = '')
	{
		$CI =& get_instance();
		if ( ! $msg) {
			$msg = '잘못된 접근입니다';
		}
		echo '<meta http-equiv="content-type" content="text/html; charset=' . $CI->config->item('charset') . '">';
		echo '<script type="text/javascript">alert("' . $msg . '");';
		if ( ! $url) echo 'history.go(-1);';
		if ($url) echo 'document.location.href="' . $url . '"';
		echo '</script>';
		exit;
	}
}

if ( ! function_exists('alert_close'))
{
	// Alert 후 창 닫음
	function alert_close($msg = '')
	{
		$CI =& get_instance();
		if ( ! $msg) {
			$msg = '잘못된 접근입니다';
		}
		echo '<meta http-equiv="content-type" content="text/html; charset=' . $CI->config->item('charset') . '">';
		echo '<script type="text/javascript"> alert("' . $msg . '"); window.close(); </script>';
		exit;
	}
}

if ( ! function_exists('alert_refresh_close'))
{
	// Alert 후 창 닫음
	function alert_refresh_close($msg = '')
	{
		$CI =& get_instance();
		if ( ! $msg) {
			$msg = '잘못된 접근입니다';
		}
		echo '<meta http-equiv="content-type" content="text/html; charset=' . $CI->config->item('charset') . '">';
		echo '<script type="text/javascript"> alert("' . $msg . '"); window.opener.location.reload();window.close(); </script>';
		exit;
	}
}

if ( ! function_exists('cdate'))
{
	/**
	 * DATE 함수의 약간 변형
	 */
	function cdate($date, $timestamp='')
	{
		defined('TIMESTAMP') or define('TIMESTAMP' , time());
		return $timestamp ? date($date, $timestamp) : date($date, TIMESTAMP);
	}
}

if ( ! function_exists('ctimestamp'))
{
	/**
	 * TIMESTAMP 불러오기
	 */
	function ctimestamp()
	{
		defined('TIMESTAMP') or define('TIMESTAMP' , time());
		return TIMESTAMP;
	}
}

if ( ! function_exists('array_to_keys'))
{
	function array_to_keys($array='')
	{
		$result = array();
		if ( ! is_array($array)) return FALSE;
		foreach ($array as $key) {
			$result[$key] = FALSE;
		}
		return $result;
	}
}

if ( ! function_exists('search_option'))
{
	/**
	 * 검색 select option
	 */
	function search_option($options='', $selected='')
	{
		if ( ! $options OR ! is_array($options)) return FALSE;

		$result = '';
		foreach ($options as $key => $val) {
			$result .= '<option value="' . $key . '" ';
			if ($selected == $key) {
				$result .=' selected="selected" ';
			}
			$result .=' >' . $val . '</option>';
		}
		return $result;
	}
}

if ( ! function_exists('cut_str'))
{
	/**
	 * 글자자르기
	 */
	function cut_str($str='', $len='', $suffix='…')
	{
		$arr_str = preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
		$str_len = count($arr_str);

		if ($str_len >= $len) {
			$slice_str = array_slice($arr_str, 0, $len);
			$str = join('', $slice_str);
			return $str . ($str_len > $len ? $suffix : '');
		} else {
			$str = join('', $arr_str);
			return $str;
		}
	}
}

if ( ! function_exists('show_alert_message'))
{
	/**
	 * ALERT MESSAGE 가 있을 경우 DIV 로 감싸서 보여주기
	 */
	function show_alert_message($message = '', $html1='', $html2='')
	{
		if ( ! $message) return FALSE;

		$result  = $html1;
		$result .= $message;
		$result .= $html2;
		return $result;
	}
}

if ( ! function_exists('get_skin_name'))
{
	/**
	 * 스킨 디렉토리 검색
	 */
	function get_skin_name($skin_path='', $selected_skin = '', $default_text = '', $dir = VIEW_DIR)
	{

		$result = '';

		if ($dir) $dir = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

		if ( isset($default_text) && $default_text != '') {
			$result .= '<option value="">' . $default_text . '</option>';
		}

			$skin_dir = array();
			$dirname = $dir . $skin_path . '/';

			if ( ! is_dir($dir . $skin_path)) return;

			$handle = opendir($dirname);
			while ($file = readdir($handle)) {
				if ($file == '.' OR $file == '..') continue;

				if (is_dir($dirname . $file))
					$skin_dir[] = $file;
			}
			closedir($handle);
			sort($skin_dir);

			foreach ($skin_dir as $row) {
				$option = $row;
				if (strlen($option) > 10)
					$option = substr($row, 0, 18) . '…';

				$slt = ($selected_skin == $row) ? 'selected="selected"' : '';
				$result .= '<option value="' . $row . '" ' . $slt . '>' . $option . '</option>';
			}

			return $result;

	}
}

if ( ! function_exists('get_access_selectbox'))
{
	/**
	 * 권한관리 페이지에 보이는 셀렉트박스
	 */
	function get_access_selectbox($config='', $memberonly='')
	{
		if ( ! $config) return FALSE;

		$show_level_array = array('3', '4', '5');

		$result = '';
		$result .= '<select name="' . element('column_name', $config) . '" class="form-control" >';

		if ( ! $memberonly) {
			$result .= '<option value=""';
			$result .= element('column_value', $config) == '' ? 'selected="selected"' : '';
			$result .= '>모든 사용자</option>';
		}

		$result .= '<option value="1"';
		$result .= element('column_value', $config) == '1' ? 'selected="selected"' : '';
		$result .= '>로그인 사용자</option>';

		$result .= '<option value="100"';
		$result .= element('column_value', $config) == '100' ? 'selected="selected"' : '';
		$result .= '>관리자</option>';

		$result .= '<option value="3"';
		$result .= element('column_value', $config) == '3' ? 'selected="selected"' : '';
		$result .= '>특정레벨이상인자</option>';

		$result .= '</select>';

		$result .= '<span id="' . element('column_level_name', $config) . '" style="';
		$result .= in_array(element('column_value', $config), $show_level_array) ? 'display:inline;' : 'display:none;';
		$result .= '">';
		$result .= '<select name="' . element('column_level_name', $config) . '" class="form-control">';

		for ($level = 1; $level <= element('max_level', $config) ; $level ++) {
			$result .= '<option value="' . $level . '" ';
			$result .= element('column_level_value', $config) == $level ? 'selected="selected"' : '';
			$result .= ' >' . $level . '</option>';
		}
		$result .= '</select> 레벨 이상인자 </span>';

		$result .= '<script type="text/javascript">';
		$result .= '$(function() {
			$(document).on("change", "select[name=' . element('column_name', $config) . ']", function() {';
				$result .= 'if ($(this).val() == "3") {';
					$result .= '$("#' . element('column_level_name', $config) . '").css("display", "inline");';
				$result .= '} else {';
					$result .= '$("#' . element('column_level_name', $config) . '").css("display", "none");';
				$result .= '}';

			$result .= '})
		});';
		$result .= '</script>';

		return $result;

	}
}

if ( ! function_exists('required_user_login'))
{
	/**
	 * 로그인한 회원만 접근이 가능합니다
	 */
	function required_user_login($type='')
	{
			$CI =& get_instance();
			if ($CI->member->is_member() == FALSE) {
				if ($type == 'alert') {
					alert_close('로그인 후 이용이 가능합니다');
				} else {
					$CI->session->set_flashdata('message', '로그인 후 이용이 가능합니다');
					redirect('login?url=' . $CI->uri->uri_string());
				}
			}
			return TRUE;
	}
}

if ( ! function_exists('display_ipaddress'))
{
	/**
	 * ip 를 정한 형식에 따라 보여주기
	 */
	function display_ipaddress($ip='', $type='0001')
	{
		$len = strlen($type);
		if ($len != 4) return FALSE;

		if ( ! $ip) return FALSE;

		$regex = '';
		$regex .= ($type[0] == '1') ? '\\1' : '&#9825;';
		$regex .= '.';
		$regex .= ($type[1] == '1') ? '\\2' : '&#9825;';
		$regex .= '.';
		$regex .= ($type[2] == '1') ? '\\3' : '&#9825;';
		$regex .= '.';
		$regex .= ($type[3] == '1') ? '\\4' : '&#9825;';
		return preg_replace("/([0-9]+).([0-9]+).([0-9]+).([0-9]+)/", $regex, $ip);
	}
}

if ( ! function_exists('display_admin_ip'))
{
	/**
	 * 관리자에게 보여주는 IP 팝업버튼
	 */
	function display_admin_ip($ip='')
	{
		if ( ! $ip) return FALSE;
		$CI = & get_instance();
		if ($CI->member->is_admin() != 'super') {
			return;
		}
		
		return $ip;
	}
}

if ( ! function_exists('display_username'))
{
	/**
	 * 회원닉네임을 사이드뷰와 함께 출력
	 */
	function display_username($userid = '', $name = '', $icon='', $use_sideview='')
	{
			$CI = & get_instance();
			$name = $name ? html_escape($name) :  '비회원';
			$title = $userid ? '[' . $userid . ']' : '[비회원]';

			$_use_sideview = ($CI->cbconfig->get_device_view_type() == 'mobile') ? $CI->cbconfig->item('use_mobile_sideview') : $CI->cbconfig->item('use_sideview');

			$result = '';
			if ($use_sideview) {
				if ($use_sideview == 'Y' && $userid)
					$result .= '<a href="javascript:;" onClick="getSideView(this, \'' . $userid . '\');" title="' . $title . $name . '" style="text-decoration:none;">';
			} else if ($_use_sideview && $userid) {
				$result .= '<a href="javascript:;" onClick="getSideView(this, \'' . $userid . '\');" title="' . $title . $name . '" style="text-decoration:none;">';
			}
			if ($CI->cbconfig->item('use_member_icon') && $icon) {
				$width = $CI->cbconfig->item('member_icon_width');
				$height = $CI->cbconfig->item('member_icon_height');
				$result .= '<img src="' . member_icon_url($icon) . '" alt="icon" class="member-icon" width="' . $width . '" height="' . $height . '"  /> ';
			}
				$result .= $name;
			if ($use_sideview) {
				if ($use_sideview == 'Y' && $userid)
					$result .= '</a>';
			} else if ($_use_sideview && $userid) {
				$result .= '</a>';
			}
			return $result;

	}
}

if ( ! function_exists('member_photo_url'))
{
	/**
	 * 회원 사진 가져오기
	 */
	function member_photo_url($img='', $width = '', $height = '')
	{
			$CI = & get_instance();
			if ( ! $img) return FALSE;
			$width = ($width && is_numeric($width)) ? $width : $CI->cbconfig->item('member_photo_width');
			$height = ($height && is_numeric($height)) ? $height : $CI->cbconfig->item('member_photo_height');

			return thumb_url('member_photo' , $img , $width , $height);
	}
}

if ( ! function_exists('member_icon_url'))
{
	/**
	 * 회원 아이콘 가져오기
	 */
	function member_icon_url($img='', $width = '', $height = '')
	{
			$CI = & get_instance();
			if ( ! $img) return FALSE;
			$width = ($width && is_numeric($width)) ? $width : $CI->cbconfig->item('member_icon_width');
			$height = ($height && is_numeric($height)) ? $height : $CI->cbconfig->item('member_icon_height');

			return thumb_url('member_icon' , $img , $width , $height);
	}
}

if ( ! function_exists('banner_image_url'))
{
	/**
	 * 배너 이미지 가져오기
	 */
	function banner_image_url($img='', $width = '', $height = '')
	{
			if ( ! $img) return FALSE;
			$width = ($width && is_numeric($width)) ? $width : '';
			$height = ($height && is_numeric($height)) ? $height : '';

			return thumb_url('banner' , $img , $width , $height);
	}
}

if ( ! function_exists('banner'))
{
	/**
	 * 배너 출력하기
	 */
	function banner($position='', $type='rand', $limit = '1', $start_tag='', $end_tag='')
	{
		
		/**
		* 배너 함수 사용법
		* banner('위치명', '배너보여주는방식', '보여줄 배너 개수', '각 배너 시작전 html 태그', '각 배너 끝난후에 html 태그')
		* 
		* type 의 종류
		* rand : 같은 위치에 여러 배너를 올렸을 경우, limit 에서 정한 개수를 랜덤으로 보여줍니다
		* order : 같은 위치에 여러 배너를 올렸을 경우, limit 에서 정한 개수를 order 값(관리자페이지에서 정한값)이 큰 순으로 보여줍니다
		* 
		* limit : 보여줄 배너 개수입니다
		* 
		* start_tag, end_tag : 각 배너의 시작과 끝에 html 태그를 삽입합니다
		* 즉 2개의 배너를 start_tag 와 end_tag 와 함께 사용하면 아래와 같은 태그를 리턴합니다
		* {start_tag}<a href="첫번째배너링크"><img src="첫번재배너이미지"></a>{end_tag}{start_tag}<a href="두번째배너링크"><img src="두번재배너이미지"></a>{end_tag}
		* 
		*/

		$CI = & get_instance();

		if( ! $position) return;
		if( $type != 'order') $type = 'rand';

		$html = '';

		$CI->load->model('Banner_model');
		$result = $CI->Banner_model->get_banner($position, $type, $limit);

		if($result) {
			foreach($result as $key => $val) {
				if ($CI->cbconfig->get_device_view_type() == 'mobile' && element('ban_device', $val) == 'pc') continue;
				if ($CI->cbconfig->get_device_view_type() != 'mobile' && element('ban_device', $val) == 'mobile') continue;
				if(element('ban_image', $val)) {
					
					$html .= $start_tag;
					
					if(element('ban_url', $val)) {
						$html .= '<a href="' . site_url('gotourl/banner/' . element('ban_id', $val)) . '" ';
						if(element('ban_target', $val)) $html .= ' target="_blank" ';
						$html .= '  title="' . html_escape(element('ban_title', $val)) . '"  ';
						$html .= ' >';
					}
					
					$html .= '<img 
										src="' . thumb_url('banner', element('ban_image', $val) , element('ban_width', $val), element('ban_height', $val)) . '" 
										class="cb_banner" 
										id="cb_banner_' . element('ban_id', $val) . '" 
										alt="' . html_escape(element('ban_title', $val)) . '" 
										title="' . html_escape(element('ban_title', $val)) . '" 
									  />';
					if(element('ban_url', $val)) {
						$html .= '</a>';
					}
					$html .= $end_tag;
				}
			}
		}

		return $html;
	}
}

if ( ! function_exists('display_html_content'))
{
	/**
	 * 본문 가져오기
	 */
	function display_html_content($content='', $html='', $thumb_width=700, $autolink=FALSE, $popup=FALSE, $writer_is_admin = FALSE)
	{


		if ( ! $html) {
			$content = nl2br(html_escape($content));
			if ($autolink) {
				$content = url_auto_link($content, $popup);
			}
			$content = preg_replace("/\[<a\s*href\=\"(http|https|ftp)\:\/\/([^[:space:]]+)\.(gif|png|jpg|jpeg|bmp).*<\/a>(\s\]|\]|)/i", "<img src=\"$1://$2.$3\" alt=\"\" style=\"max-width:100%;border:0;\">", $content);
			$content = preg_replace("/{지도\:([^}]*)}/ie", "get_google_map('\\1', '{$thumb_width}')", $content); // Google Map
			return $content;
		}

		$source = array();
		$target = array();

		$source[] = '//';
		$target[] = '';

		$source[] = "/<\?xml:namespace prefix = o ns = \"urn:schemas-microsoft-com:office:office\" \/>/";
		$target[] = '';

		// 테이블 태그의 갯수를 세어 테이블이 깨지지 않도록 한다.
		$table_begin_count = substr_count(strtolower($content), '<table');
		$table_end_count = substr_count(strtolower($content), '</table');
		for ($i=$table_end_count; $i<$table_begin_count; $i++) {
			$content .= '</table>';
		}

		$content = preg_replace($source, $target, $content);
		
		if ($autolink) {
			$content = url_auto_link($content, $popup);
		}
		
		if($writer_is_admin == FALSE) {
			$content = html_purifier($content);
		}

		$content = get_view_thumbnail($content, $thumb_width);

		$content = preg_replace("/{&#51648;&#46020;\:([^}]*)}/ie", "get_google_map('\\1', '{$thumb_width}')", $content); // Google Map
		
		return $content;

	}
}


if ( ! function_exists('html_purifier'))
{
	// http://htmlpurifier.org/
	// Standards-Compliant HTML Filtering
	// Safe  : HTML Purifier defeats XSS with an audited whitelist
	// Clean : HTML Purifier ensures standards-compliant output
	// Open  : HTML Purifier is open-source and highly customizable
	function html_purifier($html)
	{

		$CI = & get_instance();

		$white_iframe = $CI->cbconfig->item('white_iframe');;
		$white_iframe = preg_replace("/[\r|\n|\r\n]+/",",", $white_iframe);
		$white_iframe = preg_replace("/\s+/","", $white_iframe);
		if ($white_iframe) {
			$white_iframe = explode(',',trim($white_iframe, ','));
			$white_iframe = array_unique($white_iframe);
		}
		$domains = array();
		if ($white_iframe) {
			foreach ($white_iframe as $domain) {
				$domain = trim($domain);
				if ($domain)
					array_push($domains, $domain);
			}
		}
		// 내 도메인도 추가
		array_push($domains, $_SERVER['HTTP_HOST'].'/');
		$safeiframe = implode('|', $domains);

		if ( ! defined('INC_HTMLPurifier')) {
			include_once(FCPATH . 'plugin/htmlpurifier/HTMLPurifier.standalone.php');
			define('INC_HTMLPurifier', TRUE);
		}
		$config = HTMLPurifier_Config::createDefault();
		// cache 디렉토리에 CSS, HTML, URI 디렉토리 등을 만든다.
		$config->set('Cache.SerializerPath', APPPATH . '/cache');
		$config->set('HTML.SafeEmbed', true);
		$config->set('HTML.SafeObject', true);
		$config->set('HTML.SafeIframe', true);
		$config->set('URI.SafeIframeRegexp','%^(https?:)?//('.$safeiframe.')%');
		$config->set('Attr.AllowedFrameTargets', array('_blank'));
		$config->set('Core.Encoding', 'utf-8');
		$config->set('Core.EscapeNonASCIICharacters', true);
		$config->set('HTML.MaxImgLength', null); 
		$config->set('CSS.MaxImgLength', null); 
		$purifier = new HTMLPurifier($config);
		return $purifier->purify($html);
	}
}


if ( ! function_exists('url_auto_link'))
{
	/**
	 * URL 자동 링크 생성
	 */
	function url_auto_link($str='', $popup=FALSE)
	{
		if ( ! $str) return FALSE;
		$target = $popup ? 'target="_blank"':'';
		$str = str_replace(array("&lt;", "&gt;", "&amp;", "&quot;", "&nbsp;", "&#039;"), array("\t_lt_\t", "\t_gt_\t", "&", "\"", "\t_nbsp_\t", "'"), $str);
		$str = preg_replace("/([^(href=\"?'?)|(src=\"?'?)]|\(|^)((http|https|ftp|telnet|news|mms):\/\/[a-zA-Z0-9\.-]+\.[가-힣\xA1-\xFEa-zA-Z0-9\.:&#=_\?\/~\+%@;\-\|\,\(\)]+)/i", "\\1<a href=\"\\2\" {$target}>\\2</A>", $str);
		$str = preg_replace("/(^|[\"'\s(])(www\.[^\"'\s()]+)/i", "\\1<a href=\"http://\\2\" {$target}>\\2</A>", $str);
		$str = preg_replace("/[0-9a-z_-]+@[a-z0-9._-]{4,}/i", "<a href=\"mailto:\\0\">\\0</a>", $str);
		$str = str_replace(array("\t_nbsp_\t", "\t_lt_\t", "\t_gt_\t", "'"), array("&nbsp;", "&lt;", "&gt;", "&#039;"), $str);
		return $str;

	}
}


if ( ! function_exists('content_syntaxhighlighter'))
{
	// syntax highlight
	function content_syntaxhighlighter($m)
	{

		$str = $m[3];

		if ( ! $str) return;

		$str = str_replace(array("<br>", "<br/>", "<br />", "<div>", "</div>", "<p>", "</p>", "&nbsp;"), "", $str);
		$target = array("/</", "/>/", "/\"/", "/\'/");
		$source = array("&lt;", "&gt;", "&#034;", "&#039;");

		 $str = preg_replace($target, $source, $str);

		if ( ! $str) return;

		$brush = strtolower(trim($m[2]));
		$brush_arr = array('css', 'js', 'jscript', 'javascript', 'php', 'xml', 'xhtml', 'xslt', 'html');
		$brush = ($brush && in_array($brush, $brush_arr)) ? $brush : 'html';

		return '<pre class="brush: '.$brush.';">'.$str.'</pre>'.PHP_EOL;

	}
}

if ( ! function_exists('content_syntaxhighlighter_html'))
{
	// syntax highlight
	function content_syntaxhighlighter_html($m)
	{

		$str = $m[3];

		if ( ! $str) return;

		$str = str_replace(array("\n\r", "\r"), array("\n"), $str);
		$str = str_replace("\n", "", $str);
		$str = str_replace(array("<br>", "<br/>", "<br />", "<div>", "</div>", "<p>", "</p>", "&nbsp;"), array("\n", "\n", "\n", "\n", "", "\n", "", "\t"), $str);
		$target = array("/<span[^>]+>/i", "/<\/span>/i", "/</", "/>/", "/\"/", "/\'/");
		$source = array("", "", "&lt;", "&gt;", "&#034;", "&#039;");

		 $str = preg_replace($target, $source, $str);

		if ( ! $str) return;

		$brush = strtolower(trim($m[2]));
		$brush_arr = array('css', 'js', 'jscript', 'javascript', 'php', 'xml', 'xhtml', 'xslt', 'html');
		$brush = ($brush && in_array($brush, $brush_arr)) ? $brush : 'html';

		return '<pre class="brush: '.$brush.';">'.$str.'</pre>'.PHP_EOL;

	}
}

if ( ! function_exists('change_key_case'))
{
	function change_key_case($str)
	{

		$str = stripcslashes($str);

		preg_match_all('@(?P<attribute>[^\s\'\"]+)\s*=\s*(\'|\")?(?P<value>[^\s\'\"]+)(\'|\")?@i', $str, $match);
		$value = @array_change_key_case(array_combine($match['attribute'], $match['value']));

		return $value;
	}
}

if ( ! function_exists('get_google_map'))
{
	//Google Map
	function get_google_map($geo_data='', $maxwidth='')
	{

		if ( ! $geo_data) return;

		if ( ! $maxwidth) $maxwidth = 700;

		$geo_data = stripslashes($geo_data);
		$geo_data = str_replace('&quot;', '', $geo_data);

		if ( ! $geo_data) return;

		$map = array();
		$map = change_key_case($geo_data);

		if (isset($map['loc'])) {
			list($lat, $lng) = explode(',', element('loc', $map));
			$zoom = element('z', $map);
		} else {
			list($lat, $lng, $zoom) = explode(',', element('geo', $map));
		}

		if ( ! $lat OR ! $lng) return;

		//Map
		$map['geo'] = $lat.','.$lng.','.$zoom;

		//Marker
		preg_match("/m=\"([^\"]*)\"/ie", $geo_data, $marker);
		$map['m'] = element(1, $marker);


		$google_map = '<div style="width:100%; margin:0 auto 15px; max-width:'.$maxwidth.'px;">'.PHP_EOL;
		$google_map .= '<iframe width="100%" height="480" src="' . site_url('helptool/googlemap?geo='.urlencode($map['geo']).'&marker='.urlencode($map['m'])) . '" frameborder="0" scrolling="no"></iframe>'.PHP_EOL;
		$google_map .= '</div>'.PHP_EOL;
		return $google_map;
	}
}

if ( ! function_exists('get_view_thumbnail'))
{
	// 게시글보기 썸네일 생성
	function get_view_thumbnail($contents='', $thumb_width=0)
	{
		if ( ! $contents) return FALSE;

		$CI = & get_instance();

		if ( ! $thumb_width)
			$thumb_width = 700;

		// $contents 중 img 태그 추출
		$matches = get_editor_image($contents, TRUE);

		if (empty($matches))
			return $contents;

		$end = count(element(1, $matches));
		for ($i=0; $i< $end; $i++) {

			$img = $matches[1][$i];
			preg_match("/src=[\'\"]?([^>\'\"]+[^>\'\"]+)/i", $img, $m);
			$src = isset($m[1]) ? $m[1] : '';
			preg_match("/style=[\"\']?([^\"\'>]+)/i", $img, $m);
			$style = isset($m[1]) ? $m[1] : '';
			preg_match("/width:\s*(\d+)px/", $style, $m);
			$width = isset($m[1]) ? $m[1] : '';
			preg_match("/height:\s*(\d+)px/", $style, $m);
			$height = isset($m[1]) ? $m[1] : '';
			preg_match("/alt=[\"\']?([^\"\']*)[\"\']?/", $img, $m);
			$alt = isset($m[1]) ? html_escape($m[1]) : '';
			if ( ! $width) {
				preg_match("/width=[\"\']?([^\"\']*)[\"\']?/", $img, $m);
				$width = isset($m[1]) ? html_escape($m[1]) : '';
			}
			if ( ! $height) {
				preg_match("/height=[\"\']?([^\"\']*)[\"\']?/", $img, $m);
				$height = isset($m[1]) ? html_escape($m[1]) : '';
			}

			// 이미지 path 구함
			$p = parse_url($src);
			if (isset($p['host']) && $p['host'] == $CI->input->server('HTTP_HOST') && strpos($p['path'], '/uploads/editor/') !== FALSE)
			{
				$thumb_tag = '<img src="' . thumb_url('editor', str_replace( site_url('uploads/editor').'/', '', $src), $thumb_width) . '" ';
			} else {
				$thumb_tag = '<img src="' . $src . '" ';
			}
			if ($width) {
				$thumb_tag .= ' width="' . $width . '" ';
			}
			$thumb_tag .= 'alt="' . $alt . '" style="max-width:100%;"/>';

			$img_tag = $matches[0][$i];
			$contents = str_replace($img_tag, $thumb_tag, $contents);
			if ($width) {
				$thumb_tag .= ' width="' . $width . '" ';
			}
			$thumb_tag .= 'alt="' . $alt . '" style="max-width:100%;"/>';

			$img_tag = $matches[0][$i];
			$contents = str_replace($img_tag, $thumb_tag, $contents);
		}

		return $contents;
	}
}

if ( ! function_exists('get_post_image_url'))
{
	// 에디터 이미지 1개  url 얻기
	function get_post_image_url($contents = '', $thumb_width='', $thumb_height='')
	{
			$CI = & get_instance();
			
			if( ! $contents) return;
			
			$matches = get_editor_image($contents);
			
			if (empty($matches)) return;

			$img = element(0, element(1, $matches));
			if( ! $img) return;
			preg_match("/src=[\'\"]?([^>\'\"]+[^>\'\"]+)/i", $img, $m);
			$src = isset($m[1]) ? $m[1] : '';

			$p = parse_url($src);
			if (isset($p['host']) && $p['host'] == $CI->input->server('HTTP_HOST') && strpos($p['path'], '/uploads/editor/') !== FALSE)
			{
				$src = thumb_url('editor', str_replace( site_url('uploads/editor').'/', '', $src), $thumb_width, $thumb_height);
			} 
			
			return $src;


	}
}

if ( ! function_exists('get_editor_image'))
{
	// 에디터 이미지 얻기
	function get_editor_image($contents = '', $view=TRUE)
	{
		if ( ! $contents)
			return FALSE;

		// $contents 중 img 태그 추출
		if ($view) {
			$pattern = "/<img([^>]*)>/iS";
		} else {
			$pattern = "/<img[^>]*src=[\'\"]?([^>\'\"]+[^>\'\"]+)[\'\"]?[^>]*>/i";
		}
		preg_match_all($pattern, $contents, $matchs);

		return $matchs;
	}
}

if ( ! function_exists('display_datetime'))
{
	// Date & Time
	function display_datetime($datetime='', $type='', $custom='')
	{

		if ( ! $datetime) return FALSE;

		if ($type == 'sns') {

			$diff = ctimestamp() - strtotime($datetime);

			$s = 60; //1분 = 60초
			$h = $s * 60; //1시간 = 60분
			$d = $h * 24; //1일 = 24시간
			$y = $d * 10; //1년 = 1일 * 10일

			if ($diff < $s) {
				$result = $diff . '초전';
			} else if ($h > $diff && $diff >= $s) {
				$result = round($diff/$s) . '분전';
			} else if ($d > $diff && $diff >= $h) {
				$result = round($diff/$h) . '시간전';
			} else if ($y > $diff && $diff >= $d) {
				$result = round($diff/$d) . '일전';
			} else {
				if (substr($datetime,0, 10) == cdate('Y-m-d')) {
					$result = str_replace('-', '.', substr($datetime,11,5));
				} else {
					$result = substr($datetime,5,5);
				}
			}
		} else if ($type == 'user' && $custom) {
			return cdate($custom, strtotime($datetime));
		} else if ($type == 'full') {
			if (substr($datetime,0, 10) == cdate('Y-m-d')) {
				$result = substr($datetime,11,5);
			} else if (substr($datetime,0, 4) == cdate('Y')) {
				$result = substr($datetime,5,11);
			} else {
				$result = substr($datetime,0,10);
			}
		} else {
			if (substr($datetime,0, 10) == cdate('Y-m-d')) {
				$result = substr($datetime,11,5);
			} else {
				$result = substr($datetime,5,5);
			}
		}

		return $result;
	}
}

if ( ! function_exists('get_extension'))
{
	// 확장자 얻기
	function get_extension($filename)
	{

		$file = explode(".", basename($filename));
		$count = count($file);
		if ($count > 1) {
				return strtolower($file[$count-1]);
		} else {
				return '';
		}
	}
}


// get_sock 함수 대체
if ( !function_exists("get_sock")) {
    function get_sock($url)
    {
        // host 와 uri 를 분리
        //if (ereg("http://([a-zA-Z0-9_\-\.]+)([^<]*)", $url, $res))
        $host = '';
		$get  = '';

		if (preg_match("/http:\/\/([a-zA-Z0-9_\-\.]+)([^<]*)/", $url, $res))
        {
            $host = $res[1];
            $get  = $res[2];
        }

        // 80번 포트로 소캣접속 시도
        $fp = fsockopen ($host, 80, $errno, $errstr, 30);
        if ( ! $fp) {
            die($errstr . ' (' . $errno . ")\n");
        } else {
            fputs($fp, "GET $get HTTP/1.0\r\n");
            fputs($fp, "Host: $host\r\n");
            fputs($fp, "\r\n");

			$header = '';
            // header 와 content 를 분리한다.
            while (trim($buffer = fgets($fp,1024)) != "")
            {
                $header .= $buffer;
            }
            while ( !feof($fp))
            {
                $buffer .= fgets($fp,1024);
            }
        }
        fclose($fp);

        // content 만 return 한다.
        return $buffer;
    }
}

if ( ! function_exists('get_phone')) {
    function get_phone($phone, $hyphen=1)
    {
        if ( !is_phone($phone)) return '';

        if ($hyphen) $preg = "$1-$2-$3"; else $preg = "$1$2$3";

        $phone = str_replace('-', '', trim($phone));
        $phone = preg_replace("/^(01[016789])([0-9]{3,4})([0-9]{4})$/", $preg, $phone);

        return $phone;
    }
}
if ( ! function_exists('is_phone')) {
    function is_phone($phone)
    {
        $phone = str_replace('-', '', trim($phone));
        if (preg_match("/^(01[016789])([0-9]{3,4})([0-9]{4})$/", $phone))
            return true;
        else
            return false;
    }
}
if ( !function_exists('json_encode')) {
    function json_encode($data)
	{
		$CI = & get_instance();
		$CI->load->library('Services_json');
        $json = new Services_JSON();
        return($json->encode($data));
    }
}

if ( !function_exists('json_decode')) {
	function json_decode($data, $output_mode=false)
	{
		$CI = & get_instance();
		$CI->load->library('Services_json');
        $param = $output_mode ? 16:null;
        $json = new Services_JSON($param);
        return($json->decode($data));
    }
}

if ( !function_exists('admin_listnum')) {
	function admin_listnum()
	{
		$CI = & get_instance();
		if ($CI->input->get('listnum') && is_numeric($CI->input->get('listnum')) && $CI->input->get('listnum') > 0 && $CI->input->get('listnum') <= 1000) {
			$listnum = $CI->input->get('listnum');
			$cookie_name = 'admin_listnum';
			$cookie_value = $listnum;
			$cookie_expire = 8640000;
			set_cookie($cookie_name, $cookie_value, $cookie_expire);
		} else {
			$listnum = get_cookie('admin_listnum') && is_numeric(get_cookie('admin_listnum')) ? get_cookie('admin_listnum') : '20';
		}
		return $listnum;

	}
}


if ( !function_exists('admin_listnum_selectbox')) {
	function admin_listnum_selectbox()
	{
		$CI = & get_instance();
		if ($CI->input->get('listnum') && is_numeric($CI->input->get('listnum')) && $CI->input->get('listnum') > 0 && $CI->input->get('listnum') <= 1000) {
			$listnum = $CI->input->get('listnum');
		} else {
			$listnum = get_cookie('admin_listnum') ? get_cookie('admin_listnum') : '20';
		}
		$array = array('10', '15', '20', '25', '30', '40', '50', '60', '70', '100' );

		$html = '<select name="listnum" class="form-control"  onchange="location.href=\'' . current_url() . '?listnum=\' + this.value;">';
		$html .= '<option value="">선택</option>';

		foreach ($array as $val) { 
			$html .= '<option value="' . $val . '" ';
			$html .= ($listnum == $val) ? ' selected="selected" ' : '';
			$html .= ' >' . $val . '</option>';
		}
		$html .= '</select>개씩 보기';

		return $html;

	}
}


// http://kr1.php.net/manual/en/function.curl-setopt-array.php 참고
if (!function_exists('curl_setopt_array')) {
   function curl_setopt_array(&$ch, $curl_options)
   {
       foreach ($curl_options as $option => $value) {
           if (!curl_setopt($ch, $option, $value)) {
               return false;
           } 
       }
       return true;
   }
}


// Browscap 정보 얻기
if (!function_exists('get_useragent_info')) {
	function get_useragent_info($useragent = '')
	{
		global $_browscap;

		if( ! $useragent)
			return FALSE;

		$result = array();

		if(config_item('user_agent_parser') == 'browscap' && is_file(FCPATH . 'plugin/browscap/browscap_cache.php')) {
			
			if( ! defined('CONSTANT_GET_USERAGENT_INFO')) {
				ini_set('memory_limit', '-1');
				require_once FCPATH . 'plugin/browscap/Browscap.php';
				$_browscap = new Browscap(FCPATH . 'plugin/browscap');
				$_browscap->updateMethod = 'cURL';
				$_browscap->doAutoUpdate = false;
				$_browscap->cacheFilename = 'browscap_cache.php';
			}
			$cap = $_browscap->getBrowser($useragent);
			$result['browsername'] = $cap->Browser;
			$result['browserversion'] = $cap->Version;
			$result['os'] = $cap->Platform;
			$result['engine'] = '';

		} else {
			
			if( ! defined('CONSTANT_GET_USERAGENT_INFO')) {
				$CI = & get_instance();
				$CI->load->library(array('phpuseragentstringparser' , 'phpuseragent'));
			}
			$userAgent = new phpUserAgent( $useragent );
			$result['browsername'] = $userAgent->getBrowserName();
			$result['browserversion'] = $userAgent->getBrowserVersion();
			$result['os'] = $userAgent->getOperatingSystem();
			$result['engine'] = $userAgent->getEngine();

		}
		defined('CONSTANT_GET_USERAGENT_INFO') OR define('CONSTANT_GET_USERAGENT_INFO', 1);

		return $result;

	}
}