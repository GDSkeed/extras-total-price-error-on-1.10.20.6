<?php
$star = '<polygon points="8 11.6000001 3.29771798 14.472136 4.57619641 9.11246122 0.39154787 5.52786405 5.88397301 5.0875387 8 0 10.116027 5.0875387 15.6084521 5.52786405 11.4238036 9.11246122 12.702282 14.472136"></polygon>';
$star5 = "<g>$star</g><g transform='translate(20)'>$star</g><g transform='translate(40)'>$star</g><g transform='translate(60)'>$star</g><g transform='translate(80)'>$star</g>";
?>

<span class="hfy-listing-reviews-stars">
	<div class="d-sm-block stars-main">
		<span>
			<svg width="100" height="18" viewBox="0 0 100 16"><g stroke="none" fill="#E4E5E6" stroke-width="0"><?= $star5 ?></g></svg>
			<span style="height:20px;width:<?= $reviewsRating ?>px">
				<svg width="100" height="18" viewBox="0 0 100 16"><g stroke="none" fill="<?= $settings->custom_color; ?>" stroke-width="0"><?= $star5 ?></g></svg>
			</span>
		</span>
	</div>
</span>
