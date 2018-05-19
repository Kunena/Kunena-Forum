(function ($) {
		$.fn.doRecount = function (href, data) {
			var $this = $(this);
			var kunenaRequest = $.ajax({
				type: 'POST',
				url: href,
				data: data,
				dataType: 'json',
				context: this,
				cache: false,
				timeout: 180000 // 3 minutes
			})
				.done(function (data) {
					var $this = $(this);

					if ('header' in data) {
						$this.find('.modal-header h3').text(data.header);
					}
					if ('message' in data) {
						$this.find('.modal-body p').html(data.message);
					}
					if ('status' in data) {
						$this.find('.bar').css('width', data.status);
					}
					if ('error' in data) {
						$this.find('.modal-error').html(data.error);
					}
					if (!('success' in data && data.success)) {
						$this.find('.progress').removeClass('active').children('.bar').addClass('bar-danger');
					} else if ('status' in data && data.status === '100%') {
						$this.find('.progress').removeClass('active').children('.bar').addClass('bar-success');
					} else if ('href' in data && data.href) {
						$this.doRecount(data.href, {format: 'json'});
						return;
					} else {
						$this.find('.progress').removeClass('active').children('.bar').addClass('bar-warning');
						if (!('error' in data)) {
							$this.find('.modal-error').text(Joomla.JText._('COM_KUNENA_AJAXMODAL_ERROR_ABORT'));
						}
					}
					$this.find('.recount-close').removeAttr('disabled');
				})
				.fail(function () {
					var $this = $(this);
					if (status === 'error' && error) {
						$this.find('.modal-header h3').text(xhr.status + ' ' + error);
						$this.find('.modal-body p').text(Joomla.JText._('COM_KUNENA_AJAXMODAL_ERROR_RESPONSE_BODY'));
						$this.find('.modal-error').html(xhr.responseText);
					} else if (status === 'timeout') {
						$this.find('.modal-header h3').text(Joomla.JText._('COM_KUNENA_AJAXMODAL_ERROR_TIMEOUT_HEADER'));
						$this.find('.modal-error').text(Joomla.JText._('COM_KUNENA_AJAXMODAL_ERROR_TIMEOUT_BODY'));
					} else if (status === 'abort') {
						$this.find('.modal-header h3').text(Joomla.JText._('COM_KUNENA_AJAXMODAL_ERROR_CANCEL_HEADER'));
						$this.find('.modal-body p').text(Joomla.JText._('COM_KUNENA_AJAXMODAL_ERROR_ABORT_BODY'));
						$this.find('.modal-error').text(error);
					} else if (status === 'parsererror') {
						$this.find('.modal-header h3').text(Joomla.JText._('COM_KUNENA_AJAXMODAL_ERROR_ABORT_HEADER'));
						$this.find('.modal-body p').text(error);
						$this.find('.modal-error').html(xhr.responseText);
					} else {
						$this.find('.modal-header h3').text(Joomla.JText._('COM_KUNENA_AJAXMODAL_ERROR_UNKNOWN_HEADER'));
						$this.find('.modal-error').text(error ? error : Joomla.JText._('COM_KUNENA_AJAXMODAL_ERROR_UNKNOWN_BODY'));
					}
					$this.find('.progress').removeClass('active').children('.bar').addClass('bar-danger');
					$this.find('.recount-close').removeAttr('disabled');
				});
		};

		$(document).on('click.bs.ajaxmodal.data-api', '[data-toggle="ajaxmodal"]', function (e) {
			// Reset modal.
			var $this = $(this);
			var href = $this.attr('href');
			var $target = $($this.attr('data-target'));
			var data = $this.attr('data-form') ? $($this.attr('data-form')).serializeArray() : null;
			var option = $target.data('modal') ? 'toggle' : $.extend({}, $target.data(), $this.data());

			e.preventDefault();

			$target.find('.progress').addClass('active').find('.bar').attr('class', '').addClass('bar').css('width', '1%');
			$target.find('.modal-header h3').text(Joomla.JText._('COM_KUNENA_AJAXMODAL_START_HEADER'));
			$target.find('.modal-body p').text(Joomla.JText._('COM_KUNENA_AJAXMODAL_START_BODY'));
			$target.find('.modal-error').text('');
			$target.find('.recount-close').attr('disabled', 'disabled');

			$target
				.modal(option, this)
				.one('hide', function () {
					$this.is(':visible') && $this.focus()
				});

			data.format = 'json';
			$target.doRecount(href, data);
		});
	})(window.jQuery);