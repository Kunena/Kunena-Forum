<?php
/**
 * Kunena Component
 * @package Kunena.Installer
 * @subpackage Template
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div id="kunena" style="max-width:530px">
	<div id="kunena-install">
		<h2><?php echo JText::_('COM_KUNENA_INSTALL_PLEASE_WAIT'); ?></h2>
		<div>
			<div id="kunena-description"><?php echo JText::_('COM_KUNENA_INSTALL_PREPARING'); ?></div>
			<div class="progress progress-striped active">
				<div id="kunena-progress" class="bar" style="width: 0%;"></div>
			</div>
		</div>
	</div>
	<div>
		<button id="kunena-toggle" class="btn" style="float: left;"><?php echo JText::_('COM_KUNENA_INSTALL_DETAILS'); ?></button>
		<div class="pull-right">
			<button id="kunena-component" class="btn kunena-close" disabled="disabled"><?php echo JText::_('COM_KUNENA_INSTALL_TO_KUNENA'); ?></button>
			<button id="kunena-installer" class="btn kunena-close" disabled="disabled" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('COM_KUNENA_INSTALL_CLOSE'); ?></button>
		</div>
		<div id="kunena-container" class="hidden">
			<p class="clr clearfix"></p>
			<div id="kunena-details" class="well well-small"><h4><?php echo JText::_('COM_KUNENA_INSTALL_DETAILS'); ?></h4><div><?php echo JText::_('COM_KUNENA_INSTALL_PREPARING'); ?></div></div>
		</div>
	</div>
</div>
<script>
window.kunenaAddItems = function(log) {
	jQuery('#kunena-details').html(log);
};
window.kunenainstall = function() {
	var kunenaInstall = jQuery('#kunena-install');
	var kunenaProgress = jQuery('#kunena-progress');
	var kunenaDescription = jQuery('#kunena-description');

	jQuery.ajax({
		type: 'POST',
		dataType: 'json',
		timeout: '180000', // 3 minutes
		url: '<?php echo JRoute::_('index.php?option=com_kunena&view=install&task=run', false)?>',
		data: '<?php echo JSession::getFormToken(); ?>=1',
		cache: false,
		error: function (xhr, ajaxOptions, thrownError) {
			kunenaInstall.html('<h2><?php echo JText::_('COM_KUNENA_INSTALL_ERROR_MESSAGE', true); ?></h2><div><?php echo JText::_('COM_KUNENA_INSTALL_ERROR_DETAILS', true); ?></div><div>' + xhr.responseText + '</div>');
			kunenaProgress.addClass('bar-danger');
			jQuery('#kunena-installer').show();
		},
		beforeSend: function () {
			kunenaProgress.css('width', '1%');
		},
		complete: function () {

		},
		success: function (json) {
			if (json.status) {
				kunenaProgress.css('width', json.status);
			}
			if (json.log) {
				window.kunenaAddItems(json.log);
			}
			if (json.success) {
				kunenaDescription.html(json.current);
				if (json.status != '100%') {
					window.kunenainstall();
					return;
				} else {
					kunenaInstall.find('h2').text('<?php echo JText::_('COM_KUNENA_INSTALL_SUCCESS_MESSAGE', true); ?>');
					kunenaProgress.parent().removeClass('active');
					kunenaProgress.addClass('bar-success');
				}
				jQuery('.kunena-close').removeAttr('disabled');
			} else {
				kunenaProgress.parent().removeClass('active');
				kunenaInstall.find('h2').text('<?php echo JText::_('COM_KUNENA_INSTALL_ERROR_MESSAGE', true); ?>');
				kunenaDescription.html(json.error);
				kunenaProgress.addClass('bar-danger');
				jQuery('#kunena-installer').removeAttr('disabled');
				jQuery('#kunena-container').removeClass('hidden');
			}
		}
	});


}
jQuery( document ).ready(function() {
	jQuery('#kunena-toggle').click(function(e) {
		jQuery('#kunena-container').toggleClass('hidden');
		e.preventDefault();
	});
	jQuery('#kunena-component').click(function(e) {
		window.location.href='<?php echo JRoute::_('index.php?option=com_kunena', false)?>';
		e.preventDefault();
	});
	jQuery('#kunena-installer').click(function(e) {
		window.location.href='#Close';
		e.preventDefault();
	});
	window.kunenainstall();
});
</script>
