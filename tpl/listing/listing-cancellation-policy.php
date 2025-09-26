<?php if (!defined('WPINC')) die; ?>

<div class="hfy-listing-cancellation-policy">
	<?php if ($cancellationPolicy->penalties ?? null) : ?>
		<ul>
			<?php foreach ($cancellationPolicy->penalties as $item) : ?>
				<li><?= empty($item->description) ? ($item->name ?? '') : $item->description ?></li>
			<?php endforeach; ?>
		</ul>
	<?php else: ?>
		<?= $cancellationPolicy->name ?? '' ?>
	<?php endif; ?>
</div>

<?php
/*
$cancellationPolicy OBJECT EXAMPLE:

'grace_hours' => null
'penalties' => array
	0 => object
		'penalty_percent' => int 30
		'name' => string '30% Non-refundable'
		'description' => string 'Charge 30% if cancelled anytime after the booking'
		'deadline_days' => null
		'penalty_nights' => null
		'deadline_hours' => null
	1 => object
		'penalty_percent' => int 70
		'name' => string '70% after 60 days'
		'description' => string 'Charge 70% if cancelled within 60 days before arrival'
		'deadline_days' => int 60
		'penalty_nights' => null
		'deadline_hours' => int 1440
'name' => string '30% Non-refundable, 70% after 60 days before arrival'
'id' => int 1000
'days_before_checkin' => null
'type' => string 'non-refundable'

*/
?>