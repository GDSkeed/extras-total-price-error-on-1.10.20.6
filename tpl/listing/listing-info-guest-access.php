<?php if (!defined('WPINC')) die; ?>

<?php if (!empty($listingDescription->access)) : ?>

	<div class="hfy-listing-info-guest-access">
		<?= nl2br($listingDescription->access); ?>
	</div>

<?php endif; ?>
