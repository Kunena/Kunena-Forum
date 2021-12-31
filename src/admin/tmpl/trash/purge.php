<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Trash
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\WebAsset\WebAssetManager;
use Kunena\Forum\Libraries\Version\KunenaVersion;
use Kunena\Forum\Libraries\Route\KunenaRoute;

/** @var WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('multiselect')
		->useScript('formbehavior.chosen');

$count = count($this->purgeItems);
?>

<div id="kunena" class="container-fluid">
	<div class="row">
		<div id="j-main-container" class="col-md-12" role="main">
			<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=trash') ?>"
				  method="post" id="adminForm"
				  name="adminForm">
				<input type="hidden" name="task" value="purge"/>
				<input type="hidden" name="boxchecked" value="1"/>
				<input type="hidden" name="md5" value="<?php echo $this->md5Calculated ?>"/>
				<?php echo HTMLHelper::_('form.token'); ?>
				<fieldset>
					<legend><?php echo Text::_('COM_KUNENA_ITEMS_BEING_DELETED'); ?></legend>
					<div class="alert"><?php echo Text::sprintf('COM_KUNENA_WARNING_PERM_DELETE_ITEMS', $count); ?></div>
					<?php
					if ($count)
						:
						?>
						<table class="table table-striped">
							<?php foreach ($this->purgeItems as $item)
								:
								?>
								<tr>
									<td width="1%"><?php echo $this->escape($item->id); ?></td>
									<td><?php echo $this->escape($item->subject); ?></td>
								</tr>
							<?php endforeach; ?>
						</table>
					<?php endif; ?>
				</fieldset>
			</form>
		</div>
		<div class="pull-right small">
			<?php echo KunenaVersion::getLongVersionHTML(); ?>
		</div>
	</div>
</div>
