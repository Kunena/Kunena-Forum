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
JToolBarHelper::title( JText::_( 'Kunena Translate' ).': <small><small>'.JText::_('New Label').'</small></small>', 'generic.png' );
JToolBarHelper::save(); 
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
			<th class="title">
				Client
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
				<?php echo $this->client; ?>
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

