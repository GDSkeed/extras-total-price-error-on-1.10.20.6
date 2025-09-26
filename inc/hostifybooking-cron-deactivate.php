<?php

/**
 * de-activate cron jobs
 */

function cronstarter_deactivation_hostifybooking()
{
    $timestamp = wp_next_scheduled(HOSTIFYBOOKING_CRON_NAME);
    wp_unschedule_event($timestamp, HOSTIFYBOOKING_CRON_NAME);
}

register_deactivation_hook(__FILE__, 'cronstarter_deactivation_hostifybooking');
