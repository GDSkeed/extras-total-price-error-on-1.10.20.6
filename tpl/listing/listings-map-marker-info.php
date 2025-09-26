<?php
/*

NOTE:
This template is used as a "pattern" for dynamic rendering: no data or objects are used here.

Important class names is:
	.iwc-link
	.img
	.title
	.description
	.text
	.price-box

*/

if (!defined('WPINC')) die;
?>

<div class="info-window-content">
	<a class="iwc-link" href="#">
		<div class="img"><img src="<?= HOSTIFYBOOKING_URL ?>public/res/images/loading.svg" /></div>
		<div class="info">
			<div class="title"></div>
			<div class="description">
				<span class='text'></span>
				<span class='price-box'></span>
			</div>
		</div>
	</a>
</div>
