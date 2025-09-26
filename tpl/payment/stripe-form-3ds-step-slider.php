<?php
if (!defined('WPINC')) die;

$stepActive = (int) ($stepActive ?? 1);

$stepPrevIcon = '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.6667 4.66797L6 11.3413L4 9.3413" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';

?>

<div class="slider-steps">
	<?php
	for ($step = 1; $step <= $sliderStepsCount; $step++):
		if ($step < $stepActive) {
			?><div><?= $stepPrevIcon ?></div><?php
		} else if ($step == $stepActive) {
			?><div class="active"><?= $step ?></div><?php
		} else {
			?><div><?= $step ?></div><?php
		}
	endfor;
	?>
</div>

