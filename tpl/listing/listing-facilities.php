<?php if (!defined('WPINC')) die; ?>

<div class="hfy-listing-hotel-facilities">
	<div>
		<img src="<?= HOSTIFYBOOKING_URL ?>public/res/images/room_type.png" />
		<?= ListingHelper::getRoomType($listingData->room_type); ?>
	</div>
	<div>
		<img src="<?= HOSTIFYBOOKING_URL ?>public/res/images/guest_count.png" />
		<?= $listingDetails->person_capacity; ?>
		<?= __( 'guests', 'hostifybooking' ) ?>
	</div>
	<div>
		<img src="<?= HOSTIFYBOOKING_URL ?>public/res/images/bedroom_count.png" />
		<?php if ($listingDetails->bedrooms < 1): ?>
			<?= __( 'Studio', 'hostifybooking' ) ?>
		<?php else: ?>
			<?= $listingDetails->bedrooms ?> <?= __( 'bedrooms', 'hostifybooking' ) ?>
		<?php endif; ?>
	</div>
	<div>
		<img src="<?= HOSTIFYBOOKING_URL ?>public/res/images/bed_count.png" />
		<?= $listingDetails->beds; ?>
		<?= __( 'beds', 'hostifybooking' ) ?>
	</div>
	<?php if (intval($listingDetails->bathrooms) > 0) : ?>
		<div>
			<img src="<?= HOSTIFYBOOKING_URL ?>public/res/images/bath_count.png" />
			<?= $listingDetails->bathrooms; ?>
			<?= ($listingDetails->bathroom_shared ?? 0) == 1
				? __( 'shared baths', 'hostifybooking' )
				: __( 'baths', 'hostifybooking' )
			?>
		</div>
	<?php endif; ?>
</div>
