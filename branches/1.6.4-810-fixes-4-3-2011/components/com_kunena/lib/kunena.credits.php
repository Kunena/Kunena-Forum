<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.org
*
* Based on FireBoard Component
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
**/
defined( '_JEXEC' ) or die();

$kunena_config = KunenaFactory::getConfig ();
?>
<div class="kblock">
	<div class="kheader">
		<h2><span><?php echo JText::_('COM_KUNENA_CREDITS_PAGE_TITLE'); ?></span></h2>
	</div>
	<div class="kcontainer">
		<div class="kbody">
			<div class="kcreditsheader">
				<img src="<?php echo KUNENA_DIRECTURL . '/template/default/images/kunena.logo.png';?>" alt="Kunena" align="left" hspace="5" vspace="5"/>
				<div class="kcredits-intro"><?php echo JText::_('COM_KUNENA_CREDITS_INTRO_TEXT'); ?></div>
			</div>
			<div class="kcredits-language">
				<ul class="kteam">
					<li class="kteammember"><a href="http://www.starVmax.com" target='_blank' rel='follow'>fxstein</a>: <?php echo JText::sprintf('COM_KUNENA_CREDITS_DEVELOPER_SPECIAL', 'Yamaha Star VMax' ); ?> <a href="http://www.starVmax.com/forum/" target='_blank' rel='follow'>www.starVmax.com/forum/</a></li>
					<li class="kteammember"><a href="http://www.herppi.net" target='_blank' rel='follow'>Matias</a>: <?php echo JText::_('COM_KUNENA_CREDITS_DEVELOPER'); ?></li>
					<li class="kteammember"><a href="http://www.kunena.org/community/profile?userid=114" target='_blank' rel='follow'>severdia</a>: <?php echo JText::_('COM_KUNENA_CREDITS_DEVELOPER'); ?></li>
					<li class="kteammember"><a href="http://www.kunena.org/community/profile?userid=1288" target='_blank' rel='follow'>xillibit</a>: <?php echo JText::_('COM_KUNENA_CREDITS_DEVELOPER'); ?></li>
					<li class="kteammember"><a href="http://www.kunena.org/community/profile?userid=447" target='_blank' rel='follow'>@quila</a>: <?php echo JText::_('COM_KUNENA_CREDITS_CONTRIBUTOR'); ?></li>
					<li class="kteammember"><a href="http://www.kunena.org/community/profile?userid=634" target='_blank' rel='follow'>810</a>: <?php echo JText::_('COM_KUNENA_CREDITS_CONTRIBUTOR'); ?></li>
					<li class="kteammember"><a href="http://www.kunena.org/community/profile?userid=2171" target='_blank' rel='follow'>LDA</a>: <?php echo JText::_('COM_KUNENA_CREDITS_CONTRIBUTOR'); ?></li>
					<li class="kteammember"><a href="http://www.kunena.org/community/profile?userid=684" target='_blank' rel='follow'>Alakentu</a>: <?php echo JText::_('COM_KUNENA_CREDITS_MODERATOR'); ?></li>
					<li class="kteammember"><a href="http://www.kunena.org/community/profile?userid=2198" target='_blank' rel='follow'>Rich</a>: <?php echo JText::_('COM_KUNENA_CREDITS_MODERATOR'); ?></li>
					<li class="kteammember"><a href="http://www.kunena.org/community/profile?userid=997" target='_blank' rel='follow'>sozzled</a>: <?php echo JText::_('COM_KUNENA_CREDITS_MODERATOR'); ?></li>
				</ul>
			</div>
			<div class="kcredits-more">
				<?php echo JText::sprintf('COM_KUNENA_CREDITS_THANKS_PART_LONG', 'Beat', 'Cerberus', 'DTP2', 'LittleJohn', 'JoniJnm', '<a href="http://www.kunena.org" target="_blank" rel="follow">www.kunena.org</a>'); ?>
				<?php echo JText::_('COM_KUNENA_CREDITS_THANKS'); ?>
			</div>
			<div class="kcredits-language">
				<?php echo JText::_('COM_KUNENA_CREDITS_LANGUAGE'); ?> <?php echo JText::_('COM_KUNENA_CREDITS_LANGUAGE_THANKS'); ?>
			</div>
			<div class="kcredits-more">
				<?php $catid = (int)$catid;
				// Add a link to go back to the latest category we where viewing...
				echo '<div>'. JText::_('COM_KUNENA_CREDITS_GO_BACK') . ' ' .
				CKunenaLink::GetCategoryLink('showcat', $catid, JText::_('COM_KUNENA_USER_RETURN_B'), $rel='nofollow') . '</div>';
				?>
			</div>
			<!-- Version Info -->
			<div class="kfooter"><?php echo JText::_('COM_KUNENA_COPYRIGHT');?> &copy; 2008 - 2011 <a href = "http://www.kunena.org" target = "_blank">Kunena</a>, <?php echo JText::_('COM_KUNENA_LICENSE');?>: <a href = "http://www.gnu.org/copyleft/gpl.html" target = "_blank">GNU GPL</a></div>
			<!-- /Version Info -->
		</div>
	</div>
</div>
<!-- Begin: Forum Jump -->
<div class="kblock">
	<div class="kheader">
		<h2><span><?php echo JText::_('COM_KUNENA_GO_TO_CATEGORY'); ?></span></h2>
	</div>
	<div class="kcontainer">
		<div class="khelprulesjump">
			<?php CKunenaTools::loadTemplate('/forumjump.php') ?>
		</div>
	</div>
</div>
<!-- Finish: Forum Jump -->
