<?php defined('_JEXEC') or die('Restricted access'); 
JToolBarHelper::cancel();?>

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
		</tr>
	</thead>
	<tbody>
		<?php $k = 0;
		foreach ($this->labels as $i=>$v):?>
			<tr class="row<?php echo $k;?>">
				<td><?php echo $i+1; ?></td>
				<td><?php echo $v->label; ?></td>
				<?php foreach ($this->languages as $val):?>
					<td>
						<input type="text" name="<?php echo $val.'['.$v->id.']';?>"
						value="<?php 
						foreach ($v->lang as $value){
							if($value->lang == $val)
								echo $value->translation;
						}?>" />
					</td>
				<?php endforeach;?>
			</tr>
			<?php $k = 1- $k;
			endforeach;?>
	</tbody>
</table>

<input type="hidden" name="option" value="com_kunenatranslate" />
<input type="hidden" name="id" value="" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxedchecked" value="0" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
