<?php

class UrlHelper
{
    public static function buildLogoUrl($logo)
    {
        return '';
        // return //params['imageUrl'] . $logo . '?t=' . time();
    }

    public static function buildCityImageUrl($city)
    {
        return '';
        // return $city->id && $city->image ? //params['imageUrl'] . $city->image . '?t=' . time() : '';
    }

    public static function listing($prm = [])
    {
        $prm = is_array($prm) ? $prm : ['id' => $prm];

        // Get current language using Polylang if available
        $lang = '';
        if (function_exists('pll_current_language') && function_exists('pll_get_post')) {
            $lang = pll_current_language();
        }

        // remove default values
        if (isset($prm['long_term_mode'])) if ($prm['long_term_mode'] < 1) unset($prm['long_term_mode']);
        if (isset($prm['guests'])) if ($prm['guests'] < 2) unset($prm['guests']);
        if (isset($prm['adults'])) if ($prm['adults'] < 2) unset($prm['adults']);
        if (isset($prm['children'])) if ($prm['children'] < 2) unset($prm['children']);
        if (isset($prm['infants'])) if ($prm['infants'] < 2) unset($prm['infants']);
        if (isset($prm['pets'])) if ($prm['pets'] < 2) unset($prm['pets']);

        $id = $prm['id'] ?? '';
        $query = http_build_query($prm);

        // Get the base URL with language
        $baseUrl = HFY_PAGE_LISTING_URL;
        if ($lang && function_exists('pll_home_url') && function_exists('pll_get_post')) {
            $baseUrl = pll_home_url($lang);
            $parsed = parse_url(HFY_PAGE_LISTING_URL);
            $path = $parsed['path'] ?? '';
            $path = preg_replace('/^\/[a-z]{2}\//', '', $path);
            
            // Get the language-specific slug
            if (function_exists('pll_get_post_language')) {
                $post_id = url_to_postid(HFY_PAGE_LISTING_URL);
                if ($post_id) {
                    $translated_id = pll_get_post($post_id, $lang);
                    if ($translated_id) {
                        $translated_slug = get_post_field('post_name', $translated_id);
                        if ($translated_slug) {
                            $path = preg_replace('/\/[^\/]+\/?$/', '/' . $translated_slug . '/', $path);
                        }
                    }
                }
            }
            
            $baseUrl = rtrim($baseUrl, '/') . $path;
        }

        $url = $baseUrl . (strpos($baseUrl, '?') ? '&' : '?') . $query;
        return HFY_SEO_LISTINGS ? self::get_listing_human_url($id, $prm) : $url;
    }

    public static function listings($prm = [], $samepage = false)
    {
        // Get current language using Polylang if available
        $lang = '';
        if (function_exists('pll_current_language') && function_exists('pll_get_post')) {
            $lang = pll_current_language();
        }

        if ($samepage) {
            return '?' . http_build_query($prm);
        }

        // Get the base URL with language
        $baseUrl = HFY_PAGE_LISTINGS_URL;
        if ($lang && function_exists('pll_home_url') && function_exists('pll_get_post')) {
            $baseUrl = pll_home_url($lang);
            $parsed = parse_url(HFY_PAGE_LISTINGS_URL);
            $path = $parsed['path'] ?? '';
            $path = preg_replace('/^\/[a-z]{2}\//', '', $path);
            
            // Get the language-specific slug
            if (function_exists('pll_get_post_language')) {
                $post_id = url_to_postid(HFY_PAGE_LISTINGS_URL);
                if ($post_id) {
                    $translated_id = pll_get_post($post_id, $lang);
                    if ($translated_id) {
                        $translated_slug = get_post_field('post_name', $translated_id);
                        if ($translated_slug) {
                            $path = preg_replace('/\/[^\/]+\/?$/', '/' . $translated_slug . '/', $path);
                        }
                    }
                }
            }
            
            $baseUrl = rtrim($baseUrl, '/') . $path;
        }

        return $baseUrl . (strpos($baseUrl, '?') ? '&' : '?') . http_build_query($prm);
    }

    public static function get_listing_human_url($id = null, $params = [])
    {
        $slug = self::get_listing_human_slug($id);
        
        // Get current language using Polylang if available
        $lang = '';
        if (function_exists('pll_current_language') && function_exists('pll_get_post')) {
            $lang = pll_current_language();
        }

        // Get base URL with language
        $baseUrl = HFY_PAGE_LISTING_URL;
        if ($lang && function_exists('pll_home_url') && function_exists('pll_get_post')) {
            $baseUrl = pll_home_url($lang);
            $parsed = parse_url(HFY_PAGE_LISTING_URL);
            $path = $parsed['path'] ?? '';
            $path = preg_replace('/^\/[a-z]{2}\//', '', $path);
            
            // Get the language-specific slug
            if (function_exists('pll_get_post_language')) {
                $post_id = url_to_postid(HFY_PAGE_LISTING_URL);
                if ($post_id) {
                    $translated_id = pll_get_post($post_id, $lang);
                    if ($translated_id) {
                        $translated_slug = get_post_field('post_name', $translated_id);
                        if ($translated_slug) {
                            $path = preg_replace('/\/[^\/]+\/?$/', '/' . $translated_slug . '/', $path);
                        }
                    }
                }
            }
            
            $baseUrl = rtrim($baseUrl, '/') . $path;
        }

        if ($slug) {
            $url = preg_replace('/\/[^\/]+\/?$/', '/' . $slug . '/', $baseUrl);
        } else {
            $url = $baseUrl;
        }

        if (!empty($params) && is_array($params)) {
            unset($params['hfylisting']);
            $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($params);
        } else {
            $url .= '?id=' . $id;
        }

        return $url;
    }

    public static function get_listing_human_url_single()
    {
        $qs = trim(explode('?', $_SERVER['REQUEST_URI'])[1] ?? '');
        if (empty($qs)) {
            $rq = preg_replace('/^\/.{2}\//', '', $_SERVER['REQUEST_URI'], 1);
            // Get current language using Polylang if available
            $lang = '';
            if (function_exists('pll_current_language') && function_exists('pll_get_post')) {
                $lang = pll_current_language();
            }
            $baseUrl = get_bloginfo('url');
            if ($lang && function_exists('pll_home_url') && function_exists('pll_get_post')) {
                $baseUrl = pll_home_url($lang);
            }
            return rtrim($baseUrl, '/') . '/' . ltrim($rq, '/');
        } else {
            $id = intval($_GET['id'] ?? null);
            if ($id) {
                $slug = self::get_listing_human_slug($id);
                if ($slug) {
                    // Get current language using Polylang if available
                    $lang = '';
                    if (function_exists('pll_current_language') && function_exists('pll_get_post')) {
                        $lang = pll_current_language();
                    }
                    $baseUrl = HFY_PAGE_LISTING_URL;
                    if ($lang && function_exists('pll_home_url') && function_exists('pll_get_post')) {
                        $baseUrl = pll_home_url($lang);
                        $parsed = parse_url(HFY_PAGE_LISTING_URL);
                        $path = $parsed['path'] ?? '';
                        $path = preg_replace('/^\/[a-z]{2}\//', '', $path);
                        
                        // Get the language-specific slug
                        if (function_exists('pll_get_post_language')) {
                            $post_id = url_to_postid(HFY_PAGE_LISTING_URL);
                            if ($post_id) {
                                $translated_id = pll_get_post($post_id, $lang);
                                if ($translated_id) {
                                    $translated_slug = get_post_field('post_name', $translated_id);
                                    if ($translated_slug) {
                                        $path = preg_replace('/\/[^\/]+\/?$/', '/' . $translated_slug . '/', $path);
                                    }
                                }
                            }
                        }
                        
                        $baseUrl = rtrim($baseUrl, '/') . $path;
                    }
                    return preg_replace('/\/[^\/]+\/?$/', '/' . $slug . '/', $baseUrl);
                }
            }
        }
        return HFY_PAGE_LISTING_URL . '?id=' . $id;
    }

    public static function get_listing_human_slug($id = null)
    {
        // todo
        // $lang = 'en';
        // if (class_exists('TRP_Translation_Render')) {
        //     global $TRP_LANGUAGE;
        //     $lang = substr($TRP_LANGUAGE ?? 'en', 0, 2);
        // }

        $id = $id ? $id : intval($_GET['id'] ?? 0);
        if ($id > 0) {
            global $wpdb;
            $tname = $wpdb->prefix . 'hfy_listing_permalink';
            $sql = $wpdb->prepare("select permalink from {$tname} where listing_id = %d limit 1", [ $id ]);
            $res = $wpdb->get_row($sql);
            if ($res) {
                return $res->permalink;
            }
        } else {

            // $rules = get_option( 'rewrite_rules' );
            // var_dump($rules);die;
            // foreach

            $parsed = self::parse_listings_url();
            global $wpdb;
            $tname = $wpdb->prefix . 'hfy_listing_permalink';
            $sql = $wpdb->prepare("select permalink from {$tname} where permalink = %s limit 1", [ $parsed['city'] ]);
            $res = $wpdb->get_row($sql);
            if ($res) {
                return $res->permalink;
            }
        }
        return false;
    }

    public static function parse_listings_url()
    {
        // $_SERVER['PATH_INFO'] -- miss on live server
        // $_SERVER['QUERY_STRING']

        $i = $_SERVER['PATH_INFO'] ?? null;
        $qs = $_SERVER['QUERY_STRING'] ?? null;
        if (!$i) {
            $ii = explode('?', $_SERVER['REQUEST_URI']);
            $i = $ii[0] ?? '';
            $qs = $ii[1] ?? '';
        }

        $path = explode('/', $i);

        # search: /lang/city/district/type/amenity/?prms=...&...
        #   ex: /en/barcelona/gracia/with-terrace/?from=&till=&
        #   ex: /en/barcelona/gracia/with-terrace/?from=&till=&
        # single listing:
        #   ex: /en/sky-blue-attic-penthouse-gothic-quarter/?...

        $lang = trim($path[1] ?? '');

        if (strlen($lang) > 2) {
            return [
                'full' => $i . '?' . $qs,
                'path' => $i,
                'qs' => $qs,
                'lang' => '',
                'city' => $path[1] ?? '',
                'district' => $path[2] ?? '',
                'type' => $path[3] ?? '',
                'amenity' => $path[4] ?? '',
            ];
        }

        return [
            'full' => $i . '?' . $qs,
            'path' => $i,
            'qs' => $qs,
            'lang' => $lang,
            'city' => $path[2] ?? '',
            'district' => $path[3] ?? '',
            'type' => $path[4] ?? '',
            'amenity' => $path[5] ?? '',
        ];
    }

    public static function init()
    {
        // Only add filters if Polylang is active
        if (function_exists('pll_current_language') && function_exists('pll_get_post')) {
            // Add filter to preserve language codes in URLs
            add_filter('page_link', function($link, $post_id) {
                if (function_exists('pll_current_language')) {
                    $lang = pll_current_language();
                    if ($lang) {
                        $parsed = parse_url($link);
                        $path = $parsed['path'] ?? '';
                        if (!preg_match('/^\/[a-z]{2}\//', $path)) {
                            $link = preg_replace('/^https?:\/\/[^\/]+/', 'https://' . $_SERVER['HTTP_HOST'] . '/' . $lang, $link);
                        }
                    }
                }
                return $link;
            }, 10, 2);

            // Add filter to preserve language codes in all URLs
            add_filter('home_url', function($url) {
                if (function_exists('pll_current_language')) {
                    $lang = pll_current_language();
                    if ($lang) {
                        $parsed = parse_url($url);
                        $path = $parsed['path'] ?? '';
                        if (!preg_match('/^\/[a-z]{2}\//', $path)) {
                            $url = preg_replace('/^https?:\/\/[^\/]+/', 'https://' . $_SERVER['HTTP_HOST'] . '/' . $lang, $url);
                        }
                    }
                }
                return $url;
            }, 10, 1);

            // Add filter to preserve language codes in form actions
            add_filter('form_action', function($action) {
                if (function_exists('pll_current_language')) {
                    $lang = pll_current_language();
                    if ($lang) {
                        $parsed = parse_url($action);
                        $path = $parsed['path'] ?? '';
                        if (!preg_match('/^\/[a-z]{2}\//', $path)) {
                            $action = preg_replace('/^https?:\/\/[^\/]+/', 'https://' . $_SERVER['HTTP_HOST'] . '/' . $lang, $action);
                        }
                    }
                }
                return $action;
            }, 10, 1);

            // Add filter to preserve query parameters in Polylang language switcher
            add_filter('pll_the_language_link', function($url, $lang) {
                // Only modify if we have query parameters
                if (!empty($_SERVER['QUERY_STRING'])) {
                    // Remove any existing query string from the language switcher URL
                    $url = strtok($url, '?');
                    // Add the current query string
                    $url .= '?' . $_SERVER['QUERY_STRING'];
                }
                return $url;
            }, 10, 2);
        }
    }
}
