<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/

defined( '_JEXEC' ) or die();

$kunena_config = KunenaFactory::getConfig ();
require_once (JPATH_COMPONENT . DS . 'lib' .DS. 'kunena.poll.class.php');
$kunena_poll =& CKunenaPolls::getInstance();
$document =& JFactory::getDocument();

ob_start();

//
// function kPreviewHelper (elementId)
//
// Helper function for to perform JSON request for preview
//
?>
function kPreviewHelper()
{
	if (_previewActive == true){
		previewRequest = new Request.JSON({url: "<?php echo CKunenaLink::GetJsonURL('preview');?>",
				  							onSuccess: function(response){
			message = $("kbbcode-preview");
			if (message) {
				message.set("html", response.preview);
			}
			}}).post({body: $("kbbcode-message").get("value")
		});
	}
}

<?php
// Now we instanciate the class in an object and implement all the buttons and functions.
?>
window.addEvent('domready', function() {
<?php
//creating a kbbcode object
?>
kbbcode = new kbbcode('kbbcode-message', 'kbbcode-toolbar', {
				dispatchChangeEvent: true,
				changeEventDelay: 1000,
				interceptTab: true
			});

kbbcode.addFunction('Bold', function() {
	this.replaceSelection('[b]' + this.getSelection() + '[/b]');
}, {'id': 'kbbcode-bold-button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_BOLD');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_BOLD');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_BOLD');?>")'});

kbbcode.addFunction('Italic', function() {
	this.replaceSelection('[i]' + this.getSelection() + '[/i]');
}, {'id': 'kbbcode-italic-button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_ITALIC');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_ITALIC');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_ITALIC');?>")'});

kbbcode.addFunction('Underline', function() {
	this.replaceSelection('[u]' + this.getSelection() + '[/u]');
}, {'id': 'kbbcode-underline-button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_UNDERL');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_UNDERL');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_UNDERL');?>")'});

kbbcode.addFunction('Strike', function() {
	this.replaceSelection('[strike]' + this.getSelection() + '[/strike]');
}, {'id': 'kbbcode-strike-button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_STRIKE');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_STRIKE');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_STRIKE');?>")'});

kbbcode.addFunction('Sub', function() {
	this.replaceSelection('[sub]' + this.getSelection() + '[/sub]');
}, {'id': 'kbbcode-sub-button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_SUB');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_SUB');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_SUB');?>")'});

kbbcode.addFunction('Sup', function() {
	this.replaceSelection('[sup]' + this.getSelection() + '[/sup]');
}, {'id': 'kbbcode-sup-button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_SUP');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_SUP');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_SUP');?>")'});

kbbcode.addFunction('Size', function() {
	kToggleOrSwap("kbbcode-size-options");
}, {'id': 'kbbcode-size-button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_FONTSIZE');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_FONTSIZE');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_FONTSIZE');?>")'});

kbbcode.addFunction('Color', function() {
	kToggleOrSwap("kbbcode-colorpalette");
}, {'id': 'kbbcode-color-button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_COLOR');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_COLOR');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_COLOR');?>")'});

kbbcode.addFunction('#', function() {
}, {'id': 'kbbcode-separator1'});

<?php
//adding a "List" button that will create a new list if nothing is selected
//or make each line of the selection into a list item if some text is selected...
 ?>
kbbcode.addFunction("uList", function() {
	selection = this.getSelection();
	if (selection == "") {
		this.replaceSelection("\n[ul]\n   [li]Item 1[/li]\n   [li]Item 2[/li]\n   [li]...[/li]\n[/ul]");
	}
	else {
		this.processEachLine(function(line) {
			newline = "  [li]" + line + "[/li]";
			return newline;
		}, false);
		this.insert("[ul]\n", "before", false);
		this.insert("\n[/ul]", "after", true); //now isLast is set to true, because it is the last one!
	}
}, {'id': 'kbbcode-ulist-button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_ULIST');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_ULIST');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_ULIST');?>")'});

kbbcode.addFunction("oList", function() {
	selection = this.getSelection();
	if (selection == "") {
		this.replaceSelection("\n[ol]\n   [li]Item 1[/li]\n   [li]Item 2[/li]\n   [li]...[/li]\n[/ol]");
	}
	else {
		this.processEachLine(function(line) {
			newline = "  [li]" + line + "[/li]";
			return newline;
		}, false);
		this.insert("[ol]\n", "before", false);
		this.insert("\n[/ol]", "after", true); //now isLast is set to true, because it is the last one!
	}
}, {'id': 'kbbcode-olist-button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_OLIST');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_OLIST');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_OLIST');?>")'});

kbbcode.addFunction('List', function() {
	this.replaceSelection('[li]' + this.getSelection() + '[/li]');
}, {'id': 'kbbcode-list-button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_LIST');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_LIST');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_LIST');?>")'});

kbbcode.addFunction('Left', function() {
	this.replaceSelection('[left]' + this.getSelection() + '[/left]');
}, {'id': 'kbbcode-left-button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_LEFT');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_LEFT');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_LEFT');?>")'});

kbbcode.addFunction('Center', function() {
	this.replaceSelection('[center]' + this.getSelection() + '[/center]');
}, {'id': 'kbbcode-center-button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_CENTER');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_CENTER');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_CENTER');?>")'});

kbbcode.addFunction('Right', function() {
	this.replaceSelection('[right]' + this.getSelection() + '[/right]');
}, {'id': 'kbbcode-right-button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_RIGHT');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_RIGHT');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_RIGHT');?>")'});

kbbcode.addFunction('#', function() {
}, {'id': 'kbbcode-separator2'});

kbbcode.addFunction('Quote', function() {
	this.replaceSelection('[quote]' + this.getSelection() + '[/quote]');
}, {'id': 'kbbcode-quote-button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_QUOTE');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_QUOTE');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_QUOTE');?>")'});

<?php
if ($kunena_config->highlightcode) { ?>
kbbcode.addFunction('Code', function() {
	kToggleOrSwap("kbbcode-code-options");
}, {'id': 'kbbcode-code-button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_CODE');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_CODE');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_CODE');?>")'});
<?php
} else {
?>
kbbcode.addFunction('Code', function() {
	this.replaceSelection('[code]' + this.getSelection() + '[/code]');
}, {'id': 'kbbcode-code-button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_CODE');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_CODE');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_CODE');?>")'});
<?php
}
?>

kbbcode.addFunction("Table", function() {
	selection = this.getSelection();
	if (selection == "") {
		this.replaceSelection("\n[table]\n  [tr]\n    [td]Line 1[/td]\n    [td]Column 2[/td]\n  [/tr]\n  [tr]\n    [td]Line 2[/td]\n    [td]...[/td]\n  [/tr]\n[/table]");
	}
	else {
		this.processEachLine(function(line) {
			newline = "  [tr][td]" + line + "[/td][/tr]";
			return newline;
		}, false);
		this.insert("[table]\n", "before", false);
		this.insert("\n[/table]", "after", true); //now isLast is set to true, because it is the last one!
	}
}, {'id': 'kbbcode-table-button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_TABLE');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_TABLE');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_TABLE');?>")'});

<?php
if ($kunena_config->showspoilertag) {
?>
kbbcode.addFunction('Spoiler', function() {
	this.replaceSelection('[spoiler]' + this.getSelection() + '[/spoiler]');
}, {'id': 'kbbcode-spoiler-button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_SPOILER');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_SPOILER');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_SPOILER');?>")'});
<?php
}
?>

kbbcode.addFunction('Hide', function() {
	this.replaceSelection('[hide]' + this.getSelection() + '[/hide]');
}, {'id': 'kbbcode-hide-button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_HIDE');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_HIDE');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_HIDE');?>")'});

kbbcode.addFunction('#', function() {
}, {'id': 'kbbcode-separator3'});

kbbcode.addFunction('Image', function() {
	kToggleOrSwap("kbbcode-image-options");
}, {'id': 'kbbcode-image-button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_IMAGELINK');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_IMAGELINK');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_IMAGELINK');?>")'});

kbbcode.addFunction('Link', function() {
	sel = this.getSelection();
	if (sel != "") {
		$('kbbcode-link_text').set('value', sel);
	}
	kToggleOrSwap("kbbcode-link-options");
}, {'id': 'kbbcode-link-button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_LINK');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_LINK');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_LINK');?>")'});

kbbcode.addFunction('#', function() {
}, {'id': 'kbbcode-separator4'});
<?php
/*
kbbcode.addFunction('Gallery', function() {
	kToggleOrSwap("kbbcode-gallery-options");
}, {'id': 'kbbcode-gallery-button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_GALLERY');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_GALLERY');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_GALLERY');?>")'});
*/
?>
<?php if (!isset($this->msg_cat->allow_polls)) $this->msg_cat->allow_polls = ''; //display only the poll icon in the first message of the thread

$poll_allowed = $kunena_poll->get_poll_allowed($this->id, $this->parent, $this->kunena_editmode, $this->msg_cat->allow_polls);

if( $poll_allowed ){ ?>

kbbcode.addFunction('Poll', function() {
	kToggleOrSwap("kbbcode-poll-options");
}, {'id': 'kbbcode-poll-button',
<?php
if ($this->msg_cat->allow_polls == '0' || empty($this->msg_cat->allow_polls)) {
	echo '\'style\':\'display: none;\',';
} ?>
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_POLL');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_POLL');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_POLL');?>")'});

kbbcode.addFunction('#', function() {
}, {'id': 'kbbcode-separator5'
<?php
if ($this->msg_cat->allow_polls == '0' || empty($this->msg_cat->allow_polls)) {
	echo ',\'style\':\'display: none;\'';
} ?>
});

<?php
}

if ($kunena_config->showebaytag) {
?>
kbbcode.addFunction('eBay', function() {
	this.replaceSelection('[ebay]' + this.getSelection() + '[/ebay]');
}, {'id': 'kbbcode-ebay-button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_EBAY');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_EBAY');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_EBAY');?>")'});
<?php
}
?>

<?php
if ($kunena_config->showvideotag) {
?>
kbbcode.addFunction('Video', function() {
	kToggleOrSwap("kbbcode-video-options");
}, {'id': 'kbbcode-video-button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_VIDEO');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_VIDEO');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_VIDEO');?>")'});
<?php
}
?>

kbbcode.addFunction('Map', function() {
	kToggleOrSwap("kbbcode-map-options");
}, {'id': 'kbbcode-map-button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_MAP');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_MAP');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_MAP');?>")'});

kbbcode.addFunction('Module', function() {
	this.replaceSelection('[module]' + this.getSelection() + '[/module]');
}, {'id': 'kbbcode-module-button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_MODULE');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_MODULE');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_MODULE');?>")'});

kbbcode.addFunction('#', function() {
}, {'id': 'kbbcode-separator6'});

kbbcode.addFunction('PreviewBottom', function() {
	kToggleOrSwapPreview("kbbcode-preview-bottom");
}, {'id': 'kbbcode-previewbottom-button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_PREVIEWBOTTOM');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_PREVIEWBOTTOM');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_PREVIEWBOTTOM');?>")'});

kbbcode.addFunction('PreviewRight', function() {
	kToggleOrSwapPreview("kbbcode-preview-right");
}, {'id': 'kbbcode-previewright-button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_PREVIEWRIGHT');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_PREVIEWRIGHT');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_PREVIEWRIGHT');?>")'});

kbbcode.addFunction('#', function() {
}, {'id': 'kbbcode-separator7'});

kbbcode.addFunction('Help', function() {
	window.open('http://docs.kunena.com/index.php/bbcode');
}, {'id': 'kbbcode-help-button',
	'title': '<?php echo JText::_('COM_KUNENA_EDITOR_HELP');?>',
	'alt': '<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_HELP');?>',
	'onmouseover' : '$("helpbox").set("value", "<?php echo JText::_('COM_KUNENA_EDITOR_HELPLINE_HELP');?>")'});

$('kbbcode-message').addEvent('change', function(){
	kPreviewHelper();
});

<?php
// Add the click behaviors for our bbcode options
?>
	var color = $$("table.kbbcode-colortable td");
	if (color) {
		color.addEvent("click", function(){
			var bg = this.getStyle( "background-color" );
			selection = kbbcode.getSelection();
			kbbcode.replaceSelection('[color='+ bg +']' + selection + '[/color]');
			kToggleOrSwap("kbbcode-colorpalette");
		});
	}
	var size = $$("div#kbbcode-size-options span");
	if (size) {
		size.addEvent("click", function(){
			var tag = this.get( "title" );
			selection = kbbcode.getSelection();
			kbbcode.replaceSelection(tag + selection + '[/size]');
			kToggleOrSwap("kbbcode-size-options");
		});
	}

	newAttachment();
	//This is need to retrieve the video provider selected by the user in the dropdownlist
	$('kvideoprovider').addEvent('change', function() {
		var sel = $$('#kvideoprovider option:selected');
		sel.each(function(el) {
			$('kvideoprovider').store('videoprov',el.value);
		});
	});
});

<?php
/* Plupload hooks: promising piece of software, but not ready for production use */
/*
window.addEvent('domready', function() {
	kupload = $('kupload');
	if (typeof(plupload) == 'object' && kupload) {
		var uploader = new plupload.Uploader({
			//runtimes : 'gears,silverlight,flash,html5,html4',
			runtimes : 'flash,html4',
			browse-button : 'kupload',
			max_file_size : '4mb',
			url : '<?php echo CKunenaLink::GetJsonURL('uploadfile','upload', false);?>',
			//resize : {width : 320, height : 240, quality : 90},
			flash_swf_url : '<?php echo KUNENA_DIRECTURL;?>/js/plupload/plupload.flash.swf',
			silverlight_xap_url : '<?php echo KUNENA_DIRECTURL;?>/js/plupload/plupload.silverlight.xap',
			filters : [
				{title : "Image files", extensions : "jpg,gif,png"},
				{title : "Zip files", extensions : "zip,gz"}
			],
			multipart_params : [
				{type: 'hidden', name: 'mesid', value: '0'}
			]
		});

		uploader.bind('Init', function(up, params) {
			$('kattachmentsnote').set('html', "<div>Multi-File Upload enabled: " + params.runtime + "</div>");
		});

		uploader.bind('FilesAdded', function(up, files) {
			$each(files, function(file, i) {
				fileDiv = new Element('div', {id: file.id, html: file.name + ' (' + plupload.formatSize(file.size) + ') <a></a> <b></b>'});
				fileDiv.inject($('kattachment'), 'before');
				$$('#'+file.id+' a').addEvent('click', function(e) { $(file.id).dispose(); uploader.removeFile(file); return false;});
			});
			$('kupload').fireEvent('upload', null, 500);
		});

		uploader.bind('UploadProgress', function(up, file) {
			$$("#" + file.id + " b").set('html', file.percent + "%");
		});

		uploader.bind('FileUploaded', function(up, file) {
			$$("#" + file.id + " b").set('html', file.response);
		});

		kupload.addEvent('upload', function() {
			uploader.start();
		});

		kupload.setProperty('value', '');
		uploader.init();
	}
});
*/
?>

<?php
$script = ob_get_contents();
ob_end_clean();

CKunenaTools::addScript(KUNENA_DIRECTURL . '/template/default/js/editor-min.js');
$document->addScriptDeclaration($script);
