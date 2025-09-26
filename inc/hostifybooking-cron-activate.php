<?php

/**
 * activate cron jobs
 */

function hostifybooking_5min_interval($sched)
{
    $sched['hostifybooking_5_min'] = [
        'interval' => 300,
        'display' => 'Every 5 min'
    ];
    return $sched;
}

add_filter('cron_schedules', 'hostifybooking_5min_interval');

function cronstarter_activation_hostifybooking()
{
    if (!wp_next_scheduled(HOSTIFYBOOKING_CRON_NAME)) {
        wp_schedule_event(time(), 'hostifybooking_5_min', HOSTIFYBOOKING_CRON_NAME);
    }
}

add_action('wp', 'cronstarter_activation_hostifybooking');

add_action(HOSTIFYBOOKING_CRON_NAME, 'hostifybooking_get_prices_search');
