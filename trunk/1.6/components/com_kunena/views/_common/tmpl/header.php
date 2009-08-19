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
		<div class="topnav">
			<ul>
				<li class="active"><a href="/forum/latest" title="<?php echo JText::_('K_RECENT_DISCUSSIONS'); ?>"><span><?php echo JText::_('K_RECENT_DISCUSSIONS'); ?></span></a></li>
				<li><a href="/forum/listcat" title="<?php echo JText::_('K_CATEGORIES'); ?>"><span><?php echo JText::_('K_CATEGORIES'); ?></span></a></li>
				<li><a href="/forum/faq" title="<?php echo JText::_('K_HELP'); ?>"><span><?php echo JText::_('K_HELP'); ?></span></a></li>
			</ul>
		</div>
		<div class="search">
			<form action="/forum/search" name="Search" method="post">
				<input class="input_search" type="text" name="q" size="15" value="<?php echo JText::_('K_SEARCH_FORUM'); ?>" onblur="if(this.value=='') this.value='<?php echo JText::_('K_SEARCH_FORUM'); ?>';" onfocus="if(this.value=='<?php echo JText::_('K_SEARCH_FORUM'); ?>') this.value='';" />
				<input type="submit" value="<?php echo JText::_('K_GO'); ?>" name="submit" class="go_btn"/>
			</form>
		</div>
		<div class="corner_tl">
			<div class="corner_tr">
				<div class="corner_br">
					<div class="corner_bl">		
						<div class="profile_box">
							<p class="welcome"><?php echo JText::_('K_WELCOME'); ?>, <span><?php echo JText::_('K_GUEST'); ?></span></p>
							<p class="register_login"><?php echo JText::_('K_PLEASE'); ?> <a href="/component/user/login"><?php echo JText::_('K_LOG_IN'); ?></a> <?php echo JText::_('K_OR'); ?> <a href="/component/user/register"><?php echo JText::_('K_REGISTER'); ?></a>. <a href="/component/user/reset"><?php echo JText::_('K_LOST_PASSWORD'); ?></a></p>												
						</div>	
					</div>
					</div>
				</div>
			</div>
									
<?php foreach ($this->announcements as $this->current=>$this->announcement): ?>									
		<div class="corner_tl">
			<div class="corner_tr">
				<div class="corner_br">
					<div class="corner_bl">
						<div class="announcements">
							<?php echo $this->loadCommonTemplate('announce'); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
<?php endforeach; ?>