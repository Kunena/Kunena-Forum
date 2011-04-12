<?php 
/**
 * @version $Id$
 * Kunena Translate Component
 * 
 * @package	Kunena Translate
 * @Copyright (C) 2010-2011 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 */
defined('_JEXEC') or die('Restricted access');
JToolBarHelper::title( JText::_( 'Kunena Translate' ).': <small><small>'.JText::_('New Label').'</small></small>', 'generic.png' );
JToolBarHelper::save(); 
JToolBarHelper::cancel();?>

<script type="text/javascript">
<!--
window.addEvent( 'domready' , function() {
	var ext = document.getElementById('extension');

	ext.addEvent('change' , function(){
		var num = ext.get('value');
		if( num != -1){
			var tdc = document.getElementsByName('client');
			var req = new Request( {
				method: 'get',
				url: 'index.php',
				onRequest: function(){
					tdc[0].set( 'text', '<?php echo JText::_('COM_KUNENATRANSLATE_LOADING'); ?>');
				},
				onSuccess: function(response){
					tdc[0].set( 'html', response );
				},
				onFailure: function(){
					tdc[0].set( 'text', '<?php echo JText::_('COM_KUNENATRANSLATE_LOADING_FAILED');?>');
				}
			});
			req.send( 'option=com_kunenatranslate&controller=import&task=getClientList&extension='+num );
		}
	});
});

//-->
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
<table class="adminlist">
	<thead>
		<tr>
			<th width="10">
				<?php echo JText::_( 'Num' ); ?>
			</th>
			<th class="title">
				Label
			</th>
			<?php foreach ($this->languages as $v):?>
				<th class="title">
					<?php echo $v; ?>
				</th>
			<?php endforeach;?>
			<th class="title">
				<?php echo JText::_('COM_KUNENATRANSLATE_EXTENSION')?>
			</th>
			<th class="title">
				<?php echo JText::_('COM_KUNENATRANSLATE_CLIENT')?>
			</th>
		</tr>
	</thead>
	<tbody>
		<tr class="row0">
			<td>1</td>
			<td>
				<input type="text" name="label" />
			</td>
			<?php foreach ($this->languages as $val):
				?><td>
					<input type="text" name="<?php echo $val.'[][insert]';?>" />
				</td>
			<?php endforeach;?>
			<td>
				<?php echo $this->extensionlist; ?>
			</td>
			<td name="client">
				<?php echo JText::_('COM_KUNENATRANSLATE_CHOOSE_EXTENSION'); ?>
			</td>
		</tr>
	</tbody>
</table>

<input type="hidden" name="option" value="com_kunenatranslate" />
<input type="hidden" name="add" value="add" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="knownlanguages" value="<?php echo implode(',',$this->languages);?>" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>

