
There was a new inquiry for <?=$listingNickname?>
<br/>
<br/>
Listing title: <?=$listingName?>
<br/>
Name: <?=$first_name?> <?=$last_name?>
<br/>
Email: <?=$email?>
<br/>
Phone:  <?=$phone?>
<br/>
Check In:  <?=$check_in?>
<br/>
Check Out:  <?=$check_out?>
<br/>
Adults:  <?=$adults?>
<br/>
<?php if($children):?>
Children: <?=$children?>
<br/>
<?php endif;?>
<?php if($infants):?>
Infants: <?=$infants?>
<br/>
<?php endif;?>
<?php if($pets):?>
Pets: <?=$pets?>
<br/>
<?php endif;?>
<?php if($discount_code):?>
Discount Code: <?=$discount_code?>
<br/>
<?php endif;?>
<?php if($booking_reference):?>
Booking Reference: <?=$booking_reference?>
<br/>
<?php endif;?>
Message:  <?=$message?>
<br/>
