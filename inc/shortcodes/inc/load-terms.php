<?php

$api = new HfyApi();
$terms_ = $api->getTerms($settings->fs_integration_id);
$terms = !empty($terms_->data->reservation_inquiry) ? $terms_->data->reservation_inquiry : '';
$terms_long = !empty($terms_->data->long_term_reservation_inquiry) ? $terms_->data->long_term_reservation_inquiry : '';
$terms_cancel = !empty($terms_->data->cancellation_policy) ? $terms_->data->cancellation_policy : '';
$terms_cancel_long = !empty($terms_->data->long_term_cancellation_policy) ? $terms_->data->long_term_cancellation_policy : '';
