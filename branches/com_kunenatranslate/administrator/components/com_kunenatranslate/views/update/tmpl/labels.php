<?php defined('_JEXEC') or die('Restricted access'); 
JToolBarHelper::save();?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
<table class="adminlist">
	<thead>
		<tr>
			<th width="10">
				<?php echo JText::_( 'Num' ); ?>
			</th>
			<th class="title" width="10">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->labels['new']); ?>);" />
			</th>
			<th class="title">
				Label
			</th>
			<th class="title">
				Client
			</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($this->labels['new'] as $k=>$v):?>
		<tr>
			<td><?php echo $k+1; ?></td>
			<td><?php echo JHTML::_('grid.id', $k, $k ); ?></td>
			<td><?php echo $v; ?></td>
			<td><?php echo $this->client; ?></td>
		</tr>
		<?php endforeach;?>
	</tbody>
</table>

<input type="hidden" name="option" value="com_kunenatranslate" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="labels" value="<?php echo implode(";",$this->labels['new']); ?>" />
<input type="hidden" name="client" value="<?php echo $this->client; ?>" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
