<?php

$for_listing_id = 0;
global $wp;
$for_listing_id = (int) ($listingId ?? $_GET['id'] ?? $wp->query_vars['id'] ?? 0);

$api = new HfyApi();
$publishable_key_ = $api->getPaymentToken($paymentSettings->services->service ?? 'none', $for_listing_id);
$publishable_key = $publishable_key_ ? $publishable_key_->token : '';
