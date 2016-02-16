<?php
$k = 0;
$is_open = FALSE;

if (element('board_list', $view))
{
	foreach (element('board_list', $view) as $key => $board)
	{
		// 15.12.30 smpark 메인화면 커스터마이징 
		if(element('brd_key', $board) == "gallery"){
			echo '<div class="row">';
			        $config = array(
			            'skin' => 'bootstrap_gallery',
			            'brd_key' => 'gallery',
			            'limit' => 4,
			            'length' => 80,
			            'is_gallery' => '1',
			            'image_width' => '200',
			            'image_height' => '110',
			            'cache_minute' => 1,
			        );
			        echo $this->board->latest($config);
			echo '</div>';
		}else{ // origin source
			$config = array(
			'skin' => 'bootstrap',
			'brd_key' => element('brd_key', $board),
			'limit' => 5,
			'length' => 40,
			'is_gallery' => '',
			'image_width' => '',
			'image_height' => '',
			'cache_minute' => 1,
		);
		if($k % 2 == 0) {
			echo '<div class="row">';
			$is_open = TRUE;
		}
		echo $this->board->latest($config);
		if($k % 2 == 1) {
			echo '</div>';
			$is_open = FALSE;
		}
		$k++;
		}		
	}
}
if($is_open) {
	echo '</div>';
	$is_open = FALSE;
}