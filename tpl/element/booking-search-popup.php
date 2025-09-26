<?php if (!defined('WPINC')) die;

$prop = $_GET['prop'] ?? [];
$guests = (int) ($_GET['guests'] ?? 1);
$amenitiesSelected = $_GET['am'] ?? [];
$bedrooms = isset($_GET['bedrooms']) ? (is_numeric($_GET['bedrooms']) ? $_GET['bedrooms'] : '') : '';
$bathrooms = (int) ($_GET['bathrooms'] ?? 0);
$priceMin = $_GET['price_min'] ?? $_GET['pmin'] ?? false;
$priceMin = $priceMin <= 0 ? 0 : $priceMin;
$priceMax = $_GET['price_max'] ?? $_GET['pmax'] ?? false;
$cat = $_GET['cat'] ?? [];
$hr = $_GET['hr'] ?? [];

$city_id = $prm->city_id;

$price_min = $settings->price_min_day ?? 0;
$price_max = $settings->price_max_day ?? 10000;

$amenitiesToShow = [
	46 => 'Air conditioning',
	55 => 'Pool',
	39 => 'Balcony or pation',
	59 => 'Garden or backyard',
	60 => 'BBQ grill',
	47 => 'Parking',
	1 => 'TV',
	5 => 'Washer',
	38 => 'Dishwasher',
	24 => 'Microwave',
	44 => 'Coffee maker',
	106 => 'Jacuzzi',
	54 => 'Indoor fireplace',
	20 => 'Crib',
	16 => 'Home office friendly',
	105 => 'Wheelchair accessible',
	71 => 'Beachfront',
	75 => 'Waterfront',
];

?>

<div style="display:none">
	<div class="advanced-search-modal" role="dialog" tabindex="-1">

		<div class="hfy-wrap">
            <div class="advanced-search-modal-content hfy-search-form-wrap-popup">

				<form action="<?php echo UrlHelper::listings(); ?>" method="get" target="_top">

					<?php
					$bathrooms = intval($_GET['bathrooms'] ?? 0);
					?>

					<div class="hfy-search-form-row-advanced">

						<div class='lineb'>
							<div class='a-s-title fh'><?= __('Bedrooms', 'hostifybooking') ?></div>
							<select name="bedrooms" class="form-control custom-search-ctrl">
								<option value="" selected><?= __('Select') ?></option>
								<?php if (HFY_SHOW_STUDIO_OPTION): ?>
									<option value="0" <?= $bedrooms === '0' ? 'selected' : '' ?>><?= __('Studio') ?></option>
								<?php endif; ?>
								<option value="1" <?= $bedrooms == 1 ? 'selected' : '' ?>>1</option>
								<option value="2" <?= $bedrooms == 2 ? 'selected' : '' ?>>2</option>
								<option value="3" <?= $bedrooms == 3 ? 'selected' : '' ?>>3</option>
								<option value="4" <?= $bedrooms == 4 ? 'selected' : '' ?>>4</option>
							</select>
						</div>

						<div class='lineb'>
							<div class='a-s-title fh'><?= __('Bathrooms', 'hostifybooking') ?></div>
							<select name="bathrooms" class="form-control custom-search-ctrl">
								<option value="" disabled selected><?= __('Select', 'hostifybooking') ?></option>
								<option value="1" <?= $bathrooms == 1 ? 'selected' : '' ?>>1</option>
								<option value="2" <?= $bathrooms == 2 ? 'selected' : '' ?>>2</option>
								<option value="3" <?= $bathrooms == 3 ? 'selected' : '' ?>>3</option>
								<option value="4" <?= $bathrooms == 4 ? 'selected' : '' ?>>4</option>
							</select>
						</div>

						<div class='lineb'>
							<div class='a-s-title'><?= __('Property Type', 'hostifybooking') ?></div>
							<div class="a-s-container">
								<div class="col-list">
									<?php
									$num = 0;
									foreach ($dictionaryPropertyType as $propId => $propName) :
										$ihide = ++$num > 4;
										?>
										<label <?= $ihide ? 'class="hidden"' : '' ?>>
											<input type='checkbox' name='prop[]' value='<?= $propId ?>' <?= in_array($propId, $prm->prop) ? 'checked' : '' ?> />
											<span><?= $propName ?></span>
										</label>
									<?php endforeach; ?>
								</div>
								<?php if ($ihide): ?>
									<div class="show-more"><?= __('Show more', 'hostifybooking') ?></div>
								<?php endif; ?>
							</div>
						</div>

						<div class='lineb'>
							<div class='a-s-title'><?= __('Amenities', 'hostifybooking') ?></div>
							<div class="a-s-container">
								<div>
									<div class='col-list'>
										<?php foreach ($amenitiesToShow as $key => $value) : ?>
											<label>
												<input type='checkbox' name='am[]' value='<?= $key ?>' <?= in_array($key, $prm->am) ? 'checked' : '' ?> />
												<span><?= __($value, 'hostifybooking') ?></span>
											</label>
										<?php endforeach; ?>
									</div>
								</div>
							</div>
						</div>

						<div class='lineb adv-price-row'>
							<div class='a-s-title'><?= __('Price', 'hostifybooking') ?></div>
							<div class="a-s-container">
								<div class="prices-slider">
									<input type="text" class="js-range-slider" value=""
										data-type="double"
										data-skin="round"
										data-step="10"
										data-from="<?= intval($priceMin) > 0 ? $priceMin : $price_min ?>"
										data-to="<?= intval($priceMax) > 0 ? $priceMax : $price_max ?>"
										data-min="<?= $price_min ?>"
										data-max="<?= $price_max ?>"
									/>
									<input type="hidden" name="pmin" value='<?= intval($priceMin) > 0 ? $priceMin : $price_min ?>' />
									<input type="hidden" name="pmax" value='<?= intval($priceMax) > 0 ? $priceMax : '' ?>' />
								</div>
							</div>
						</div>

						<div class='lineb no-border'>
							<div class='a-s-title'><?= __('House rules', 'hostifybooking') ?></div>
							<div class="a-s-container">
								<div class='col-list'>
									<label>
										<input type='checkbox' name='hr[]' value='1' <?= in_array(1, $hr) ? 'checked' : '' ?> />
										<span><?= __('Pets allowed', 'hostifybooking') ?></span>
									</label>
									<label>
										<input type='checkbox' name='hr[]' value='2' <?= in_array(2, $hr) ? 'checked' : '' ?> />
										<span><?= __('Events allowed', 'hostifybooking') ?></span>
									</label>
									<label>
										<input type='checkbox' name='hr[]' value='3' <?= in_array(3, $hr) ? 'checked' : '' ?> />
										<span><?= __('Smoking allowed', 'hostifybooking') ?></span>
									</label>
								</div>
							</div>
						</div>

						<div class='adv-controls-row'>
							<div>
								<button class="btn btn-outline-secondary btn-reset" type="button"><?= __('Clear all', 'hostifybooking') ?></button>
							</div>
							<div class='text-right'>
								<button class="btn btn-outline-secondary btn-close" type="button" onclick="jQuery('.advanced-search-modal.modal .close-modal').click()"><?= __('Close', 'hostifybooking') ?></button>
								<button class="btn btn-primary add-white-preloader-by-click" type="submit"><?= __('Search', 'hostifybooking') ?></button>
							</div>
						</div>

					</div>

				</form>

			</div>
		</div>

	</div>
</div>
