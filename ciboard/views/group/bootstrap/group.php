<?php
$k = 0;
$is_open = FALSE;
if (element('board_list', $view))
{
	foreach (element('board_list', $view) as $key => $board)
	{
		$config = array(
			'skin' => 'bootstrap',
			'brd_id' => element('brd_id', $board),
			'limit' => 5,
			'length' => 25,
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
if($is_open) {
	echo '</div>';
	$is_open = FALSE;
}
