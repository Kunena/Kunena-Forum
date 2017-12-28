<?php
/**
 * Kunena Component
 * @package     Kunena.Administrator.Template
 * @subpackage  SyncUsers
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die();

// @var KunenaAdminViewTools $this

JText::script('COM_KUNENA_MODAL_CLOSE');
JText::script('COM_KUNENA_AJAXMODAL_START_HEADER');
JText::script('COM_KUNENA_AJAXMODAL_START_BODY');
JText::script('COM_KUNENA_AJAXMODAL_ERROR_UNKNOWN_HEADER');
JText::script('COM_KUNENA_AJAXMODAL_ERROR_RESPONSE_BODY');
JText::script('COM_KUNENA_AJAXMODAL_ERROR_TIMEOUT_HEADER');
JText::script('COM_KUNENA_AJAXMODAL_ERROR_TIMEOUT_BODY');
JText::script('COM_KUNENA_AJAXMODAL_ERROR_CANCEL_HEADER');
JText::script('COM_KUNENA_AJAXMODAL_ERROR_ABORT_BODY');
JText::script('COM_KUNENA_AJAXMODAL_ERROR_ABORT_HEADER');
JText::script('COM_KUNENA_AJAXMODAL_ERROR_UNKNOWN_HEADER');
JText::script('COM_KUNENA_AJAXMODAL_ERROR_UNKNOWN_BODY');
?>

<div id="kunena" class="admin override">
	<div id="j-sidebar-container" class="span2">
		<div id="sidebar">
			<div class="sidebar-nav"><?php include KPATH_ADMIN . '/template/common/menu.php'; ?></div>
		</div>
	</div>
	<div id="j-main-container" class="span10">
		<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post" id="adminForm" name="adminForm">
			<input type="hidden" name="task" value="recount" />
			<?php echo JHtml::_('form.token'); ?>

			<fieldset>
				<legend><?php echo JText::_('COM_KUNENA_A_RECOUNT'); ?></legend>
				<table class="table table-bordered table-striped">
					<tr>
						<td><?php echo JText::_('COM_KUNENA_A_RECOUNT_TOPICS'); ?></td>
						<td><input type="checkbox" checked="checked" name="topics" value="1" /></td>
						<td><?php echo JText::_('COM_KUNENA_A_RECOUNT_TOPICS_DESC'); ?></td>
					</tr>
					<tr>
						<td><?php echo JText::_('COM_KUNENA_A_RECOUNT_USERTOPICS'); ?></td>
						<td><input type="checkbox" checked="checked" name="usertopics" value="1" /></td>
						<td><?php echo JText::_('COM_KUNENA_A_RECOUNT_USERTOPICS_DESC'); ?></td>
					</tr>
					<tr>
						<td><?php echo JText::_('COM_KUNENA_A_RECOUNT_CATEGORIES'); ?></td>
						<td><input type="checkbox" checked="checked" name="categories" value="1" /></td>
						<td><?php echo JText::_('COM_KUNENA_A_RECOUNT_CATEGORIES_DESC'); ?></td>
					</tr>
					<tr>
						<td><?php echo JText::_('COM_KUNENA_A_RECOUNT_USERS'); ?></td>
						<td><input type="checkbox" checked="checked" name="users" value="1" /></td>
						<td><?php echo JText::_('COM_KUNENA_A_RECOUNT_USERS_DESC'); ?></td>
					</tr>
				</table>
			</fieldset>
		</form>
	</div>

	<div class="pull-right small">
		<?php echo KunenaVersion::getLongVersionHTML(); ?>
	</div>
</div>

<!-- Modal -->
<div id="recountModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="recountModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
	<div class="modal-header">
		<button type="button" class="close recount-close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3></h3>
	</div>
	<div class="modal-body">
		<p></p>
		<div class="progress progress-striped">
			<div class="bar"></div>
		</div>
		<div class="modal-error"></div>
	</div>
	<div class="modal-footer">
		<button class="btn recount-close" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('COM_KUNENA_MODAL_CLOSE'); ?></button>
	</div>
</div>

<script>
	(function($) {
		$.fn.doRecount = function(href, data) {
			var $this = $(this);
			var kunenaRequest = $.ajax({
				type: 'POST',
				url: href,
				data: data,
				dataType: 'json',
				context: this,
				cache: false,
				timeout: 180000, // 3 minutes
				success: function(data, status) {
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
					} else if ('status' in data && data.status == '100%') {
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
				},
				error: function(xhr, status, error) {
					var $this = $(this);
					if (status == 'error' && error) {
						$this.find('.modal-header h3').text(xhr.status + ' ' + error);
						$this.find('.modal-body p').text(Joomla.JText._('COM_KUNENA_AJAXMODAL_ERROR_RESPONSE_BODY'));
						$this.find('.modal-error').html(xhr.responseText);
					} else if (status == 'timeout') {
						$this.find('.modal-header h3').text(Joomla.JText._('COM_KUNENA_AJAXMODAL_ERROR_TIMEOUT_HEADER'));
						$this.find('.modal-error').text(Joomla.JText._('COM_KUNENA_AJAXMODAL_ERROR_TIMEOUT_BODY'));
					} else if (status == 'abort') {
						$this.find('.modal-header h3').text(Joomla.JText._('COM_KUNENA_AJAXMODAL_ERROR_CANCEL_HEADER'));
						$this.find('.modal-body p').text(Joomla.JText._('COM_KUNENA_AJAXMODAL_ERROR_ABORT_BODY'));
						$this.find('.modal-error').text(error);
					} else if (status == 'parsererror') {
						$this.find('.modal-header h3').text(Joomla.JText._('COM_KUNENA_AJAXMODAL_ERROR_ABORT_HEADER'));
						$this.find('.modal-body p').text(error);
						$this.find('.modal-error').html(xhr.responseText);
					} else {
						$this.find('.modal-header h3').text(Joomla.JText._('COM_KUNENA_AJAXMODAL_ERROR_UNKNOWN_HEADER'));
						$this.find('.modal-error').text(error ? error : Joomla.JText._('COM_KUNENA_AJAXMODAL_ERROR_UNKNOWN_BODY'));
					}
					$this.find('.progress').removeClass('active').children('.bar').addClass('bar-danger');
					$this.find('.recount-close').removeAttr('disabled');
				}
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

			data.format ='json';
			$target.doRecount(href, data);
		});
	})(window.jQuery);
</script>
