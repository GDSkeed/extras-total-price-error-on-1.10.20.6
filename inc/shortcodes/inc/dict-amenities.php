<?php
if ( ! defined( 'WPINC' ) ) die;

$api = new HfyApi();
$ams_ = $api->getAmenities(HFY_ADV_SEARCH_AM_SHORT);

$dictionaryAmenities = [];
$dictionaryAmenities_ = [];

if (isset($ams_->amenities)) {
	$oth = [];
	foreach ($ams_->amenities as $item) {
		$gr = $item->group_name ?? null;
		if ($gr) {
			$dictionaryAmenities_[$gr][$item->id] = $item->name;
		} else {
			$oth[$item->id] = $item->name;
		}
	}
	if (!empty($oth)) {
		$dictionaryAmenities_['Other'] = $oth;
	}
}

// asort($dictionaryAmenities_);
// var_dump($dictionaryAmenities_, array_flip(explode(',', HFY_ADV_SEARCH_AM_LIST)));
if (HFY_ADV_SEARCH_AM) {
	$asel = array_flip(explode(',', HFY_ADV_SEARCH_AM_LIST));
	foreach ($dictionaryAmenities_ as $k => $v) {
		$x = array_intersect_key($v, $asel);
		if (!empty($x)) {
			$dictionaryAmenities[$k] = $x;
		}
	}
} else {
	$dictionaryAmenities = $dictionaryAmenities_;
}

if (HFY_ADV_SEARCH_AM_GROUPS_HIDE_OTHER) {
	if (isset($dictionaryAmenities['Other'])) unset($dictionaryAmenities['Other']);
}

// if (isset($prm->am) && gettype($prm->am) === 'array') {
// 	arrayMoveSelectedToTop($dictionaryAmenities, $prm->am);
// }

$amNames = [];
foreach ($dictionaryAmenities as $group => $ams) {
	foreach ($ams as $amId => $amName) {
		$amNames[$amId] = $amName;
	}
}

if ( ! function_exists( 'hfy_amenity_name' ) ) {
	function hfy_amenity_name( $id, $def = '' ) {
		return isset($amNames[$id]) ? $amNames[$id] : $def;
	}
}
