<div class="alert alert-primary" role="alert">
    <?= __('No available properties for your search.', 'hostifybooking') ?>

    <br/>
    <?= __('Try to change the filter conditions.', 'hostifybooking') ?>

    <?php if (HFY_MAP_TRACKING): ?>
        <br/>
        <?= __('Try to move or resize the map.', 'hostifybooking') ?>
    <?php endif; ?>

</div>
