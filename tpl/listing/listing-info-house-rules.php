<?php if (!defined('WPINC')) die; ?>

<?php if (!empty($listingDescription->house_rules)) : ?>

	<div class="hfy-listing-info-house-rules">
		<?= nl2br($listingDescription->house_rules); ?>
	</div>

<?php endif; ?>
