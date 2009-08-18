<?php
/**
 * @version		$Id:$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;
?>
							<h3><a href="3"><?php echo $this->escape($this->announcement->title); ?></a></h3>	
							<p class="announce_summary"><span><?php echo JHTML::_('date', $this->announcement->created); ?></span><?php echo $this->escape($this->announcement->sdescription); ?>
											<a href="/forum/announcement/read/id-8"><?php echo JText::_('K_READ_MORE'); ?></a></p>
							
							<div class="clr"></div>