<?php if (!defined('WPINC')) die; ?>

<?php if (!empty($listingDescription->transit)) : ?>

	<div class="hfy-listing-info-transit">
		<?= nl2br($listingDescription->transit); ?>
	</div>

<?php endif; ?>
