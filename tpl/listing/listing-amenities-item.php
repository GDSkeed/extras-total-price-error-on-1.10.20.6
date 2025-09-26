<?php if (!defined('WPINC')) die; ?>

<span class='hfy-am hfy-am-<?= $amenity->id; ?> <?= $hidden ? 'hidden' : '' ?>'>
	<?php if (!empty($imgurl)) { ?>
		<img src='<?= $imgurl ?>' alt="<?= esc_attr(__( $amenity->name, 'hostifybooking' )) ?>" />
	<?php } ?>
	<?= __( $amenity->name, 'hostifybooking' ) ?>
</span>
