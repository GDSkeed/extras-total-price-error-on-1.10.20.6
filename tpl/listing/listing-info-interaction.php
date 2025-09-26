<?php if (!defined('WPINC')) die; ?>

<?php if (!empty($listingDescription->interaction)) : ?>

	<div class="hfy-listing-info-interaction">
		<?= nl2br($listingDescription->interaction); ?>
	</div>

<?php endif; ?>
