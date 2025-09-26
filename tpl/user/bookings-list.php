<?php
/**
 * $type = passed parameter: current|future|past|cancelled|''
 * $bookings_type = determined type for render: current|future|past|cancelled
 * $listings = array
 */

$title = '';
if (empty($type)) {
	switch ($bookings_type) {
		case 'current':
			$title = __('Сurrent', 'hostifybooking'); break;
		case 'future':
			$title = __('Future', 'hostifybooking'); break;
		case 'past':
			$title = __('Past', 'hostifybooking'); break;
		case 'cancelled':
			$title = __('Cancelled', 'hostifybooking'); break;
	}
}

?>

<div class="my-booking-list">

	<?php if (!empty($title)): ?>
		<h3><?= $title ?></h3>
	<?php endif; ?>

	<?php if (empty($listings)): ?>
		<?= __('Nothing yet', 'hostifybooking') ?>

	<?php else: ?>
		<?php
		foreach ($listings as $listing):
			include hfy_tpl('user/bookings-list-item');
		endforeach;
		?>
	<?php endif; ?>

</div>
