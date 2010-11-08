<?php defined('_JEXEC') or die('Restricted access'); ?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
<table class="adminlist">
	<thead>
		<tr>
			<th width="10">
				<?php echo JText::_( 'Num' ); ?>
			</th>
			<th class="title" width="10">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->labels); ?>);" />
			</th>
			<th class="title">
				Label
			</th>
			<th width="1%" nowrap="nowrap">
				ID
			</th>
		</tr>
	</thead>
</table>

<input type="hidden" name="option" value="com_kunenatranslate" />
<input type="hidden" name="id" value="" />
<input type="hidden" name="task" value="" />
<!-- <input type="hidden" name="controller" value="" /> -->
</form>
