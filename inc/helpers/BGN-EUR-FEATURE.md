# BGN to EUR Dual Currency Display Feature

## Overview
This feature automatically displays both Bulgarian Lev (BGN) and Euro (EUR) prices when a listing uses BGN as its currency. The conversion uses the central rate of 1 EUR = 1.95583 BGN and rounds to 2 decimal places.

## How it Works

### Automatic Detection
The system automatically detects BGN currency by checking for BGN in multiple possible locations in the listing or price data:
- `iso_code` field (e.g. `$listing->iso_code === 'BGN'`)
- Nested `currency_data` object (e.g. `$listing->currency_data->iso_code === 'BGN'`)
- Direct `currency` field (e.g. `$listing->currency === 'BGN'`)
- Symbol field containing 'лв' (e.g. `$listing->symbol` or `$listing->currency_data->symbol`)

This robust detection ensures that the dual currency display works regardless of how the API or data source structures the currency information.

### Display Format
When BGN currency is detected, prices are displayed in the format:
```
19.00 лв. / 9.71 €
```

### Conversion Rate
- **Central Rate**: 1 EUR = 1.95583 BGN
- **Rounding**: 2 decimal places
- **Example**: 19.00 BGN ÷ 1.95583 = 9.71 EUR

## Implementation Details

### Modified Methods
1. **`ListingHelper::formatBGNWithEUR($price, $space)`** - Converts BGN to EUR and formats the output
2. **`ListingHelper::withSymbol($value, $details, $sym, $space)`** - Detects BGN using all methods above and applies dual display
3. **`ListingHelper::formatPrice($price, $listing, ...)`** - Detects BGN using all methods above and applies dual display

### Affected Areas
The feature automatically applies to all price displays throughout the plugin, including:
- Listing cards (main, recommended, selected, map markers)
- Booking forms
- Payment previews
- Price blocks
- User booking management
- Wishlist items
- And more...

## No Configuration Needed
- The feature is fully automatic. No settings or configuration are required.
- All existing price formatting for non-BGN currencies is preserved.
- The dual display is only shown for BGN listings.

## Example Usage
```php
// Direct conversion
$price = 19.00;
$formatted = ListingHelper::formatBGNWithEUR($price);
// Result: "19.00 лв. / 9.71 €"

// With currency data object
$currencyData = (object) [
    'iso_code' => 'BGN',
    'symbol' => 'лв.',
    'position' => 'after'
];
$formatted = ListingHelper::withSymbol($price, $currencyData);
// Result: "19.00 лв. / 9.71 €"
```

## Notes
- Only affects BGN currency listings
- Other currencies display normally
- No configuration required - works automatically
- Maintains existing formatting for non-BGN currencies
- Robust detection covers all possible data structures for currency info 