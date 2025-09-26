<?php if (!defined('WPINC')) die; ?>

<div class="hfy-listing-info">

    <?php include hfy_tpl('listing/listing-info-summary'); ?>

    <br/>
    <?php include hfy_tpl('listing/listing-info-permit'); ?>

    <?php if (!empty($listingDescription->space)) { ?>
        <h4><?= __( 'The space', 'hostifybooking' ) ?></h4>
        <p><?= nl2br($listingDescription->space); ?></p>
    <?php } ?>

    <?php if (!empty($listingDescription->access)) { ?>
        <h4><?= __( 'Guest access', 'hostifybooking' ) ?></h4>
        <p><?= nl2br($listingDescription->access); ?></p>
    <?php } ?>

    <?php if (!empty($listingDescription->interaction)) { ?>
        <h4><?= __( 'Interaction with guests', 'hostifybooking' ) ?></h4>
        <p><?= nl2br($listingDescription->interaction); ?></p>
    <?php } ?>

    <?php if (!empty($listingDescription->notes)) { ?>
        <h4><?= __( 'Other things to note', 'hostifybooking' ) ?></h4>
        <p><?= nl2br($listingDescription->notes); ?></p>
    <?php } ?>

</div>
