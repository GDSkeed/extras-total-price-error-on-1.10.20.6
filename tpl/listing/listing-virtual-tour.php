<?php if (!defined('WPINC')) die; ?>

<?php if (!empty($listingData->virtual_tour) && filter_var($listingData->virtual_tour, FILTER_VALIDATE_URL) !== FALSE): ?>
	<h4><?= __( 'Virtual tour', 'hostifybooking' ) ?></h4>
	<iframe src="<?= esc_attr($listingData->virtual_tour) ?>&play=1" width="100%" height='480' frameborder="0" allowfullscreen allow="xr-spatial-tracking" autofocus="true" style="z-index:999999"></iframe>
<?php endif; ?>
