<?php

class HfyHelper
{

    public static function getWishlist()
    {
		    if (!is_user_logged_in()) return [];
		    return array_map('intval', explode(',', get_user_meta(get_current_user_id(), '_wishlist', true)));
    }

    public static function getUserMeta($slug = '')
    {
        if (!is_user_logged_in()) return '';
        $user = wp_get_current_user();
        if (isset($user->$slug)) {
            return $user->$slug;
        }
        return get_user_meta(get_current_user_id(), $slug, true);
    }

}
