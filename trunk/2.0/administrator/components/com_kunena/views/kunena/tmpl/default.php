<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;
?>
<table width="100%" border="0">
	<tr>
		<td width="65%" valign="top">
			<div id="cpanel">
				<?php echo $this->addIcon('config.png','index.php?option=com_kunena&view=config', JText::_('Configuration'));?>
				<?php echo $this->addIcon('forums.png','index.php?option=com_kunena&view=forums', JText::_('Forums'));?>
				<?php echo $this->addIcon('users.png','index.php?option=com_kunena&view=users', JText::_('Users'));?>
				<?php echo $this->addIcon('smilies.png','index.php?option=com_kunena&view=smilies', JText::_('Smilies'));?>
				<?php echo $this->addIcon('ranks.png','index.php?option=com_kunena&view=ranks', JText::_('Ranks'));?>
				<?php echo $this->addIcon('templates.png','index.php?option=com_kunena&view=templates', JText::_('Templates'));?>
				<?php echo $this->addIcon('queue.png','index.php?option=com_kunena&view=queue', JText::_('Job Queue'));?>
				<?php echo $this->addIcon('tools.png','index.php?option=com_kunena&view=tools', JText::_('Tools'));?>
				<?php echo $this->addIcon('plugins.png','index.php?option=com_plugins&filter_type=kunena', JText::_('Plugins'));?>
				<?php echo $this->addIcon('about.png','index.php?option=com_kunena&view=about', JText::_('About')); ?>
				<?php echo $this->addIcon('help.png','http://docs.kunena.com', JText::_('Help'), true ); ?>
			</div>
		</td>
		<td width="35%" valign="top">
			<?php
				echo $this->pane->startPane( 'stat-pane' );
				echo $this->pane->startPanel( JText::_('Welcome to Kunena') , 'welcome' );
			?>
			<table class="adminlist">
				<tr>
					<td>
						<div style="font-weight:700;">
							<?php echo JText::_('The best forum component for Joomla!');?>
						</div>
						<p>
							If you require support just head on to the forums at
							<a href="http://www.kunena.com/forum/" target="_blank">
							http://www.kunena.com/forum
							</a>
						</p>
						<p>
							For documentation please visit our wiki at
							<a href="http://docs.kunena.com" target="_blank">http://docs.kunena.com</a>
						</p>
					</td>
				</tr>
			</table>
			<?php
				echo $this->pane->endPanel();
				echo $this->pane->endPane();
			?>
		</td>
	</tr>
</table>
