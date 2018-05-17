jQuery(document).ready(function ($) {
	/* To hide or open collapse localStorage */
	$('.collapse').on('hidden', function () {
		if (this.id) {
			if (this.id !== 'search') {
				localStorage[this.id] = 'true';
			}
		}
	}).on('shown', function () {
		if (this.id) {
			localStorage.removeItem(this.id);
		}
	}).each(function () {
		if (this.id && localStorage[this.id] === 'true') {
			$(this).collapse('hide');
		}
	});
});
