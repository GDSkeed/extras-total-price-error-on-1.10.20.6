<?php
if ( ! defined( 'WPINC' ) ) die;

$dictionaryPropertyType_ = [
	1 => 'Apartment',
	2 => 'Bungalow',
	3 => 'Cabin',
	4 => 'Condominium',
	5 => 'Guesthouse',
	6 => 'House',
	7 => 'Guest suite',
	8 => 'Townhouse',
	9 => 'Vacation home',
	10 => 'Boutique hotel',
	11 => 'Nature lodge',
	12 => 'Hostel',
	13 => 'Chalet',
	14 => 'Dorm',
	15 => 'Villa',
	16 => 'Other',
	17 => 'Bed and breakfast',
	18 => 'Studio',
	19 => 'Hotel',
	20 => 'Resort',
	21 => 'Castle',
	22 => 'Aparthotel',
	23 => 'Boat',
	24 => 'Cottage',
	25 => 'Camping',
	37 => 'Serviced apartment',
	38 => 'Loft',
	39 => 'Hut',
];

if (isset($propTypes)) {
	$t = $propTypes;
	$dictionaryPropertyType = [];
	foreach (array_keys($dictionaryPropertyType_) as $key) {
		if ($key != 16 && in_array($key, $propTypes)) {
			$dictionaryPropertyType[$key] = $dictionaryPropertyType_[$key];
		}
	}
} else {
	$dictionaryPropertyType = $dictionaryPropertyType_;
}

// if (isset($prm->prop) && gettype($prm->prop) === 'array') {
// 	arrayMoveSelectedToTop($dictionaryPropertyType, $prm->prop);
// }
