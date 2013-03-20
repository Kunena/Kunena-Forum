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

if (version_compare(JVERSION, '3.0', '<')) : ?>
<style>
#kunena h4 {
	font-size: 14px;
	line-height: 16px;
}
#kunena h2, h4 {
	margin: 12px 0;
}
#kunena .btn {
	-moz-border-bottom-colors: none;
	-moz-border-left-colors: none;
	-moz-border-right-colors: none;
	-moz-border-top-colors: none;
	background-color: #F5F5F5;
	background-image: linear-gradient(to bottom, #FFFFFF, #E6E6E6);
	background-repeat: repeat-x;
	border-color: #BBBBBB #BBBBBB #A2A2A2;
	border-image: none;
	border-radius: 4px 4px 4px 4px;
	border-style: solid;
	border-width: 1px;
	box-shadow: 0 1px 0 rgba(255, 255, 255, 0.2) inset, 0 1px 2px rgba(0, 0, 0, 0.05);
	color: #333333;
	cursor: pointer;
	display: inline-block;
	font-size: 13px;
	line-height: 18px;
	margin-bottom: 0;
	padding: 4px 14px;
	text-align: center;
	text-shadow: 0 1px 1px rgba(255, 255, 255, 0.75);
	vertical-align: middle;
}
#kunena .btn.disabled, .btn[disabled] {
	background-color: #E6E6E6;
	background-image: none;
	box-shadow: none;
	cursor: default;
	opacity: 0.65;
}
#kunena .progress {
	background-color: #F7F7F7;
	background-image: linear-gradient(to bottom, #F5F5F5, #F9F9F9);
	background-repeat: repeat-x;
	border-radius: 4px 4px 4px 4px;
	box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1) inset;
	height: 18px;
	margin-bottom: 18px;
	overflow: hidden;
}
#kunena .progress .bar {
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
#kunena .progress-striped .bar {
	background-color: #149BDF;
	background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
	background-size: 40px 40px;
}
#kunena .well {
	background-color: #F5F5F5;
	border: 1px solid #E3E3E3;
	border-radius: 4px 4px 4px 4px;
	box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05) inset;
	margin-bottom: 20px;
	min-height: 20px;
	padding: 19px;
}
#kunena .well-small {
	border-radius: 3px 3px 3px 3px;
	padding: 9px;
}
#kunena .pull-right {
	float: right;
}
#kunena .hidden {
	display: none;
	visibility: hidden;
}
</style>
<?php endif; ?>

<div id="kunena" style="max-width:530px">
	<div id="kunena-install">
		<h2>Installing Kunena, please wait...</h2>
		<div>
			<div id="kunena-description">Preparing installation...</div>
			<div class="progress progress-striped active">
				<div id="kunena-progress" class="bar" style="width: 0%;"></div>
			</div>
		</div>
	</div>
	<div>
		<button id="kunena-toggle" class="btn" style="float: left;">Details</button>
		<div class="pull-right">
			<button id="kunena-component" class="btn kunena-close" disabled="disabled">To Kunena</button>
			<button id="kunena-installer" class="btn kunena-close" disabled="disabled" data-dismiss="modal" aria-hidden="true">Close</button>
		</div>
		<div id="kunena-container" class="hidden">
			<p class="clr clearfix"></p>
			<div id="kunena-details" class="well well-small"><h4>Installation Details</h4><div>Preparing installation...</div></div>
		</div>
	</div>
</div>
<script>
window.kunenaAddItems = function(html) {
	var el = new Element('div', {html: html});
	document.id('kunena-details').grab(el);
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
			if (window.parent.SqueezeBox) {
				window.parent.SqueezeBox.closeBtn.setStyle('display','none');
				window.parent.SqueezeBox.asset.set('scrolling', 'no');
			}
			kunenaProgress.setStyle('width', '1%');
		},
		onSuccess: function(responseJSON, responseText) {
			if (responseJSON.status) {
				kunenaProgress.setStyle('width', responseJSON.status);
			}
			if (responseJSON.html) {
				window.kunenaAddItems(responseJSON.html);
			}
			if (responseJSON.success) {
				kunenaDescription.set('html', responseJSON.html);
				if (responseJSON.status != '100%') {
					window.kunenainstall();
					return;
				} else {
					kunenaInstall.getElement('h2').set('text', 'Kunena has been successfully installed!')
					kunenaProgress.getParent().removeClass('active');
				}
			} else {
				if (window.parent.SqueezeBox) {
					window.parent.SqueezeBox.resize({y: 500}, true);
					window.parent.SqueezeBox.asset.set('height', 500).set('scrolling', 'auto');
				}
				kunenaInstall.set('html', '<h2>Kunena Installation Failed!</h2><div>Sorry, installation failed with following error:</div><div style="border:3px solid red;margin: 10px 10px 10px 0px;padding:10px;">' + responseJSON.html + '</div>');
			}
			$$('.kunena-close').removeProperty('disabled');
		},
		onError: function(responseText) {
			if (window.parent.SqueezeBox) {
				window.parent.SqueezeBox.resize({y: 500}, true);
				window.parent.SqueezeBox.asset.set('height', 500).set('scrolling', 'auto');
			}
			kunenaInstall.set('html', '<h2>Kunena Installation Failed!</h2><div>Sorry, installation failed with following error:</div><div style="border:3px solid red;margin: 10px 10px 10px 0px;padding:10px;">' + responseText + '</div>');
			$$('.kunena-close').removeProperty('disabled');
		},
		onFailure: function() {
			kunenaDescription.set('text', 'Sorry, installation failed on network error!');
			$$('.kunena-close').removeProperty('disabled');
		}
	});
	kunenaRequest.post('<?php echo JSession::getFormToken(); ?>=1');
}
window.addEvent('domready', function() {
	document.id('kunena-toggle').addEvent('click', function(e) {
		document.id('kunena-container').toggleClass('hidden');
		if (window.parent.SqueezeBox) {
			var height = document.id('kunena-container').hasClass('hidden') ? 140 : 500;
			window.parent.SqueezeBox.resize({y: height}, true);
			window.parent.SqueezeBox.asset.set('height', height).set('scrolling', height==140 ? 'no' : 'auto');
		}
		e.preventDefault();
	});
	$$('.kunena-close').addEvent('click', function(e) {
		var win = window.parent.SqueezeBox ? window.parent : window;
		if (this.get('id') == 'kunena-component') win.location.href='<?php echo JRoute::_('index.php?option=com_kunena', false)?>';
		if (window.parent.SqueezeBox) {
			window.parent.SqueezeBox.close();
		} else if (document.id('kunena-modal')) {
		} else if (this.get('id') == 'kunena-installer') {
			win.location.href='<?php echo JRoute::_('index.php?option=com_installer&view=install', false)?>';
		}
		e.preventDefault();
	});
	window.kunenainstall();
});
</script>
