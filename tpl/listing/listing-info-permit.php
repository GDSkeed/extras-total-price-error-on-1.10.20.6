<?php if (!defined('WPINC')) die; ?>

<?php if (!empty($listingData->permit_or_tax_id)): ?>
	<div class="hfy-listing-info-permit">
		<?= __('Permit / Tax ID', 'hostifybooking') ?>: <?= $listingData->permit_or_tax_id; ?>
	</div>
<?php endif; ?>

