<?php
/**
 * Kunena Component
 * @package Kunena.Installer
 * @subpackage Template
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
JHtml::_('behavior.framework', true);
?>
<style>
.progress {
	background-color: #F7F7F7;
	background-image: linear-gradient(to bottom, #F5F5F5, #F9F9F9);
	background-repeat: repeat-x;
	border-radius: 4px 4px 4px 4px;
	box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1) inset;
	height: 18px;
	margin-bottom: 18px;
	overflow: hidden;
}
.progress .bar {
	-moz-box-sizing: border-box;
	background-color: #0E90D2;
	background-image: linear-gradient(to bottom, #149BDF, #0480BE);
	background-repeat: repeat-x;
	box-shadow: 0 -1px 0 rgba(0, 0, 0, 0.15) inset;
	color: #FFFFFF;
	float: left;
	font-size: 12px;
	height: 100%;
	text-align: center;
	text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
	transition: width 0.6s ease 0s;
	width: 0;
}
.progress-striped .bar {
	background-color: #149BDF;
	background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
	background-size: 40px 40px;
}
</style>

<div id="kunena-install" style="max-width:600px">
	<h2>Installing Kunena, please wait...</h2>
	<div class="progress progress-striped active">
		<div id="kunena-progress" class="bar" style="width: 0%;"></div>
	</div>
	<div id="kunena-description">Preparing installation...</div>
</div>
<script>
window.kunenainstall = function() {
	var kunenaInstall = document.id('kunena-install');
	var kunenaProgress = document.id('kunena-progress');
	var kunenaDescription = document.id('kunena-description');
	var kunenaRequest = new Request.JSON({
		url: 'index.php?option=com_kunena&view=install&task=run',
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
			if (responseJSON.success) {
				kunenaDescription.set('html', responseJSON.html);
				if (responseJSON.status != '100%') {
					window.kunenainstall();
					return;
				} else {
					kunenaProgress.getParent().removeClass('active');
				}
			} else {
				if (window.parent.SqueezeBox) {
					window.parent.SqueezeBox.resize({x: 600, y: 500}, true);
					window.parent.SqueezeBox.asset.set('width', 600).set('height', 500);
				}
				kunenaInstall.set('html', '<h2>Kunena Installation Failed!</h2><div>Sorry, installation failed with following error:</div><div style="border:3px solid red;margin: 10px 10px 10px 0px;padding:10px;">' + responseJSON.html + '</div>');
			}
			if (window.parent.SqueezeBox) {
				window.parent.SqueezeBox.closeBtn.removeProperty('style');
				window.parent.SqueezeBox.options.closable = true;
			}
		},
		onError: function(responseText) {
			if (window.parent.SqueezeBox) {
				window.parent.SqueezeBox.closeBtn.removeProperty('style');
				window.parent.SqueezeBox.options.closable = true;
				window.parent.SqueezeBox.resize({x: 600, y: 500}, true);
				window.parent.SqueezeBox.asset.set('width', 600).set('height', 500);
			}
			kunenaInstall.set('html', '<h2>Kunena Installation Failed!</h2><div>Sorry, installation failed with following error:</div><div style="border:3px solid red;margin: 10px 10px 10px 0px;padding:10px;">' + responseText + '</div>');
		},
		onFailure: function() {
			if (window.parent.SqueezeBox) {
				window.parent.SqueezeBox.closeBtn.removeProperty('style');
				window.parent.SqueezeBox.options.closable = true;
			}
			kunenaDescription.set('text', 'Sorry, installation failed on network error!');
		}
	});
	kunenaRequest.post('<?php echo JSession::getFormToken(); ?>=1');
}
window.addEvent('domready', function() {
	window.kunenainstall();
});
</script>
