<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Report
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\WebAsset\WebAssetManager;
use Kunena\Forum\Libraries\Version\KunenaVersion;
use Kunena\Forum\Libraries\Route\KunenaRoute;

/** @var WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->usePreset('chosen')
	->registerAndUseScript('joomla-chosen', 'legacy/joomla-chosen.min.js', [], [], ['chosen'])
	->useScript('multiselect')
	->addInlineScript(
		"
		jQuery(document).ready(function ($) {
	$('#link_sel_all').click(function(e) {
		$('#report_final').select();
		try {
			var successful = document.execCommand('copy');
			var msg = successful ? 'successful' : 'unsuccessful';
			console.log('Copying text command was ' + msg);
		}
		catch (err)
		{
			console.log('Oops, unable to copy');
	}
	});

	$('#link_sel_all_complete').click(function(e) {
		$('#report_final_anonymous').select();
		try {
			var successful = document.execCommand('copy');
			var msg = successful ? 'successful' : 'unsuccessful';
			console.log('Copying text command was ' + msg);
		}
		catch (err)
		{
			console.log('Oops, unable to copy');
		}
	});
});
	"
	);
?>

<div id="kunena" class="container-fluid">
	<div class="row">
		<div id="j-main-container" class="col-md-12" role="main">
			<div class="card card-block bg-faded p-2">
				<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>"
					  method="post" id="adminForm" name="adminForm">
					<input type="hidden" name="task" value=""/>
					<input type="hidden" name="boxchecked" value="1"/>
					<fieldset>
						<legend>
							<i class="icon icon-support"></i> <?php echo Text::_('COM_KUNENA_REPORT_SYSTEM_COMPLETE'); ?>
						</legend>
						<table class="table table-bordered table-striped">
							<tr>
								<td>
									<p><?php echo Text::_('COM_KUNENA_REPORT_SYSTEM_COMPLETE_DESC'); ?></p>
									<p>
										<a href="#" id="link_sel_all" name="link_sel_all" type="button"
										   class="btn btn-small btn-outline-primary"><i
													class="icon icon-signup"></i><?php echo Text::_('COM_KUNENA_REPORT_SELECT_ALL'); ?>
										</a>
									</p>
									<textarea id="report_final" class="input-block-level" name="report_final" cols="80"
											  rows="15"><?php echo $this->escape($this->systemReport); ?></textarea>
								</td>
							</tr>
						</table>
					</fieldset>
					<fieldset>
						<legend>
							<i class="icon icon-support"></i> <?php echo Text::_('COM_KUNENA_REPORT_SYSTEM_ANONYMOUS'); ?>
						</legend>
						<table class="table table-bordered table-striped">
							<tr>
								<td>
									<p><?php echo Text::_('COM_KUNENA_REPORT_SYSTEM_ANONYMOUS_DESC'); ?></p>
									<p>
										<a href="#" id="link_sel_all_complete" name="link_sel_all_complete"
										   type="button"
										   class="btn btn-small btn-outline-primary"><i
													class="icon icon-signup"></i><?php echo Text::_('COM_KUNENA_REPORT_SELECT_ALL'); ?>
										</a>
									</p>
									<textarea id="report_final_anonymous" class="input-block-level"
											  name="report_final_anonymous" cols="80"
											  rows="15"><?php echo $this->escape($this->systemReportAnonymous); ?></textarea>
								</td>
							</tr>
						</table>
					</fieldset>
				</form>
			</div>
		</div>
	</div>
	<div class="pull-right small">
		<?php echo KunenaVersion::getLongVersionHTML(); ?>
	</div>
</div>
