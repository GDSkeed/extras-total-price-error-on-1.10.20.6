<?php
if (!defined('WPINC')) die;
?>

<div class="input-group number-input guests-input guests-count-num-wrap">
	<span class='guests-input-label ico-guest'><span class='guests-count-num'><?= (int) $guests ?></span></span>
</div>

<div class="select-guests-wrap">
	<div class="select-guests-dropdown" tabindex="-1">

		<div class="select-guests-item">
			<div class="input-group number-guest-input">
				<div class="context-box">
					<div class="context"><?= __('Adults', 'hostifybooking') ?></div>
					<?php if (HFY_SHOW_GUESTS_HINTS): ?>
						<div class="subcontext"><?= __('Age: 13+', 'hostifybooking') ?></div>
					<?php endif; ?>
				</div>
				<div class="func-box">
					<div class="input-func-box">
						<?php if (isset($listingDetails)): ?>
							<span class='ctrl-dec icon-dec'></span>
							<input id="adults" name="adults" value="<?= $adults ?>" readonly type="text" min="1" max="<?= $listingDetails->person_capacity; ?>" data-val="<?= $adults ?>" />
							<span class='ctrl-inc icon-inc'></span>
						<?php else: ?>
							<span class='ctrl-dec icon-dec'></span>
							<input id="adults" name="adults" value="<?= $adults ?>" readonly type="text" min="1" max="<?= HFY_GUESTS_MAX ?>" data-val="<?= $adults ?>" />
							<span class='ctrl-inc icon-inc'></span>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>

		<div class="select-guests-item">
			<div class="input-group number-guest-input">
				<div class="context-box">
					<div class="context"><?= __('Children', 'hostifybooking') ?></div>
					<?php if (HFY_SHOW_GUESTS_HINTS): ?>
						<div class="subcontext"><?= __('Age: 2-12', 'hostifybooking') ?></div>
					<?php endif; ?>
				</div>
				<div class="func-box">
					<div class="input-func-box">
						<?php if (isset($listingData)): ?>
							<span class='ctrl-dec icon-dec <?= $listingData->children_allowed ? '' : 'disabled' ?>'></span>
							<input id="children" name="children" value="<?= $listingData->children_allowed ? $children : 0 ?>" type="text" min="0" readonly class="<?= $listingData->children_allowed ? '' : 'disabled' ?>" data-val="<?= $listingData->children_allowed ? $children : 0 ?>" />
							<span class='ctrl-inc icon-inc <?= $listingData->children_allowed ? '' : 'disabled' ?>'></span>
						<?php else: ?>
							<span class='ctrl-dec icon-dec'></span>
							<input id="children" name="children" value="<?= $children ?>" type="text" min="0" max="<?= HFY_GUESTS_MAX ?>" readonly data-val="<?= $children ?>" />
							<span class='ctrl-inc icon-inc'></span>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>

		<?php if (HFY_SHOW_INFANTS): ?>
			<div class="select-guests-item">
				<div role="group" class="input-group number-guest-input">
					<div class="context-box">
						<div class="context"><?= __('Infants', 'hostifybooking') ?></div>
						<?php if (HFY_SHOW_GUESTS_HINTS): ?>
							<div class="subcontext"><?= __('Age under 2', 'hostifybooking') ?></div>
						<?php endif; ?>
					</div>
					<div class="func-box">
						<div class="input-func-box">
							<?php if (isset($listingData)): ?>
								<span class='ctrl-dec icon-dec <?= $listingData->infants_allowed ? '' : 'disabled' ?>'></span>
								<input id="infants" name="infants" value="<?= $listing->listing->infants_allowed ? $infants : 0 ?>" type="text" readonly min="0" max="<?=
									isset($listingDetails)
										? (HFY_SHOW_INFANTS_MAX <= 0 ? $listingDetails->person_capacity : HFY_SHOW_INFANTS_MAX)
										: ''
								?>" class="<?= $listingData->infants_allowed ? '' : 'disabled' ?>" data-val="<?= $listing->listing->infants_allowed ? $infants : 0 ?>" />
								<span class='ctrl-inc icon-inc <?= $listingData->infants_allowed ? '' : 'disabled' ?>'></span>
							<?php else: ?>
								<span class='ctrl-dec icon-dec'></span>
								<input id="infants" name="infants" value="<?= $infants ?>" type="text" readonly min="0" max="<?= HFY_GUESTS_MAX ?>" data-val="<?= $infants ?>" />
								<span class='ctrl-inc icon-inc'></span>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		<?php else: ?>
			<input id="infants" name="infants" value="0" type="hidden" />
		<?php endif; ?>

		<?php if (HFY_SHOW_PETS): ?>
			<div class="select-guests-item pets-control <?= HFY_ADV_SEARCH_PETS ? 'pets-control-adv' : '' ?>">
				<div role="group" class="input-group number-guest-input">
					<div class="context-box">
						<div class="context"><?= __('Pets', 'hostifybooking') ?></div>
					</div>
					<div class="func-box">
						<div class="input-func-box">
							<?php if (isset($listingData)): ?>
								<span class='ctrl-dec icon-dec <?= $listingData->pets_allowed ? '' : 'disabled' ?>'></span>
								<input id="pets" name="pets" value="<?= $listing->listing->pets_allowed ? $pets : 0 ?>" type="text" readonly min="0" max="<?= HFY_SHOW_PETS_MAX <= 0 ? 1 : HFY_SHOW_PETS_MAX ?>" class="<?= $listingData->pets_allowed ? '' : 'disabled' ?>" data-val="<?= $listing->listing->pets_allowed ? $pets : 0 ?>" />
								<span class='ctrl-inc icon-inc <?= $listingData->pets_allowed ? '' : 'disabled' ?>'></span>
							<?php else: ?>
								<span class='ctrl-dec icon-dec'></span>
								<input id="pets" name="pets" value="<?= isset($pets) ? $pets : 0 ?>" type="text" readonly min="0" max="<?= HFY_GUESTS_MAX ?>" />
								<span class='ctrl-inc icon-inc'></span>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		<?php else: ?>
			<input id="pets" name="pets" value="0" type="hidden" />
		<?php endif; ?>

		<?php if(isset($listingData) && isset($listing)): ?>
			<div class="extra-description">
				<?= isset($listingDetails) ?
						($listing->listing->infants_allowed
							? str_replace('{max}', $listingDetails->person_capacity, __('This place has a maximum of {max} guests, not including infants.', 'hostifybooking'))
							: str_replace('{max}', $listingDetails->person_capacity, __('This place has a maximum of {max} guests, infants not allowed.', 'hostifybooking')
						))
						: ''
					. ' '
					. ($listingData->children_allowed ? ''
						: (
							__('Children aren\'t allowed', 'hostifybooking')
							. ($listingData->children_not_allowed_details
								? ' (' . $listingData->children_not_allowed_details . ').'
								: '.'
							)
						)
					) . ' '
					. ($listing->listing->pets_allowed
						?  __('Pets allowed.', 'hostifybooking')
						:  __('Pets aren\'t allowed.', 'hostifybooking')
					)
				?>
			</div>
		<?php endif; ?>

		<div class="btn-close-guests-box">
			<button type="button" class="btn btn-default"><?= __('Close', 'hostifybooking') ?></button>
		</div>
	</div>
</div>
