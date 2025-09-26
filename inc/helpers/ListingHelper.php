<?php

class ListingHelper
{
    public static $listing_types = [
        1 => 'listing_category_type_home',
        2 => 'listing_category_type_hotel',
        9 => 'listing_category_type_other',
    ];

    public static $room_types = [
        1 => 'Entire home/flat',
        2 => 'Private room',
        3 => 'Shared room',
    ];

    public static $room_types_title = [
        1 => 'Flats for',
        2 => 'Rooms for',
        3 => 'Rooms for',
    ];

    public static $property_types = [
        1	=> 'Apartment',
        2	=> 'Bungalow',
        3	=> 'Cabin',
        4	=> 'Condominium',
        5	=> 'Guesthouse',
        6	=> 'House',
        7	=> 'Guest suite',
        8	=> 'Townhouse',
        9	=> 'Vacation home',
        10	=> 'Boutique hotel',
        11	=> 'Nature lodge',
        12	=> 'Hostel',
        13	=> 'Chalet',
        14	=> 'Dorm',
        15	=> 'Villa',
        16	=> 'Other',
        17	=> 'Bed and breakfast',
        18	=> 'Studio',
        19	=> 'Hotel',
        20	=> 'Resort',
        21	=> 'Castle',
        22	=> 'Aparthotel',
        23	=> 'Boat',
        24	=> 'Cottage',
        25	=> 'Camping',
        37	=> 'Serviced apartment',
        38	=> 'Loft',
        39	=> 'Hut',
    ];

    private static function fakestr() // for wp translate
    {
        $a = [
            __('Entire home/flat', 'hostifybooking'),
            __('Private room', 'hostifybooking'),
            __('Shared room', 'hostifybooking'),
        ];
    }

    public static function getListingType($listingTypeId)
    {
        return isset(self::$listing_types[$listingTypeId]) ? self::$listing_types[$listingTypeId] : '';
    }

    public static function getPropertyType($id)
    {
        return isset(self::$property_types[(int) $id]) ? __(self::$property_types[(int) $id], 'hostifybooking') : '';
    }

    public static function getRoomType($roomId)
    {
        return isset(self::$room_types[$roomId]) ? __(self::$room_types[$roomId], 'hostifybooking') : '';
    }

    public static function getRoomTypeForTitle($roomId)
    {
        return isset(self::$room_types_title[$roomId]) ? self::$room_types_title[$roomId] : '';
    }

    public static function getReviewRating($rating)
    {
        // Return rating as-is without any scale conversion
        $rating = floatval($rating);
        
        // Only ensure minimum is 0, allow any positive value
        $rating = max(0, $rating);
        return round($rating, 1);
    }

    public static function getReviewStarRating($rating, $maxStars = 5)
    {
        return $rating ? round(round($rating, 1) * (100/$maxStars)) : 0;
    }

    public static function calcPricePerNight($listingPrice, $priceMarkup = 0)
    {
        if (is_object($listingPrice)) {
            $price = round($listingPrice->price / ($listingPrice->nights ?? 1));
            return self::calcPriceMarkup($price, $listingPrice->price_markup ?? 0);
        } else {
            return self::calcPriceMarkup($listingPrice, $priceMarkup);
        }
    }

    public static function calcDefaultPrice($listingData)
    {
        return self::calcPriceMarkup($listingData->default_daily_price, $listingData->price_markup);
    }

    public static function calcPriceMarkup($price, $listingPriceMarkup) {
        // if ( //params['price_markup'] !== false) {
        //     $price *= (1 + //params['price_markup'] / 100);
        //     return round($price);
        // } else
        // todo
        if ($listingPriceMarkup) {
            $price *= (1 + $listingPriceMarkup / 100);
            return round($price);
        }
        return $price;
    }

    public static function toAirbnbDateFormat($date)
    {
        return date("Y-m-d", strtotime($date));
    }

    /**
     * Convert BGN price to display both BGN and EUR
     * 
     * @param float $price Price in BGN
     * @param string $space Space character between currency and amount
     * @return string Formatted price with both currencies
     */
    public static function formatBGNWithEUR($price, $space = '&nbsp;')
    {
        // Central rate: 1 EUR = 1.95583 BGN
        $centralRate = 1.95583;
        
        // Convert BGN to EUR and round to 2 decimal places
        $eurPrice = round($price / $centralRate, 2);
        
        // Format BGN price (keep original formatting)
        $bgnFormatted = number_format($price, 2, '.', ',');
        
        // Format EUR price
        $eurFormatted = number_format($eurPrice, 2, '.', ',');
        
        // Return both currencies: "19.00 лв. / 9.71 €"
        return $bgnFormatted . $space . 'лв.' . ' / ' . $eurFormatted . $space . '€';
    }

    public static function formatPrice($price, $listing, $round = false, $num = 2, $th = ',', $space = '&nbsp;')
    {
        if (is_object($price)) {
            $price = $price->price ?? 0;
        }
        
        // Check if this is BGN currency and should display both currencies
        $isBGN = false;
        if (isset($listing)) {
            // Check for BGN currency by iso_code - try multiple possible locations
            if (isset($listing->iso_code) && strtoupper($listing->iso_code) === 'BGN') {
                $isBGN = true;
            } elseif (isset($listing->currency_data) && isset($listing->currency_data->iso_code) && strtoupper($listing->currency_data->iso_code) === 'BGN') {
                $isBGN = true;
            } elseif (isset($listing->currency) && strtoupper($listing->currency) === 'BGN') {
                $isBGN = true;
            } elseif (isset($listing->symbol) && strpos($listing->symbol, 'лв') !== false) {
                $isBGN = true;
            } elseif (isset($listing->currency_data) && isset($listing->currency_data->symbol) && strpos($listing->currency_data->symbol, 'лв') !== false) {
                $isBGN = true;
            }
        }
        
        // If BGN currency, use the dual currency display
        if ($isBGN) {
            return self::formatBGNWithEUR($price, $space);
        }
        
        // Original logic for other currencies
        $p = number_format( $price, ($round || (!$round && intval($price) == $price)) ? 0 : 2, '.', $th);
        // $p = number_format($price, 2, '.', '');
        $sym = isset($listing->symbol)
            ? $listing->symbol
            : (
                isset($listing->currency_data)
                    ? $listing->currency_data->symbol
                    : (isset($listing->currency) ? $listing->currency : '')
            );
        $pos = isset($listing->currency_data) ? $listing->currency_data->position : null;
        if (!$pos) $pos = isset($listing->position) ? $listing->position : '';
        return $pos == 'before'
            ? $sym . $space . $p
            : $p . $space . $sym;
    }

    public static function withSymbol($value, $details = null, $sym = '', $space = '&nbsp;')
    {
        $formatted = is_string($value) ? $value : number_format($value, 2, '.', ',');
        
        // Check if this is BGN currency and should display both currencies
        $isBGN = false;
        if (isset($details)) {
            // Check for BGN currency by iso_code - try multiple possible locations
            if (isset($details->iso_code) && strtoupper($details->iso_code) === 'BGN') {
                $isBGN = true;
            } elseif (isset($details->currency_data) && isset($details->currency_data->iso_code) && strtoupper($details->currency_data->iso_code) === 'BGN') {
                $isBGN = true;
            } elseif (isset($details->currency) && strtoupper($details->currency) === 'BGN') {
                $isBGN = true;
            } elseif (isset($details->symbol) && strpos($details->symbol, 'лв') !== false) {
                $isBGN = true;
            } elseif (isset($details->currency_data) && isset($details->currency_data->symbol) && strpos($details->currency_data->symbol, 'лв') !== false) {
                $isBGN = true;
            }
        }
        
        // If BGN currency, use the dual currency display
        if ($isBGN) {
            return self::formatBGNWithEUR($value, $space);
        }
        
        // Original logic for other currencies
        if (isset($details)) {
            if (isset($details->position) || isset($details->currency_position)) {
                $s = isset($details->symbol) || isset($details->currency_symbol) ? ($details->symbol ?? $details->currency_symbol) : $sym;
                return ($details->position ?? $details->currency_position) == 'before'
                    ? $s . $space . $formatted
                    : $formatted . $space . $s;
            }
        }
        return $formatted . $space . $sym;
    }

    public static function listingName($listing)
    {
        if (isset($listing) && is_object($listing)) {
            $name = empty($listing->name) ? $listing->nickname : $listing->name;
            if (!empty(HFY_SEO_LISTING_SLUG_FIND)) {
				$name = str_replace(HFY_SEO_LISTING_SLUG_FIND, HFY_SEO_LISTING_SLUG_REPLACE, $name);
			}
            return $name;
        }
        return '';
    }

    /**
     * Generate star SVG elements for rating display
     * 
     * @param float $rating Rating value (0-maxStars scale)
     * @param string $color Star color (default: #FF5A5F)
     * @param string $bgColor Background star color (default: #E4E5E6)
     * @param int $width SVG width (default: 120)
     * @param int $height SVG height (default: 20)
     * @param int $maxStars Maximum stars scale (default: 5)
     * @return array Array with 'background' and 'foreground' SVG elements
     */
    public static function generateStarRating($rating, $color = '#FF5A5F', $bgColor = '#E4E5E6', $width = 120, $height = 20, $maxStars = 5)
    {
        // Create star polygon - using the same structure as comments template
        $star = '<polygon points="8 11.6000001 3.29771798 14.472136 4.57619641 9.11246122 0.39154787 5.52786405 5.88397301 5.0875387 8 0 10.116027 5.0875387 15.6084521 5.52786405 11.4238036 9.11246122 12.702282 14.472136"></polygon>';
        $star5 = "<g>$star</g><g transform='translate(20)'>$star</g><g transform='translate(40)'>$star</g><g transform='translate(60)'>$star</g><g transform='translate(80)'>$star</g>";
        
        // Calculate star width using the same method as getReviewStarRating, but subtract 0.1
        $adjustedRating = max(0, $rating - 0.1); // Subtract 0.1 but don't go below 0
        $percentageWidth = self::getReviewStarRating($adjustedRating, $maxStars); // Get percentage (0-100)
        $starWidth = round($percentageWidth * $width / 100, 1); // Convert percentage to actual width
        
        // Generate background and foreground SVGs
        $background = '<svg width="' . $width . '" height="' . $height . '" viewBox="0 0 100 16"><g stroke="none" fill="' . $bgColor . '" stroke-width="0">' . $star5 . '</g></svg>';
        $foreground = '<svg width="' . $width . '" height="' . $height . '" viewBox="0 0 100 16"><g stroke="none" fill="' . $color . '" stroke-width="0">' . $star5 . '</g></svg>';
        
        return [
            'background' => $background,
            'foreground' => $foreground,
            'width' => $starWidth,
            'rating' => $rating
        ];
    }
}
