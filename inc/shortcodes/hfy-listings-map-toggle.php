<?php
if (!defined('WPINC')) die;

$mapIsHidden = intval($_GET['hidemap'] ?? 0) == 1;

$mobile = $mobile == 1 || strtolower($mobile) === 'true';
$tablet = $tablet == 1 || strtolower($tablet) === 'true';

include hfy_tpl('listing/listings-map-toggle');
