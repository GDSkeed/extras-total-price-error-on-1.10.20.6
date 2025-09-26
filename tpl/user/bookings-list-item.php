<?php
/**
 * $listing = reservation object
 */

$listingUrl = '#';
$listingUrl_target = '';
if (($listing->service_pms) ?? 0 == 1) {
	$listingUrl = UrlHelper::listing(['id' => $listing->fs_listing_id]);
	$listingUrl_target = '_blank';
}

?>

<div class='row my-booking-item'>
	<div class='col-md-4 my-booking-item-img'>
		<a href='<?= $listingUrl ?>' target=<?= $listingUrl_target ?>>
			<img alt='' src='<?= esc_attr($listing->thumbnail_file) ?>' />
		</a>
	</div>
	<div class='col-md-8 my-booking-item-desc'>
		<h4>
			<b class="label label-primary"><?= $listing->status_description ?></b>
			&nbsp;
			<a href='<?= $listingUrl ?>' target=<?= $listingUrl_target ?>>
				<?= $listing->name ?>
			</a>
		</h4>

		<p>
			<?= $listing->address ?>
		</p>

		<hr/>

		<p>
			<?= __('Arrival:', 'hostifybooking') ?>
			<?= date_format(date_create($listing->checkIn), "M d, Y") ?>
			&nbsp;
			&nbsp;
			<?= __('Departure:', 'hostifybooking') ?>
			<?= date_format(date_create($listing->checkOut), "M d, Y") ?>
		</p>

		<p>
			<?= __('Confirmed at:', 'hostifybooking') ?>
			<?= $listing->confirmed_at ?>
		</p>
		<p>
			<?= __('Code:', 'hostifybooking') ?>
			<code><?= $listing->confirmation_code ?></code>
		</p>

		<p class='booking-item-side'>
			<?= __('Total:', 'hostifybooking') ?>
			<b><?= ListingHelper::withSymbol($listing->pricingData->total ?? $listing->total_price, $listing) ?></b>
		</p>

		<p class='booking-manage-link'>
			<a class='btn btn-outline-primary' href='<?= HFY_PAGE_BOOKING_MANAGE_URL ?>?<?= http_build_query(['rid' => $listing->id]) ?>'
			><?= __('Details', 'hostifybooking') ?></a>
		</p>

	</div>
</div>

<hr/>
