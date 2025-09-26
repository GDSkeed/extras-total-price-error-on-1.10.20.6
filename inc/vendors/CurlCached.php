<?php

require_once 'Curl.php';

/**
 * Yii2 cURL wrapper
 * With RESTful support
 * with caching using WordPress Transients API
 *
 * @author    Softrest <info@softrest.eu>
 * @version   1.0.0
 * @link      http://softrest.eu
 *
 */

class CurlCached extends Curl
{
    protected $_use_cache = true;
    protected $_ckey = null;
    protected $_cache_lifetime;

    public function __construct($cacheTime = 300, $use = true)
    {
        $this->_use_cache = !!$use;
        $this->_ckey = null;
        $this->_cache_lifetime = intval($cacheTime);
    }

    public function make_ckey($post = false)
    {
        if (empty($this->_ckey)) {
            $x = $this->getUrl();
            if ($post) {
                // append post data
            }
            $this->_ckey = 'hfybook_curl_' . sha1($x);
        }
    }

    public function get($url, $raw = true)
    {
        $this->_baseUrl = $url;
        if ($this->_use_cache) {
            $this->make_ckey();
            $cached = get_transient($this->_ckey);
            if (false !== $cached) {
                $this->log('GET CACHED', $raw, $cached, 0);
                return $cached;
            }
        }
        $response = $this->_httpRequest('GET', $raw, $this->_use_cache);
        if ($this->_use_cache) {
            set_transient($this->_ckey, $response, $this->_cache_lifetime);
        }
        return $response;
    }

    public function post($url, $raw = true, $dbg = false)
    {
        $this->_baseUrl = $url;
        if ($this->_use_cache) {
            $this->make_ckey();
            $cached = get_transient($this->_ckey);
            if (false !== $cached) {
                $this->log('POST CACHED', $raw, $cached, 0);
                return $cached;
            }
        }
        $response = $this->_httpRequest('POST', $raw, $this->_use_cache, $dbg);
        if ($this->_use_cache) {
            set_transient($this->_ckey, $response, $this->_cache_lifetime);
        }
        return $response;
    }
}
