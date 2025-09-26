<?php if (!defined('WPINC')) die; ?>

<div class="payment-time-left">
	<?= __('Please complete the form and submit your payment within', 'hostifybooking') ?>
	<span>10 min</span>
	<br/>
	<?= __('For your security, the session will time out after this period and you\'ll need to refresh the page to continue.', 'hostifybooking') ?>
</div>

<div class="payment-time-left-over">
	<div>
		<div>
			<?= __('For your security, this session has timed out.', 'hostifybooking') ?>
			<br/>
			<?= __('Please refresh the page to start again.', 'hostifybooking') ?>
			<br/>
			<br/>
			<a class="btn btn-primary" style="text-decoration:none;display:block" href="javascript:window.location.reload()">
				<svg viewBox="0 0 24 24" height="16" width="16" style="vertical-align:text-bottom;margin-right:16px" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 4v6h-6"></path><path d="M1 20v-6h6"></path><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10"></path><path d="M20.49 15a9 9 0 0 1-14.85 3.36L1 14"></path></svg>
				<?= __('Refresh page', 'hostifybooking') ?>
			</a>
		</div>
	</div>
</div>

<script>
jQuery(document).ready(function($){

	function paymentStartTimer(eldisp, elstop, fend, fstop)
	{
		var timer = 610, minutes, seconds;

		var intervalId = setInterval(function () {
			minutes = parseInt(timer / 60, 10)
			seconds = parseInt(timer % 60, 10);
			let txt = '';
			if (minutes < 1) {
				txt = seconds + ' sec';
			} else {
				txt = minutes + ' min';
			}
			$(eldisp).text(txt);
			if (--timer < 1) {
				clearInterval(intervalId);
				fend.call();
			}
		}, 1000);

		const x = document.querySelector(elstop);
		x.addEventListener('click', function() {
			clearInterval(intervalId);
			fstop.call();
		});
	}

	paymentStartTimer('.payment-time-left span', '.pay-btn', function(){
		$('.payment-time-left').hide();
		$('.payment-time-left-over').show();
	}, function(){
		$('.payment-time-left').hide();
	});

});
</script>
