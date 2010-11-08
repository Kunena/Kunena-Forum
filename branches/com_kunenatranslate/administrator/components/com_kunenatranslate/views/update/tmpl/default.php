<?php defined('_JEXEC') or die('Restricted access'); 
JToolBarHelper::custom('update', 'save.png', '', JText::_('Update Labels'), false);
?>
<form action="index.php" method="post" name="adminForm">
<table class="adminlist">
	<tbody>
		<tr>
			<td>Client</td>
			<td><?php echo JHTML::_('select.genericlist', $this->client, 'client','', 'value','text')?></td>
		</tr>
	</tbody>
</table>
<input type="hidden" name="layout" value="labels" />
<input type="hidden" name="view" value="update" />
<input type="hidden" name="option" value="com_kunenatranslate" />
<input type="hidden" name="task" value="" />
</form>
