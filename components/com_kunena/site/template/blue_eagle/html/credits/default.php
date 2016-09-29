<?php
/**
 * Kunena Component
 * @package Kunena.Template.Blue_Eagle
 * @subpackage Credits
 *
 * @copyright (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
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
				<img src="<?php echo $this->ktemplate->getImagePath('icons/kunena-logo-48-white.png');?>" alt="Kunena" class="fltlft" style="margin: 18px;" />
				<div class="kcredits-intro"><?php echo $this->intro; ?></div>
			</div>
			<div class="kcredits-language">
				<ul class="kteam">
					<?php foreach ($this->memberList as $member) : ?>
					<li class="credits-teammember">
						<a href="<?php echo $member['url'] ?>" target="_blank" rel="follow"><?php echo $this->escape($member['name']) ?></a> - <?php echo $member['title'] ?>
					</li>
					<?php endforeach ?>
					<hr class="hr-condensed">
					<dt><a><?php echo JText::_('COM_KUNENA_DONATE');?></a></dt>
					<dd>
						<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
							<input name="cmd" type="hidden" value="_s-xclick">
							<input name="hosted_button_id" type="hidden" value="TPKVQFBQPFSLU">
							<input name="submit" type="image" alt="PayPal - The safer, easier way to pay online!" src="https://www.paypalobjects.com/en_US/NL/i/btn/btn_donateCC_LG.gif" border="0">
							<img width="1" height="1" alt="" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" border="0">
						</form>
					</dd>
					<hr class="hr-condensed">
				</ul>
			</div>
			<div class="kcredits-language">
				<?php echo $this->thanks ?>
			</div>
			<div class="kcredits-more">
				<div>
					<?php echo JText::_('COM_KUNENA_CREDITS_GO_BACK') ?>
					<a href="javascript:window.history.back()" title="<?php echo JText::_('COM_KUNENA_CREDITS_GO_BACK') ?>"><?php echo JText::_('COM_KUNENA_USER_RETURN_B') ?></a>
				</div>
			</div>
			<div class="kfooter"><?php echo JText::_('COM_KUNENA_COPYRIGHT');?> &copy; 2008 - 2016 <a href = "https://www.kunena.org" target = "_blank">Kunena</a>, <?php echo JText::_('COM_KUNENA_LICENSE');?>: <a href = "http://www.gnu.org/copyleft/gpl.html" target = "_blank">GNU GPL</a></div>
		</div>
	</div>
</div>
