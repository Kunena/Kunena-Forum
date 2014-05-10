<?php
/**
 * Kunena Component
 * @package Kunena.Installer
 * @subpackage Template
 *
 * @copyright (C) 2008 - 2014 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
JHtml::_('behavior.framework', true);
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
	document.id('kunena-details').set('html', log);
};
window.kunenainstall = function() {
	var kunenaInstall = document.id('kunena-install');
	var kunenaProgress = document.id('kunena-progress');
	var kunenaDescription = document.id('kunena-description');
	var kunenaRequest = new Request.JSON({
		url: '<?php echo JRoute::_('index.php?option=com_kunena&view=install&task=run', false)?>',
		format: 'json',
		timeout: '180000', // 3 minutes
		noCache: true,
		secure: false,
		onRequest: function() {
			kunenaProgress.setStyle('width', '1%');
		},
		onSuccess: function(responseJSON, responseText) {
			if (responseJSON.status) {
				kunenaProgress.setStyle('width', responseJSON.status);
			}
			if (responseJSON.log) {
				window.kunenaAddItems(responseJSON.log);
			}
			if (responseJSON.success) {
				kunenaDescription.set('html', responseJSON.current);
				if (responseJSON.status != '100%') {
					window.kunenainstall();
					return;
				} else {
					kunenaInstall.getElement('h2').set('text', '<?php echo JText::_('COM_KUNENA_INSTALL_SUCCESS_MESSAGE', true); ?>');
					kunenaProgress.getParent().removeClass('active');
					kunenaProgress.addClass('bar-success');
				}
				$$('.kunena-close').removeProperty('disabled');
			} else {
				kunenaProgress.getParent().removeClass('active');
				kunenaInstall.getElement('h2').set('text', '<?php echo JText::_('COM_KUNENA_INSTALL_ERROR_MESSAGE', true); ?>');
				kunenaDescription.set('html', responseJSON.error);
				kunenaProgress.addClass('bar-danger');
				document.id('kunena-installer').removeProperty('disabled');
				document.id('kunena-container').removeClass('hidden');
			}
		},
		onError: function(responseText) {
			kunenaInstall.set('html', '<h2><?php echo JText::_('COM_KUNENA_INSTALL_ERROR_MESSAGE', true); ?></h2><div><?php echo JText::_('COM_KUNENA_INSTALL_ERROR_DETAILS', true); ?></div><div>' + responseText + '</div>');
			kunenaProgress.addClass('bar-danger');
			document.id('kunena-installer').removeProperty('disabled');
		},
		onFailure: function() {
			kunenaDescription.set('text', '<?php echo JText::_('COM_KUNENA_INSTALL_ERROR_FATAL', true); ?>');
			kunenaProgress.addClass('bar-danger');
			document.id('kunena-installer').removeProperty('disabled');
		}
	});
	kunenaRequest.post('<?php echo JSession::getFormToken(); ?>=1');
}
window.addEvent('domready', function() {
	document.id('kunena-toggle').addEvent('click', function(e) {
		document.id('kunena-container').toggleClass('hidden');
		e.preventDefault();
	});
	$$('.kunena-close').addEvent('click', function(e) {
		if (this.get('id') == 'kunena-component') window.location.href='<?php echo JRoute::_('index.php?option=com_kunena', false)?>';
		if (document.id('kunena-modal')) {
		} else if (this.get('id') == 'kunena-installer') {
			window.location.href='<?php echo JRoute::_('index.php?option=com_installer&view=install', false)?>';
		}
		e.preventDefault();
	});
	window.kunenainstall();
});
</script>
