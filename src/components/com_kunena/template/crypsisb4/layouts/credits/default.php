<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Crypsisb4
 * @subpackage      Layout.Credits
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

?>
<div class="card">
	<h1 class="card-header">
		<?php echo Text::_('COM_KUNENA') . ' - ' . Text::_('COM_KUNENA_CREDITS_PAGE_TITLE'); ?>
	</h1>

	<div class="card-body" id="credits">
		<div class="kintro">
			<img class="float-left" src="<?php echo $this->logo; ?>" width="48" height="48" alt="Kunena"/>
			<p class="intro">
				<?php echo $this->intro; ?>
			</p>
		</div>

		<div class="clearfix"></div>

		<div class="credits">

			<dl class="row">
				<?php foreach ($this->memberList as $member)
				:
					?>
					<dt class="col-md-3">
						<a href="<?php echo $member['url']; ?>" target="_blank"
						   rel="noopener noreferrer"><?php echo $this->escape($member['name']); ?></a>
					</dt>
					<dd class="col-md-9">
						<?php echo $member['title']; ?>
					</dd>
					<hr class="hr-condensed">
				<?php endforeach ?>
				<dt class="col-md-3"><a><?php echo Text::_('COM_KUNENA_DONATE'); ?></a></dt>
				<dd class="col-md-9">
					<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
						<input name="cmd" type="hidden" value="_s-xclick">
						<input name="hosted_button_id" type="hidden" value="TPKVQFBQPFSLU">
						<input name="submit" type="image" alt="PayPal - The safer, easier way to pay online!"
							   src="https://www.paypalobjects.com/en_US/NL/i/btn/btn_donateCC_LG.gif" border="0">
						<img width="1" height="1" alt="" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif"
							 border="0">
					</form>
				</dd>
				<hr class="hr-condensed">
			</dl>

			<p>
				<?php echo $this->thanks; ?>
			</p>

			<p>
				<?php echo Text::_('COM_KUNENA_CREDITS_GO_BACK'); ?>
				<a href="javascript:window.history.back()" title="<?php echo Text::_('COM_KUNENA_CREDITS_GO_BACK'); ?>">
					<?php echo Text::_('COM_KUNENA_USER_RETURN_B'); ?>
				</a>
			</p>

			<p class="center">
				<?php echo Text::_('COM_KUNENA_COPYRIGHT'); ?> &copy; 2008 - 2020 <a href="https://www.kunena.org"
																					 target="_blank">Kunena</a>,
				<?php echo Text::_('COM_KUNENA_LICENSE'); ?>: <a href="https://www.gnu.org/copyleft/gpl.html"
																 target="_blank">GNU GPL</a>
			</p>
		</div>
	</div>
</div>
