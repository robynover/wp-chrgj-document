jQuery(document).ready(function($) {

	$('.datepicker').datepicker({
		duration: 'fast',
		dateFormat: 'yy-mm-dd',
		changeMonth: true,
		changeYear: true
	});

	$('.datepicker').example('0000-00-00');
});