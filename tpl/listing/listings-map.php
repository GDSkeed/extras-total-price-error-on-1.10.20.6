<?php if (!defined('WPINC')) die; ?>

<?php // if (!empty($listings)) : ?>
	<div class="hfy-map-wrapper">
		<div class="hfy-listing-map"></div>

		<?php if ($closebutton) : ?>
			<div class="hfy-map-close-btn hfy-ctrl-hide-map"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 21L21 3" stroke="#444444" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M21 21L3 3" stroke="#444444" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
		<?php endif; ?>

	</div>
<?php // endif; ?>
