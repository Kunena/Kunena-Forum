<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Ranks
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Helper\UserGroupsHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\WebAsset\WebAssetManager;
use Kunena\Forum\Libraries\Version\KunenaVersion;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaRankGroups;

$iconPath = json_encode(Uri::root(true) . '/');
$this->document->addScriptDeclaration(
    "function update_rank(newimage) {
	document.rankImage.src = {$iconPath} + newimage;
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
                <input type="hidden" name="view" value="ranks"/>
                <input type="hidden" name="task" value="save"/>
                <?php if ($this->state->get('item.id')) :
                    ?><input type="hidden" name="rankid"
                             value="<?php echo $this->state->get('item.id') ?>" /><?php
                endif; ?>
                <?php echo HTMLHelper::_('form.token'); ?>

                <fieldset>
                    <legend><?php echo !$this->state->get('item.id') ? Text::_('COM_KUNENA_NEW_RANK') : Text::_('COM_KUNENA_RANKS_EDIT'); ?></legend>
                    <table class="table table-bordered table-striped">

                        <tr>
                            <td width="20%">
                                <?php echo Text::_('COM_KUNENA_RANKS'); ?>
                            </td>
                            <td width="80%">
                                <input class="form-control" type="text" name="rankTitle"
                                           value="<?php echo isset($this->rankSelected->rankTitle) ? $this->rankSelected->rankTitle : '' ?>"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php echo Text::_('COM_KUNENA_RANKSIMAGE'); ?>
                            </td>
                            <td>
                                <?php echo $this->listRanks; ?>
                                <?php
                                if (!$this->state->get('item.id')) :
                                    ?>
                                    <img loading=lazy name="rankImage" src="" border="0"
                                         alt="<?php echo isset($this->rankSelected->rankTitle) ? $this->rankSelected->rankTitle : '' ?>"/>
                                <?php else :
                                    ?>
                                    <img loading=lazy name="rankImage"
                                         src="<?php echo $this->escape(KunenaFactory::getTemplate()->getRankPath($this->rankSelected->rankImage ?? '', true)); ?>"
                                         border="0"
                                         alt="<?php echo isset($this->rankSelected->rankTitle) ? $this->rankSelected->rankTitle : 'rank' ?>"/>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php echo Text::_('COM_KUNENA_RANKSMIN'); ?>
                            </td>
                            <td>
                                <input class="form-control" type="text" name="rankMin"
                                           value="<?php echo isset($this->rankSelected) ? $this->rankSelected->rankMin : '1' ?>"/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php echo Text::_('COM_KUNENA_RANKS_USERGROUP'); ?>
                            </td>
                            <td>
                                <?php $rankGroup = isset($this->rankSelected->rankId) ? KunenaRankGroups::getGroup((int) $this->rankSelected->rankId) : 0; ?>
                                <select class="form-select" name="rankGroup">
                                    <option value="0"><?php echo Text::_('COM_KUNENA_RANKS_USERGROUP_NONE'); ?></option>
                                    <?php foreach (UserGroupsHelper::getInstance()->getAll() as $group) : ?>
                                        <option value="<?php echo (int) $group->id; ?>"<?php echo $rankGroup === (int) $group->id ? ' selected' : ''; ?>>
                                            <?php echo str_repeat('- ', (int) $group->level) . $this->escape($group->title); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text"><?php echo Text::_('COM_KUNENA_RANKS_USERGROUP_DESC'); ?></div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php echo Text::_('COM_KUNENA_RANKS_SPECIAL'); ?>
                            </td>
                            <td>
                                <label>
                                    <input
                                            type="checkbox" <?php echo isset($this->rankSelected) && $this->rankSelected->rankSpecial ? 'checked="checked"' : '' ?>
                                            name="rankSpecial" value="1"/>
                                </label>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </form>
        </div>
    </div>
    <div class="float-end small">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </div>
</div>
