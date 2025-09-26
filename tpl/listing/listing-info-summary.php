<?php if (!defined('WPINC')) die; ?>

<div class="hfy-listing-info-summary">
    <?= nl2br(empty($listingDescription->summary) ? $listingDescription->description : $listingDescription->summary); ?>
</div>
