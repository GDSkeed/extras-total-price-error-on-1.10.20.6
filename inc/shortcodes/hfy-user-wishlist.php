<?php

if (!defined('WPINC')) die;

if (is_user_logged_in()) :

	$ids = array_map('intval', explode(',', get_user_meta(get_current_user_id(), '_wishlist', true)));
	?>
	<div class='user-wishlist'>
		<?= do_shortcode('[hfy_listings_selected ids="' . implode(',', $ids) . '" template="user/wishlist"]'); ?>
	</div>
	<?php

else:

	include hfy_tpl('user/error-please-login');

endif;
