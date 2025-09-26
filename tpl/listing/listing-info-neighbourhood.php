<?php if (!defined('WPINC')) die; ?>

<?php if (!empty($listingDescription->neighborhood_overview)) : ?>

	<div class="hfy-listing-info-neighbourhood">
		<?= nl2br($listingDescription->neighborhood_overview); ?>
	</div>

<?php endif; ?>
