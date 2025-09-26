<?php if (!defined('WPINC')) die;

$bathrooms = intval($_GET['bathrooms'] ?? 0);

$showAdv = !empty($prm->prop) || !empty($prm->am);

if (($longTermMode ?? 0) == 1) {
    $price_min = $settings->price_min_month ?? 0;
    $price_max = $settings->price_max_month ?? 100000;
    $price_postfix = __(' / month', 'hostifybooking');
} else {
    $price_min = $settings->price_min_day ?? 0;
    $price_max = $settings->price_max_day ?? 10000;
    $price_postfix = __(' / day', 'hostifybooking');
}

?>

<div class="hfy-search-form-row-advanced" <?= $showAdv ? '' : "style='display:none'" ?>>
	<?php /* ?>
	<div>
		<div>Price</div>
		<div class='row'>
			<div class='col'>
				<input class="form-control" name="pmin" placeholder="Min" value='<?= intval($prm->pmin) ?>' />
			</div>
			<div class='col'>
				<input class="form-control" name="pmax" placeholder="Max" value='<?= intval($prm->pmax) ?>' />
			</div>
		</div>
	</div>
	<?php */ ?>

	<?php /* ?>
	<div>
		<div><?= __('Properties', 'hostifybooking') ?></div>
		<div class="toggle-more">
			<span class="toggle-more-container">
				<?php foreach ($dictionaryPropertyType as $propId => $propName): ?>
					<label><input type='checkbox' name='prop[]' value='<?= $propId ?>' <?= in_array($propId, $prm->prop) ? 'checked' : '' ?> /><?= $propName ?></label>
  				<?php endforeach; ?>
			</span>
			<span class="toggle-more-btn"><?= __('More', 'hostifybooking') ?>&nbsp;&#8942;</span>
		</div>
	</div>
	<?php */ ?>

	<?php if (HFY_SHOW_PETS && HFY_ADV_SEARCH_PETS): ?>
		<div>
			<div class='adv-search-item-title'><?= __('Pets', 'hostifybooking') ?></div>
			<select name="pets" class="form-control custom-search-ctrl">
				<option value="0" selected>
					<?= $pets > 0 ? __('Does not matter', 'hostifybooking') : __('Select', 'hostifybooking') ?>
				</option>
				<?php for ($i = 1; $i <= (HFY_SHOW_PETS_MAX <= 0 ? 1 : HFY_SHOW_PETS_MAX); $i++): ?>
					<option value="<?= $i ?>" <?= (isset($pets) ? $pets : 0) == $i ? 'selected' : '' ?>><?= $i ?></option>
				<?php endfor; ?>
			</select>
		</div>
	<?php endif; ?>

	<div>
		<div class='adv-search-item-title'><?= __('Bathrooms', 'hostifybooking') ?></div>
		<select name="bathrooms" class="form-control custom-search-ctrl">
			<option value="" disabled selected><?= __('Select', 'hostifybooking') ?></option>
			<option value="1" <?= $bathrooms == 1 ? 'selected' : '' ?>>1</option>
			<option value="2" <?= $bathrooms == 2 ? 'selected' : '' ?>>2</option>
			<option value="3" <?= $bathrooms == 3 ? 'selected' : '' ?>>3</option>
			<option value="4" <?= $bathrooms == 4 ? 'selected' : '' ?>>4</option>
		</select>
	</div>

	<div>
		<div class='adv-search-item-title'><?= __('Amenities', 'hostifybooking') ?></div>
		<div class="toggle-more">
			<span class="toggle-more-container">
				<?php if (HFY_ADV_SEARCH_AM_GROUPS): ?>
					<?php foreach ($dictionaryAmenities as $group => $ams): ?>
						<div class='am-title'><?= $group ?></div>
						<div>
							<span class='am-container'>
								<?php foreach ($ams as $amId => $amName): ?>
									<label><input type='checkbox' name='am[]' value='<?= $amId ?>' <?= in_array($amId, $prm->am) ? 'checked' : '' ?> /><?= $amName ?></label>
								<?php endforeach; ?>
							</span>
						</div>
					<?php endforeach; ?>
				<?php else: ?>
					<div>
						<span class='am-container'>
							<?php foreach ($dictionaryAmenities as $group => $ams): ?>
								<?php foreach ($ams as $amId => $amName): ?>
									<label><input type='checkbox' name='am[]' value='<?= $amId ?>' <?= in_array($amId, $prm->am) ? 'checked' : '' ?> /><?= $amName ?></label>
								<?php endforeach; ?>
							<?php endforeach; ?>
						</span>
					</div>
				<?php endif; ?>
			</span>
		</div>
	</div>

	<div>
		<div></div>
		<div class='text-right'>
			<button class="btn btn-secondary btn-reset" type="submit"><?= __('Reset', 'hostifybooking') ?></button>
			<button class="btn btn-primary" type="submit"><?= __('Apply filters', 'hostifybooking') ?></button>
		</div>
	</div>
</div>
