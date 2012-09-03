<?php
/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 * @subpackage Credits
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="kblock kcredits">
	<div class="kheader">
		<h2><span><?php echo JText::_('COM_KUNENA_CREDITS_PAGE_TITLE'); ?></span></h2>
	</div>
	<div class="kcontainer">
		<div class="kbody">
			<div class="kcreditsheader">
				<img src="<?php echo $this->ktemplate->getImagePath('icons/kunena-logo-48-black.png');?>" alt="Kunena" class="fltlft" style="margin: 18px;" />
				<div class="kcredits-intro"><?php echo JText::_('COM_KUNENA_CREDITS_INTRO_TEXT'); ?></div>
			</div>
			<div class="kcredits-language">
				<ul class="kteam">
					<?php foreach ($this->memberList as $member) : ?>
					<li class="credits-teammember">
						<a href="<?php echo $member['url'] ?>" target="_blank" rel="follow"><?php echo $this->escape($member['name']) ?></a>: <?php echo $member['title'] ?>
					</li>
					<?php endforeach ?>
				</ul>
			</div>
			<div class="kcredits-more">
				<?php echo $this->thanks ?>
			</div>
			<div class="kcredits-language">
				<?php echo JText::_('COM_KUNENA_CREDITS_LANGUAGE'); ?> <?php echo JText::_('COM_KUNENA_CREDITS_LANGUAGE_THANKS'); ?>
			</div>
			<div class="kcredits-more">
				<div>
					<?php echo JText::_('COM_KUNENA_CREDITS_GO_BACK') ?>
					<a href="javascript: history.go(-1)" title="<?php echo JText::_('COM_KUNENA_CREDITS_GO_BACK') ?>"><?php echo JText::_('COM_KUNENA_USER_RETURN_B') ?></a>
				</div>
			</div>
			<div class="kfooter"><?php echo JText::_('COM_KUNENA_COPYRIGHT');?> &copy; 2008 - 2012 <a href = "http://www.kunena.org" target = "_blank">Kunena</a>, <?php echo JText::_('COM_KUNENA_LICENSE');?>: <a href = "http://www.gnu.org/copyleft/gpl.html" target = "_blank">GNU GPL</a></div>
		</div>
	</div>
</div>
