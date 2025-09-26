<?php

require_once HOSTIFYBOOKING_DIR . 'inc/helpers/UrlHelper.php';
require_once HOSTIFYBOOKING_DIR . 'inc/helpers/ListingHelper.php';

class HFY_SEO
{
    private $_listing_info = [];

    public function __construct()
    {
        $this->register_hooks();
        $this->add_custom_fields();
    }

    public function register_hooks()
    {
        add_filter('wpseo_title', [ $this, 'make_listing_title' ], 10, 1);
        add_filter('wpseo_opengraph_title', [ $this, 'make_listing_title' ], 10, 1);
        add_filter('wpseo_opengraph_desc', [ $this, 'make_listing_description' ], 10, 1);
        add_filter('wpseo_opengraph_type', [ $this, 'get_og_type' ], 10, 1);
        add_filter('wpseo_opengraph_image', [ $this, 'get_og_image' ], 10, 1);

        add_filter('wpseo_opengraph_url', [ $this, 'get_canonical_url' ], 10, 1);

        add_filter('wp_head', function()
        {
            if ($u = $this->get_canonical_url()) {
                echo "<link rel='canonical' href='$u' />";
            }
        });
    }

    /**
     * add custom fields and metabox
     */
    public function add_custom_fields()
    {
        add_filter('stm_wpcfto_boxes', function($boxes)
        {
            $boxes['hfy_page_options'] = [
                'post_type' => ['page'],
                'label' => esc_html__('Hostify settings', 'hostifybooking'),
            ];
            return $boxes;
        });

        add_filter('stm_wpcfto_fields', function ($fields)
        {
            $fields['hfy_page_options'] = [
                'seo' => [
                    'name' => esc_html__('SEO', 'hostifybooking'),
                    'fields' => [
                        'use_as_listing' => [
                            'type' => 'checkbox',
                            'label' => esc_html__('Apply SEO settings (such as a pretty URL) to the Listing on this page', 'hostifybooking'),
                        ],
                    ]
                ],
            ];
            return $fields;
        });
    }

    public function get_listing_info($id = 0)
    {
        if ($id > 0) {
            if (isset($this->_listing_info[$id])) {
                return $this->_listing_info[$id];
            }
            global $HostifyBookingPlugin;
            if (isset($HostifyBookingPlugin)) {
                $info = $HostifyBookingPlugin->get_listing_info($id);
                if ($info) {
                    $_listing_info[$id] = $info;
                    return $info;
                }
            }
        }
        return false;
    }

    public function get_canonical_url($url = '')
    {
        if ($u = $this->check_and_make_canonical_url()) {
            return $u;
        }
        return $url;
    }

    public function check_and_make_canonical_url()
    {
        if ($this->is_listing_page()) {
            if ($url = UrlHelper::get_listing_human_url_single()) {
                return str_replace('?', '', $url);
            }
        }
        return false;
    }

    public function is_listing_page()
    {
        return
            is_page(HFY_PAGE_LISTING)
            || (get_post_meta(get_the_ID(), 'use_as_listing', true) == 'on')
        ;
    }

    public function get_og_image($url)
    {
        if ($this->is_listing_page()) {
            $info = $this->get_listing_info($this->get_listing_id());
            if ($info) {
                if ($info->thumbnail_file) {
                    return $info->thumbnail_file;
                }
            }
        }
        return $url;
    }

    public function get_og_type($ret)
    {
        if ($this->is_listing_page()) {
            return 'website';
        }
        return $ret;
    }

    public function get_listing_id()
    {
        if ($this->is_listing_page()) {
            $prm = hfy_get_vars_(['id'], true);
            return intval($prm->id && empty($id) ? $prm->id : ($_GET['id'] ?? 0));
        }
        return false;
    }

    /**
     * title
     */
    public function make_listing_title($title)
    {
        if ($this->is_listing_page()) {
            $id = $this->get_listing_id();
            if ($id) {
                $info = $this->get_listing_info($id);
                if ($info) {
                    return
                        ListingHelper::listingName($info)
                        . ($info->city ? ', ' . $info->city : '')
                        . ($info->neighbourhood ? ', ' . $info->neighbourhood : '')
                        . ($info->country ? ', ' . $info->country : '')
                        . ' - ' . get_bloginfo('name')
                    ;
                }
            }
        }
        return $title;
    }

    /**
     * description
     */
    public function make_listing_description($desc)
    {
        if ($this->is_listing_page()) {
            $id = $this->get_listing_id();
            if ($id) {
                $info = $this->get_listing_info($id);
                if ($info) {
                    $s =
                        $info->name
                        . ', ' . $info->city
                        . ($info->neighbourhood ? ', ' . $info->neighbourhood : '')
                        . ', ' . $info->country
                    ;
                    $s = strip_tags($s);
                    $s = strip_shortcodes($s);
                    $s = str_replace(array("\n", "\r", "\t"), ' ', $s);
                    $s = preg_replace('/\s/', ' ', $s);
                    $s = str_replace("'", '`', $s);
                    $s = substr(trim($s), 0, 125);

                    $s .= ' - ' . get_bloginfo('name');
                    return $s;
                }
            }
        }
        return $desc;
    }

    /**
     * meta description
     */
    public function make_listing_description_meta($desc)
    {
        if ($this->is_listing_page()) {
            $s = $this->make_listing_description($desc);
            return "<meta name='description' content='$s' />";
        }
        return $desc;
    }

}

/**
 * add listings & listings thumbs to yoast xml sitemap
 */
function hfy_add_sitemap_listings()
{
    global $wpseo_sitemaps;
    // $date = $wpseo_sitemaps->get_last_modified();
    $date = $wpseo_sitemaps->get_last_modified('page');
    return
    '<sitemap>' .
    '<loc>' . site_url() .'/listings-sitemap.xml</loc>' .
    '<lastmod>' . htmlspecialchars($date) . '</lastmod>' .
    '</sitemap>';
}


function hfy_get_listings_permalinks()
{
    global $wpdb;
    $tname = $wpdb->prefix . 'hfy_listing_permalink';
    return $wpdb->get_results("select * from {$tname}");
}

/**
 * add listing name as listing page slug
 */
function hfy_init_generate_listings_rewrite_rules()
{
    // Check if constants are defined before proceeding
    if (!defined('HFY_PAGE_LISTING_URL') || !defined('HFY_PAGE_LISTING')) {
        return;
    }
    
    $res = hfy_get_listings_permalinks();
    if ($res) {
        $page_listing_pre = preg_replace('/\/[^\/]+\/?$/', '', HFY_PAGE_LISTING_URL);
        $page_listing_pre = ltrim(str_replace(site_url(), '', $page_listing_pre), '/');
        if (strlen($page_listing_pre) > 0) $page_listing_pre .= '/';
        foreach ($res as $listing) {
            add_rewrite_rule(
                $page_listing_pre . $listing->permalink . '/?.*',
                'index.php?page_id=' . HFY_PAGE_LISTING . '&id=' . $listing->listing_id
                ,'top'
            );
        }
    }

    // add_filter( 'query_vars', function( $vars ) {
    //     $vars[] = 'id';
    //     $vars[] = 'listing_id';
    //     return $vars;
    // } );

    flush_rewrite_rules();
}

function hfy_generate_listings_sitemap()
{
    global $wpdb;
    global $wpseo_sitemaps;

    $date = $wpseo_sitemaps->get_last_modified('page');

    $tname = $wpdb->prefix . 'hfy_listing_permalink';
    $res = $wpdb->get_results("select * from {$tname}");

    $url = [];
    $pri = 1;
    $chf = 'weekly';
    $output = '';

    $settings = [
        'publish-languages' => ['en']
    ];

    if (class_exists('dbinfo') && class_exists('TRP_Translate_Press')) {
        $trp = TRP_Translate_Press::get_trp_instance();
        $trp_settings = $trp->get_component( 'settings' );
        $settings = $trp_settings->get_settings();
    }

    $ldef = substr($settings['default-language'] ?? 'en', 0, 2);
    $page_listing_pre = preg_replace('/\/[^\/]+\/?$/', '', HFY_PAGE_LISTING_URL);

    foreach ($res as $p) {
        foreach ($settings['publish-languages'] as $lng) {
            $l = substr($lng, 0, 2);
            if ($l == $ldef) {
                $l = '';
            } else {
                $l .= '/';
            }

            $url['url1'] = $page_listing_pre;
            $url['slug'] = $p->permalink;

            if (get_option('permalink_structure')) {
                $url['loc'] = $page_listing_pre . '/' . $l . $p->permalink . '/';
            } else {
                $url['loc'] = $page_listing_pre . '/?page_id=' . HFY_PAGE_LISTING . '&id=' . $p->listing_id;
            }

            $url['pri'] = $pri;
            $url['mod'] = $date;
            $url['chf'] = $chf;

            $pname = $p->name ?? $p->listing_name ?? '';

            $url['images'] = [[
                'src' => $p->thumb,
                'alt' => $pname,
                'title' => $pname
            ]];
            $output .= $wpseo_sitemaps->renderer->sitemap_url( $url );
        }
    }

    if (empty($output)) {
        $wpseo_sitemaps->bad_sitemap = true;
        return;
    }

    $sitemap = '<urlset ';
    $sitemap .= 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" ';
    $sitemap .= 'xmlns:xsi="https://www.w3.org/2001/XMLSchema-instance" ';
    $sitemap .= 'xmlns:xhtml="http://www.w3.org/1999/xhtml" ';
    $sitemap .= 'xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" ';
    $sitemap .= 'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd http://www.google.com/schemas/sitemap-image/1.1 http://www.google.com/schemas/sitemap-image/1.1/sitemap-image.xsd" ';
    $sitemap .= '>';
    $sitemap .= $output;
    $sitemap .= '</urlset>';

    $wpseo_sitemaps->set_sitemap(trim($sitemap));
}

function hfy_generate_listings_sitemap_rankmath()
{
    $tpl = '<url>
        <loc>{{url}}</loc>
        <lastmod>{{time}}</lastmod>
        <image:image><image:loc>{{img}}</image:loc></image:image>
    </url>';

    $time = gmdate(DATE_W3C);

    $output = '';

    $settings = [
        'publish-languages' => ['en']
    ];

    if (class_exists('dbinfo') && class_exists('TRP_Translate_Press')) {
        $trp = TRP_Translate_Press::get_trp_instance();
        $trp_settings = $trp->get_component( 'settings' );
        $settings = $trp_settings->get_settings();
    }

    $ldef = substr($settings['default-language'] ?? 'en', 0, 2);
    $page_listing_pre = preg_replace('/\/[^\/]+\/?$/', '', HFY_PAGE_LISTING_URL);

    global $wpdb;
    $tname = $wpdb->prefix . 'hfy_listing_permalink';
    $res = $wpdb->get_results("select * from {$tname}");

    foreach ($res as $p) {
        foreach ($settings['publish-languages'] as $lng) {
            $l = substr($lng, 0, 2);
            if ($l == $ldef) {
                $l = '';
            } else {
                $l .= '/';
            }

            if (get_option('permalink_structure')) {
                $url = $page_listing_pre . '/' . $l . $p->permalink . '/';
            } else {
                $url = $page_listing_pre . '/?page_id=' . HFY_PAGE_LISTING . '&id=' . $p->listing_id;
            }

            $output .= str_replace(['{{url}}', '{{time}}', '{{img}}'], [$url, $time, $p->thumb], $tpl);
        }
    }

    return $output;
}

$HFY_SEO = new HFY_SEO();

// Rewrite rules are now called manually after constants are defined

// Yoast SEO
add_filter('wpseo_sitemap_index', 'hfy_add_sitemap_listings');
add_action('wpseo_do_sitemap_listings', 'hfy_generate_listings_sitemap');
// add_filter('wpseo_enable_xml_sitemap_transient_caching', '__return_false');

// Rank Math SEO
add_action('rank_math/sitemap/page_content', 'hfy_generate_listings_sitemap_rankmath');
