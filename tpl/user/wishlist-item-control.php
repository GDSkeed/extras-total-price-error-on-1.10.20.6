<?php
if (!defined('WPINC')) die;
?>

<?php if (is_user_logged_in()) : ?>
    <span class="<?= in_array((int) $listing->id, $wishlist ?? []) ? 'added' : 'add' ?>-to-wish" data-id="<?= $listing->id ?>"></span>
<?php endif; ?>
