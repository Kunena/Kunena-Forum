<?php 
/**
 * @version $Id$
 * Kunena Translate Component
 * 
 * @package	Kunena Translate
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 */
defined('_JEXEC') or die('Restricted access');
if(JRequest::getVar('task') == 'import'  || 
	JRequest::getVar('show') == 'import') 
	JToolBarHelper::save('import', 'Import');
else
	JToolBarHelper::save('export', 'Export');
JToolBarHelper::cancel();
?>
<script type="text/javascript">
<!--
window.addEvent( 'domready' , function() {
	var ext = document.getElementById('extension');
	var clientchoose = document.getElementById('clientchoose');
	var clientall = document.getElementsByName('clientall');

	ext.addEvent('change' , function(){
		clientchoose.setStyle('display', 'none');
		clientall[1].checked = true;
		num = ext.get('value');
		if( num != -1){
			document.getElementById('all').setStyle('display', '');
			document.getElementById('lang').setStyle('display', '');
			document.getElementById('addmissing').setStyle('display', '');
		}
	});
	clientall[0].addEvent('click' , function(){
		var tdc = document.getElementById('client');
		var req = new Request( {
			method: 'get',
			url: 'index.php',
			onRequest: function(){
				tdc.set( 'text', '<?php echo JText::_('COM_KUNENATRANSLATE_LOADING'); ?>');
			},
			onSuccess: function(response){
				tdc.set( 'html', response );
				clientchoose.setStyle('display', '');
			},
			onFailure: function(){
				tdc.set( 'text', '<?php echo JText::_('COM_KUNENATRANSLATE_LOADING_FAILED');?>');
			}
		});
		req.send( 'option=com_kunenatranslate&controller=import&task=getClientList&extension='+num );
	});
	clientall[1].addEvent('click', function(){
		clientchoose.setStyle('display', 'none');
	});
});

//-->
</script>
<form action="index.php" method="post" name="adminForm">
<table class="adminlist">
	<tbody>
		<tr>
			<td><?php echo JText::_('COM_KUNENATRANSLATE_EXTENSION'); ?></td>
			<td><?php echo $this->extensionlist; ?></td>
		</tr>
		<tr style="display: none;" id="all">
			<td><?php echo JText::_('COM_KUNENATRANSLATE_CLIENT_ALL')?></td>
			<td><?php echo JHTMLSelect::booleanlist('clientall', null, 1); ?></td>
		</tr>
		<tr style="display: none;" id="clientchoose">
			<td><?php echo JText::_('COM_KUNENATRANSLATE_Client'); ?></td>
			<td id="client"><?php echo JText::_('COM_KUNENATRANSLATE_CHOOSE_EXTENSION'); ?></td>
		</tr>
		<tr style="display: none;" id="lang">
			<td><?php echo JText::_('COM_KUNENATRANSLATE_LANGUAGE'); ?></td>
			<td><?php echo $this->lang; ?></td>
		</tr>
		<?php if(JRequest::getVar('task') == 'import' || 
			JRequest::getVar('show') == 'import'):?>
		<tr style="display: none;" id="addmissing">
			<td><?php echo JText::_('COM_KUNENATRANSLATE_LABELS_ADDMISSING'); ?></td>
			<td><?php echo JHTMLSelect::booleanlist('addmissinglabel', null, 1); ?></td>
		</tr>
		<?php endif; ?>
	</tbody>
</table>
<input type="hidden" name="controller" value="import" />
<input type="hidden" name="option" value="com_kunenatranslate" />
<input type="hidden" name="task" value="" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
