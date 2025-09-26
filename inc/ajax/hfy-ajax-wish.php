<?php
if (!defined('WPINC')) die;

$prm = hfy_post_vars_([
    'data',
]);

$out = ['success' => false];

if (is_user_logged_in()) {
    $user_id = get_current_user_id();
    $id = (int) ($prm->data['id'] ?? '');
    if ($id > 0) {
        $name = '_wishlist';
        $action = $prm->data['action'] ?? '';

        if ($action == 'add') {
            $list = get_user_meta($user_id, $name, true);
            $a = explode(',', $list);
            $a = array_map('intval', $a);
            $a[] = $id;
            $upd = array_unique($a);
            update_user_meta($user_id, $name, implode(',', $upd));
            $out['success'] = true;

        } else if ($action == 'remove') {
            $list = get_user_meta($user_id, $name, true);
            $a = explode(',', $list);
            $a = array_map('intval', $a);
            $a = array_filter($a, function($val) use($id) {
                return $val != $id;
            });
            update_user_meta($user_id, $name, implode(',', $a));
            $out['success'] = true;

        }
    }
}
