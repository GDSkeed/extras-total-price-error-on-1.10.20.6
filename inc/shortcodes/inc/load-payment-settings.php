<?php

$for_listing_id = 0;
global $wp;
$for_listing_id = (int) ($listingId ?? $id ?? $_GET['id'] ?? $wp->query_vars['id'] ?? 0);

$api = new HfyApi();
$paymentSettings = $api->getPaymentSettings($for_listing_id, $startDate ?? null, $endDate ?? null);
