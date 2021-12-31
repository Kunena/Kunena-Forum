<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Smilies
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\WebAsset\WebAssetManager;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Version\KunenaVersion;
use Kunena\Forum\Libraries\Route\KunenaRoute;

$iconPath = json_encode(Uri::root(true) . '/');
$this->document->addScriptDeclaration(
	"function update_smiley(newimage) {
	document.smiley_image.src = {$iconPath} + newimage;
}"
);

/** @var WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('multiselect');
?>

<div id="kunena" class="container-fluid">
	<div class="row">
		<div id="j-main-container" class="col-md-12" role="main">
			<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena') ?>" method="post"
				  id="adminForm" name="adminForm">
				<input type="hidden" name="view" value="smilies"/>
				<input type="hidden" name="task" value="save"/>
				<?php if ($this->state->get('item.id'))
					:
					?><input type="hidden" name="smileyId"
							 value="<?php echo $this->state->get('item.id') ?>" /><?php
				endif; ?>
				<?php echo HTMLHelper::_('form.token'); ?>

				<fieldset>
					<legend><?php echo !$this->state->get('item.id') ? Text::_('COM_KUNENA_EMOTICONS_NEW_SMILEY') : Text::_('COM_KUNENA_EMOTICONS_EDIT_SMILEY'); ?></legend>
					<table class="table table-bordered table-striped">
						<tr>
							<td width="20%">
								<?php echo Text::_('COM_KUNENA_EMOTICONS_CODE'); ?>
							</td>
							<td width="80%">
								<input class="col-md-2" type="text" name="smileyCode"
									   value="<?php echo isset($this->smileySelected) ? $this->smileySelected->code : '' ?>"/>
								<img loading=lazy name="smiley_image"
									 src="<?php echo isset($this->smileySelected) ? $this->escape(KunenaFactory::getTemplate()->getSmileyPath($this->smileySelected->location, true)) : '' ?>"
									 border="0"
									 alt="<?php echo isset($this->smileySelected) ? $this->smileySelected->code : 'smiley' ?>"/>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo Text::_('COM_KUNENA_EMOTICONS_URL'); ?>
							</td>
							<td>
								<?php echo $this->listSmileys; ?>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo Text::_('COM_KUNENA_EMOTICONS_EMOTICONBAR'); ?>
							</td>
							<td>
								<input type="checkbox" name="smileyEmoticonBar" value="1"
										<?php echo $this->state->get('item.id') && $this->smileySelected->emoticonbar == 1 ? 'checked="checked"' : ''; ?> />
							</td>
						</tr>
					</table>
				</fieldset>
			</form>
		</div>
	</div>
	<div class="pull-right small">
		<?php echo KunenaVersion::getLongVersionHTML(); ?>
	</div>
</div>
