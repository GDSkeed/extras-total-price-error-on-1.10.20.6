<?php
if (!defined('WPINC')) die;
?>

<div class='sort-controls-wrap'>
	<span><?= __('Sort by:', 'hostifybooking') ?></span>
	<select name='sort'>
		<option value='3' <?= $by == 3 ? 'selected' : '' ?>><?= __('Alphabetical', 'hostifybooking') ?> </option>
		<option value='2' <?= $by == 2 ? 'selected' : '' ?>><?= __('Price ascending', 'hostifybooking') ?> </option>
		<option value='1' <?= $by == 1 ? 'selected' : '' ?>><?= __('Price descending', 'hostifybooking') ?></option>
	</select>
</div>
