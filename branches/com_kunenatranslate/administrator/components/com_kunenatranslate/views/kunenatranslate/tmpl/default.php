<?php 
/**
 * @version $Id$
 * Kunena Translate Component
 * 
 * @package	Kunena Translate
 * @Copyright (C) 2011 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 */
defined('_JEXEC') or die('Restricted access');
JToolBarHelper::addNew('add','New Label');
JToolBarHelper::custom('update', 'refresh', 'refresh', 'Search New Labels', false, true );
JToolBarHelper::custom('old', 'archive', 'archive', 'Search outdated Labels', false, true);
JToolBarHelper::editListX();
JToolBarHelper::deleteList();?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
<table>
	<tr>
		<td align="left" width="100%"></td>
		<td nowrap="nowrap">
			<?php 
			echo $this->lists['extension'];
			echo $this->lists['client'];
			?>
		</td>
</table>
<table class="adminlist">
	<thead>
		<tr>
			<th width="10">
				<?php echo JText::_( 'COM_KUNENATRANSLATE_NUM' ); ?>
			</th>
			<th class="title" width="10">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->labels); ?>);" />
			</th>
			<th class="title">
				<?php echo JText::_ ( 'COM_KUNENATRANSLATE_LABEL' ); ?>
			</th>
			<th class="title">
				<?php echo JText::_ ( 'COM_KUNENATRANSLATE_TRANSLATION' ); ?>
			</th>
			<th class="title">
				<?php echo JText::_ ( 'COM_KUNENATRANSLATE_CLIENT' ); ?>
			</th>
			<th width="1%" nowrap="nowrap">
				<?php echo JText::_ ( 'COM_KUNENATRANSLATE_ID' ); ?>
			</th>
		</tr>
	</thead>
	<tbody>
		<?php $k = 0;
		if(!empty($this->labels)):
			foreach ($this->labels as $i=>$v):
				$link = '<a href="index.php?option=com_kunenatranslate&task=edit&layout=form&cid[]='.$v->id.'" >'.$v->label.'</a>';
				$lang = '';
				if(!empty($v->lang)){
					$n = count($v->lang);
					foreach ($v->lang as $ii=>$val){
						$lang .= $val->lang;
						if($n>1 && $n-1>$ii) $lang .= ', ';  
					}
				}
				?>
				<tr class="row<?php echo $k;?>">
					<td><?php echo $i+1; ?></td>
					<td><?php echo JHTML::_('grid.id', $i, $v->id)?></td>
					<td><?php echo $link; ?></td>
					<td><?php echo $lang; ?></td>
					<td><?php echo $v->client; ?></td>
					<td><?php echo $v->id; ?></td>
				</tr>
				<?php $k = 1- $k;
				endforeach;
			endif;?>
	</tbody>
</table>

<input type="hidden" name="option" value="com_kunenatranslate" />
<input type="hidden" name="id" value="" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="oldext" value="<?php echo $this->ext; ?>" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
