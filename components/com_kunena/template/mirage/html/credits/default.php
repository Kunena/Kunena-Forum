<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Credits
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

?>
<div class="kmodule credits-default">
	<div class="kbox-wrapper kbox-full">
		<div class="credits-default-kbox kbox kbox-color kbox-border kbox-border_radius kbox-border_radius-vchild kbox-shadow kbox-animate">
			<div class="headerbox-wrapper kbox-full">
				<div class="header">
					<h2 class="header"><?php echo JText::_('COM_KUNENA_CREDITS_PAGE_TITLE'); ?></h2>
				</div>
			</div>
			<div class="detailsbox-wrapper innerspacer kbox-full">
				<div class="detailsbox">
					<div class="kcontent">
						<div class="credits-header innerspacer kbox-full kbox-hover kbox-border kbox-border_radius kbox-shadow">
							<img src="<?php echo $this->ktemplate->getImagePath('kunena.logo.png') ?>" alt="Kunena" align="left" hspace="5" vspace="5"/>
							<div class="credits-intro"><?php echo JText::_('COM_KUNENA_CREDITS_INTRO_TEXT'); ?></div>
						</div>
						<div class="credits-language innerspacer kbox-full kbox-hover kbox-border kbox-border_radius kbox-shadow">
							<ul class="kcredits-team">
								<?php foreach ($this->memberList as $member) : ?>
								<li class="credits-teammember">
									<a href="<?php echo $member['url'] ?>" target="_blank" rel="follow"><?php echo $this->escape($member['name']) ?></a>: <?php echo $member['title'] ?>
								</li>
								<?php endforeach ?>
							</ul>
						</div>
						<div class="credits-more innerspacer kbox-full kbox-hover kbox-border kbox-border_radius kbox-shadow">
							<?php echo $this->thanks ?>
						</div>
						<div class="credits-language innerspacer kbox-full kbox-hover kbox-border kbox-border_radius kbox-shadow">
							<?php echo JText::_('COM_KUNENA_CREDITS_LANGUAGE'); ?> <?php echo JText::_('COM_KUNENA_CREDITS_LANGUAGE_THANKS'); ?>
						</div>
						<div class="credits-more innerspacer kbox-full kbox-hover kbox-border kbox-border_radius kbox-shadow">
							<div>
								<?php echo JText::_('COM_KUNENA_CREDITS_GO_BACK') ?>
								<a href="javascript: history.go(-1)" title="<?php echo JText::_('COM_KUNENA_CREDITS_GO_BACK') ?>"><?php echo JText::_('COM_KUNENA_USER_RETURN_B') ?></a>
							</div>
						</div>
						<!-- Version Info -->
						<div class="credits-footer innerspacer kbox-full kbox-hover kbox-border kbox-border_radius kbox-shadow"><?php echo JText::_('COM_KUNENA_COPYRIGHT');?> &copy; 2008 - 2013 <a href ="http://www.kunena.org" target = "_blank">Kunena</a>, <?php echo JText::_('COM_KUNENA_LICENSE');?>: <a href ="http://www.gnu.org/copyleft/gpl.html" target = "_blank">GNU GPL</a></div>
						<!-- /Version Info -->
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

