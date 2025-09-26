<?php
if (!defined('WPINC')) die;

/*

echo '<pre>';
var_dump($prm->neighbourhood);
print_r($location_raw);
print_r($location);
echo '</pre>';

example:

string(37) "Brazil:State of Maranhão:5043:Calhau"
stdClass Object
(
    [city_id] => 5043
    [city] => São Luís
    [neighbourhood] => Calhau
    [country] => Brazil
    [state] => State of Maranhão
)
Array
(
    [0] => Brazil
    [1] => State of Maranhão
    [2] => São Luís
    [3] => Calhau
)

*/

// out example 1:

// echo implode(' &rarr; ', $location);


// out example 2:

// if (!empty($location_raw->city)) echo $location_raw->city;


// out example 3:

echo implode(', ', array_reverse($location));
