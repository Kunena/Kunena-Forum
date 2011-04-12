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
if( JRequest::getVar('task') == 'old')
	JToolBarHelper::custom('old', 'save.png', '', JText::_('Find outdated') , false);
else
	JToolBarHelper::custom('update', 'save.png', '', JText::_('Update Labels') , false);
JToolBarHelper::cancel();
?>

<script type="text/javascript">
<!--
window.addEvent( 'domready' , function() {
	var ext = document.getElementById('extension');

	ext.addEvent('change' , function(){
		var num = ext.get('value');
		if( num != -1){
			var tdc = document.getElementById('client');
			var req = new Request( {
				method: 'get',
				url: 'index.php',
				onRequest: function(){
					tdc.set( 'text', '<?php echo JText::_('COM_KUNENATRANSLATE_LOADING'); ?>');
				},
				onSuccess: function(response){
					tdc.set( 'html', response );
				},
				onFailure: function(){
					tdc.set( 'text', '<?php echo JText::_('COM_KUNENATRANSLATE_LOADING_FAILED');?>');
				}
			});
			req.send( 'option=com_kunenatranslate&controller=import&task=getClientList&extension='+num );
		}
	});
});

//-->
</script>

<form action="index.php" method="post" name="adminForm">
<table class="adminlist">
	<tbody>
		<tr>
			<td><?php echo JText::_('COM_KUNENATRANSLATE_EXTENSION')?></td>
			<td><?php echo $this->extensionlist; ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_('COM_KUNENATRANSLATE_CLIENT')?></td>
			<td id="client"><?php echo JText::_('COM_KUNENATRANSLATE_CHOOSE_EXTENSION'); ?></td>
		</tr>
	</tbody>
</table>
<input type="hidden" name="controller" value="update" />
<input type="hidden" name="option" value="com_kunenatranslate" />
<input type="hidden" name="task" value="" />
</form>
