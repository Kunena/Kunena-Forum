<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Credits
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

?>
<div class="kmodule">
	<div class="box-wrapper">
		<div class="credits kbox box-color box-border box-border_radius box-border_radius-child box-shadow">
			<div class="headerbox-wrapper box-full">
				<div class="header">
					<h2 class="header"><?php echo JText::_('COM_KUNENA_CREDITS_PAGE_TITLE'); ?></h2>
				</div>
			</div>
			<div class="detailsbox-wrapper innerspacer">
				<div class="detailsbox">
					<div class="kcontent">
						<div class="credits-header innerspacer box-full box-hover box-border box-border_radius box-shadow">
							<img src="<?php echo $this->ktemplate->getImagePath('kunena.logo.png') ?>" alt="Kunena" align="left" hspace="5" vspace="5"/>
							<div class="credits-intro"><?php echo JText::_('COM_KUNENA_CREDITS_INTRO_TEXT'); ?></div>
						</div>
						<div class="credits-language innerspacer box-full box-hover box-border box-border_radius box-shadow">
							<ul class="kcredits-team">
								<?php foreach ($this->memberList as $member) : ?>
								<li class="credits-teammember">
									<a href="<?php echo $member['url'] ?>" target="_blank" rel="follow"><?php echo $this->escape($member['name']) ?></a>: <?php echo $member['title'] ?>
								</li>
								<?php endforeach ?>
							</ul>
						</div>
						<div class="credits-more innerspacer box-full box-hover box-border box-border_radius box-shadow">
							<?php echo $this->thanks ?>
						</div>
						<div class="credits-language innerspacer box-full box-hover box-border box-border_radius box-shadow">
							<?php echo JText::_('COM_KUNENA_CREDITS_LANGUAGE'); ?> <?php echo JText::_('COM_KUNENA_CREDITS_LANGUAGE_THANKS'); ?>
						</div>
						<div class="credits-more innerspacer box-full box-hover box-border box-border_radius box-shadow">
							<div>
								<?php echo JText::_('COM_KUNENA_CREDITS_GO_BACK') ?>
								<a href="javascript: history.go(-1)" title="<?php echo JText::_('COM_KUNENA_CREDITS_GO_BACK') ?>"><?php echo JText::_('COM_KUNENA_USER_RETURN_B') ?></a>
							</div>
						</div>
						<!-- Version Info -->
						<div class="credits-footer innerspacer box-full box-hover box-border box-border_radius box-shadow"><?php echo JText::_('COM_KUNENA_COPYRIGHT');?> &copy; 2008 - 2012 <a href ="http://www.kunena.org" target = "_blank">Kunena</a>, <?php echo JText::_('COM_KUNENA_LICENSE');?>: <a href ="http://www.gnu.org/copyleft/gpl.html" target = "_blank">GNU GPL</a></div>
						<!-- /Version Info -->
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="spacer"></div>
