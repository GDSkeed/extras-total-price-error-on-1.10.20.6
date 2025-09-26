<?php // DEPRECATED since v.1.8.1 ?>

<?php if (!defined('WPINC')) die; ?>

<div class="info-window-content-wrap" style='display:none'>
	<div class="info-window-content">
		<a href="<?= UrlHelper::listing($listingUrl); ?>">

			<div class="img">
				<img src="<?= $listing->thumbnail_file; ?>" alt="" />
			</div>

			<div class="info">
				<div class="title">
					<?php if (isset($type) && $type === 'top') : ?>
						<p class="l-description"><?= $listing->name; ?></p>
					<?php else : ?>
						<?= $listing->name; ?>
					<?php endif; ?>

				</div>

				<div class="description">
					<span class='price-box'><?= ListingHelper::formatPrice($price, $listing) ?></span>

					<?php
					// if ($showReviews && intval($reviewsRating) > 0)
					// todo - listing short
					?>
				</div>
			</div>
		</a>
	</div>
</div>
