<?php if (!defined('WPINC')) die; ?>

<?php if (HFY_USE_NEW_CALENDAR): ?>    
    <div class="hfy-listing-availability"><div></div></div>
<?php elseif (HFY_USE_V3_CALENDAR): ?>
    <div class="hfy-listing-availability">
        <div id="hotel-datepicker-v3"></div>
    </div>
<?php endif; ?>
