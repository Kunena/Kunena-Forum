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
<div class="box-module">
	<div class="box-wrapper box-color box-border box-border_radius box-shadow">
		<div class="credits block">
			<div class="headerbox-wrapper box-full">
				<div class="header">
					<h2 class="header"><?php echo JText::_('COM_KUNENA_CREDITS_PAGE_TITLE'); ?></h2>
				</div>
			</div>
			<div class="detailsbox-wrapper">
				<div class="detailsbox">
					<div class="kcontent">
						<div class="credits-header innerspacer box-full box-hover box-border box-border_radius box-shadow">
							<img src="<?php echo $this->ktemplate->getImagePath('kunena.logo.png') ?>" alt="Kunena" align="left" hspace="5" vspace="5"/>
							<div class="credits-intro"><?php echo JText::_('COM_KUNENA_CREDITS_INTRO_TEXT'); ?></div>
						</div>
						<div class="credits-language innerspacer box-full box-hover box-border box-border_radius box-shadow">
							<ul class="kcredits-team">
								<li class="credits-teammember"><a href="http://www.starVmax.com" target='_blank' rel='follow'>fxstein</a>: <?php echo JText::sprintf('COM_KUNENA_CREDITS_DEVELOPER_SPECIAL', 'Yamaha Star VMax' ); ?> <a href="http://www.starVmax.com/forum/" target='_blank' rel='follow'>www.starVmax.com/forum/</a></li>
								<li class="credits-teammember"><a href="http://www.herppi.net" target='_blank' rel='follow'>Matias</a>: <?php echo JText::_('COM_KUNENA_CREDITS_DEVELOPER'); ?></li>
								<li class="credits-teammember"><a href="http://www.kunena.org/community/profile?userid=114" target='_blank' rel='follow'>severdia</a>: <?php echo JText::_('COM_KUNENA_CREDITS_DEVELOPER'); ?></li>
								<li class="credits-teammember"><a href="http://www.kunena.org/community/profile?userid=1288" target='_blank' rel='follow'>xillibit</a>: <?php echo JText::_('COM_KUNENA_CREDITS_DEVELOPER'); ?></li>
								<li class="credits-teammember"><a href="http://www.kunena.org/community/profile?userid=447" target='_blank' rel='follow'>@quila</a>: <?php echo JText::_('COM_KUNENA_CREDITS_CONTRIBUTOR'); ?></li>
								<li class="credits-teammember"><a href="http://www.kunena.org/community/profile?userid=634" target='_blank' rel='follow'>810</a>: <?php echo JText::_('COM_KUNENA_CREDITS_CONTRIBUTOR'); ?></li>
								<li class="credits-teammember"><a href="http://www.kunena.org/community/profile?userid=10133" target='_blank' rel='follow'>LittleJohn</a>: <?php echo JText::_('COM_KUNENA_CREDITS_CONTRIBUTOR'); ?></li>
								<li class="credits-teammember"><a href="http://www.kunena.org/community/profile?userid=2171" target='_blank' rel='follow'>svanschu</a>: <?php echo JText::_('COM_KUNENA_CREDITS_CONTRIBUTOR'); ?></li>
								<li class="credits-teammember"><a href="http://www.kunena.org/community/profile?userid=2198" target='_blank' rel='follow'>Rich</a>: <?php echo JText::_('COM_KUNENA_CREDITS_MODERATOR'); ?></li>
								<li class="credits-teammember"><a href="http://www.kunena.org/community/profile?userid=997" target='_blank' rel='follow'>sozzled</a>: <?php echo JText::_('COM_KUNENA_CREDITS_MODERATOR'); ?></li>
							</ul>
						</div>
						<div class="credits-more innerspacer box-full box-hover box-border box-border_radius box-shadow">
							<?php echo JText::sprintf('COM_KUNENA_CREDITS_THANKS_PART_LONG', 'Beat', 'BoardBoss', 'GoremanX', 'madLyfe', 'Mortti', '<a href="http://www.kunena.org" target="_blank" rel="follow">www.kunena.org</a>'); ?>
							<?php echo JText::_('COM_KUNENA_CREDITS_THANKS'); ?>
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
						<div class="credits-footer innerspacer box-full box-hover box-border box-border_radius box-shadow"><?php echo JText::_('COM_KUNENA_COPYRIGHT');?> &copy; 2008 - 2011 <a href ="http://www.kunena.org" target = "_blank">Kunena</a>, <?php echo JText::_('COM_KUNENA_LICENSE');?>: <a href ="http://www.gnu.org/copyleft/gpl.html" target = "_blank">GNU GPL</a></div>
						<!-- /Version Info -->
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="spacer"></div>