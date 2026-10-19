<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Categories
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\WebAsset\WebAssetManager;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryIcon;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryTranslation;
use Kunena\Forum\Libraries\Language\KunenaMultilanguage;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Version\KunenaVersion;

HTMLHelper::_('bootstrap.framework');

/** @var WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('core')
        ->useScript('jquery')
        ->useScript('multiselect');

Text::script('COM_KUNENA_CATEGORIES_ERROR_CHOOSE_ANOTHER_ALIAS');

$this->document->addScript(Uri::root() . 'administrator\components\com_kunena\tmpl\categories\edit.js');
?>

<div id="kunena" class="container-fluid">
    <div class="row">
        <div id="j-main-container" class="col-md-12" role="main">
            <div class="card card-block bg-faded p-2">
                <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=category') ?>"
                      method="post" id="adminForm" enctype="multipart/form-data"
                      name="adminForm">
                    <input type="hidden" name="task" value=""/>
                    <input type="hidden" name="catid" value="<?php echo intval($this->category->id); ?>"/>
                    <?php echo HTMLHelper::_('form.token'); ?>

                    <article class="data-block">
                        <div class="data-container">
                            <div class="tabbable-panel">
                                <div class="tabbable-line">
                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="tab-general-tab" data-bs-toggle="tab"
                                                    data-bs-target="#tab-general" type="button" role="tab"
                                                    aria-controls="tab-general"
                                                    aria-selected="true"><?php echo Text::_('COM_KUNENA_BASICSFORUMINFO'); ?></button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="tab-access-tab" data-bs-toggle="tab"
                                                    data-bs-target="#tab-access" type="button" role="tab"
                                                    aria-controls="tab-access"
                                                    aria-selected="true"><?php echo Text::_('COM_KUNENA_CATEGORY_PERMISSIONS'); ?></button>
                                        </li>
                                        <?php if (!$this->category->id || !$this->category->isSection()) :
                                            ?>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="tab-settings-tab" data-bs-toggle="tab"
                                                        data-bs-target="#tab-settings" type="button" role="tab"
                                                        aria-controls="tab-settings"
                                                        aria-selected="true"><?php echo Text::_('COM_KUNENA_ADVANCEDDESCINFO'); ?></button>
                                            </li>
                                        <?php endif; ?>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="tab-display-tab" data-bs-toggle="tab"
                                                    data-bs-target="#tab-display" type="button" role="tab"
                                                    aria-controls="tab-display"
                                                    aria-selected="true"><?php echo Text::_('COM_KUNENA_A_CATEGORY_CFG_TAB_DISPLAY'); ?></button>
                                        </li>
                                        <?php if (KunenaMultilanguage::hasLanguages()) : ?>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="tab-translations-tab" data-bs-toggle="tab"
                                                        data-bs-target="#tab-translations" type="button" role="tab"
                                                        aria-controls="tab-translations"
                                                        aria-selected="false"><?php echo Text::_('COM_KUNENA_CATEGORY_TRANSLATIONS'); ?></button>
                                            </li>
                                        <?php endif; ?>
                                        <?php if (!$this->category->id || !$this->category->isSection()) :
                                            ?>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="tab-mods-tab" data-bs-toggle="tab"
                                                        data-bs-target="#tab-mods" type="button" role="tab"
                                                        aria-controls="tab-mods"
                                                        aria-selected="true"><?php echo Text::_('COM_KUNENA_MODHEADER'); ?></button>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                    <div class="tab-content" id="myTabContent">
                                        <div class="tab-pane fade show active" id="tab-general" role="tabpanel"
                                             aria-labelledby="tab-general-tab">
                                            <fieldset>
                                                <table class="table table-striped">
                                                    <tr>
                                                        <td><?php echo Text::_('COM_KUNENA_PARENT'); ?></td>
                                                        <td>
                                                            <?php echo $this->options ['categories']; ?>
                                                            <p><?php echo Text::_('COM_KUNENA_PARENTDESC'); ?></p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo Text::_('COM_KUNENA_NAMEADD'); ?></td>
                                                        <td>
                                                            <input class="inputbox form-control w-100" type="text" name="name"
                                                                       value="<?php echo $this->escape($this->category->name); ?>"/>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo Text::_('COM_KUNENA_A_CATEGORY_ALIAS'); ?></td>
                                                        <td>
                                                            <label for="jform_aliases"></label><input class="inputbox form-control w-100" id="jform_aliases"
                                                                                                      type="text"
                                                                                                      name="alias"
                                                                                                      value="<?php echo $this->escape($this->category->alias); ?>"/>
                                                            <span id="aliasinfo"></span>
                                                            <?php
                                                            if ($this->options ['aliases']) :
                                                                ?>
                                                                <?php echo '<span id="aliascheck">' . $this->options ['aliases'] . '</span>'; ?>
                                                            <?php endif ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo Text::_('COM_KUNENA_PUBLISHED'); ?>:</td>
                                                        <td><?php echo $this->options ['published']; ?></td>
                                                    </tr>
                                                    <?php if (KunenaMultilanguage::hasLanguages()) : ?>
                                                        <tr>
                                                            <td><?php echo Text::_('COM_KUNENA_CATEGORY_LANGUAGE'); ?></td>
                                                            <td>
                                                                <select name="language" class="form-select w-100">
                                                                    <option value="*"<?php echo ($this->category->language ?: '*') === '*' ? ' selected' : ''; ?>><?php echo Text::_('JALL'); ?></option>
                                                                    <?php foreach (KunenaMultilanguage::getLanguages() as $code => $title) : ?>
                                                                        <option value="<?php echo $this->escape($code); ?>"<?php echo $this->category->language === $code ? ' selected' : ''; ?>><?php echo $this->escape($title); ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                                <p><?php echo Text::_('COM_KUNENA_CATEGORY_LANGUAGE_DESC'); ?></p>
                                                            </td>
                                                        </tr>
                                                    <?php endif; ?>
                                                    <tr>
                                                        <td><?php echo Text::_('COM_KUNENA_ICON'); ?></td>
                                                        <td>
                                                            <input class="inputbox form-control w-100" type="text" name="icon"
                                                                       value="<?php echo $this->escape($this->category->icon); ?>"/>
                                                            <p><?php echo Text::_('COM_KUNENA_ICON_DESC'); ?></p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo Text::_('COM_KUNENA_CATEGORY_ICON_IMAGE'); ?></td>
                                                        <td>
                                                            <?php $iconImage = $this->category->exists() ? KunenaCategoryIcon::getUrl((int) $this->category->id) : null; ?>
                                                            <?php if ($iconImage) : ?>
                                                                <p>
                                                                    <img src="<?php echo $this->escape($iconImage); ?>" alt="" style="max-width:64px;max-height:64px"/>
                                                                    <label class="ms-3">
                                                                        <input type="checkbox" name="icon_image_remove" value="1"/>
                                                                        <?php echo Text::_('COM_KUNENA_CATEGORY_ICON_IMAGE_REMOVE'); ?>
                                                                    </label>
                                                                </p>
                                                            <?php endif; ?>
                                                            <input class="form-control w-100" type="file" name="icon_image" accept="image/png,image/jpeg,image/gif,image/webp"/>
                                                            <p><?php echo Text::sprintf('COM_KUNENA_CATEGORY_ICON_IMAGE_DESC', (int) (KunenaCategoryIcon::MAX_SIZE / 1024)); ?></p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo Text::_('COM_KUNENA_CLASS_SFX'); ?></td>
                                                        <td>
                                                            <input class="inputbox form-control w-100" type="text"
                                                                       name="class_sfx"
                                                                       maxlength="20"
                                                                       value="<?php echo $this->escape($this->category->class_sfx); ?>"/>
                                                            <p><?php echo Text::_('COM_KUNENA_CLASS_SFXDESC'); ?></p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo Text::_('COM_KUNENA_DESCRIPTIONADD'); ?></td>
                                                        <td>
                                                            <textarea class="inputbox form-control w-100" rows="6"
                                                                      name="description"
                                                                      id="description"><?php echo $this->escape($this->category->description); ?></textarea>
                                                            <p><?php echo Text::_('COM_KUNENA_DESCRIPTIONADD_DESC'); ?></p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo Text::_('COM_KUNENA_HEADERADD'); ?></td>
                                                        <td>
                                                            <textarea class="inputbox form-control w-100" rows="6"
                                                                      name="headerdesc"
                                                                      id="headerdesc"><?php echo $this->escape($this->category->headerdesc); ?></textarea>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo Text::_('COM_KUNENA_CATEGORY_TOPIC_TEMPLATE'); ?></td>
                                                        <td>
                                                            <textarea class="inputbox form-control w-100" rows="6"
                                                                      name="topictemplate" id="topictemplate"><?php echo $this->escape($this->category->topictemplate); ?></textarea>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </fieldset>
                                        </div>

                                        <div class="tab-pane fade" id="tab-access" role="tabpanel"
                                             aria-labelledby="tab-access-tab">
                                            <fieldset>
                                                <table class="table table-striped">
                                                    <thead>
                                                    <tr>
                                                        <td><?php echo Text::_('COM_KUNENA_A_ACCESSTYPE_TITLE'); ?></td>
                                                        <td><?php echo $this->options ['accesstypes']; ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_ACCESSTYPE_DESC'); ?></td>
                                                    </tr>
                                                    </thead>
                                                    <?php foreach ($this->options ['accesslists'] as $accesstype => $accesslist) {
                                                        foreach ($accesslist as $accessinput) :
                                                            ?>
                                                            <tr class="kaccess kaccess-<?php echo $accesstype ?>"
                                                                style="<?php echo $this->category->accesstype != $accesstype ? 'display:none' : '' ?>">
                                                                <td><?php echo $accessinput['title'] ?></td>
                                                                <td><?php echo $accessinput['input'] ?></td>
                                                                <td><?php echo $accessinput['desc'] ?></td>
                                                            </tr>
                                                        <?php endforeach;
                                                    } ?>
                                                </table>
                                            </fieldset>
                                        </div>

                                        <?php if (!$this->category->id || !$this->category->isSection()) :
                                            ?>
                                            <div class="tab-pane fade" id="tab-settings" role="tabpanel"
                                                 aria-labelledby="tab-settings-tab">
                                                <fieldset>
                                                    <table class="table table-striped">
                                                        <tr>
                                                            <td><?php echo Text::_('COM_KUNENA_LOCKED1'); ?></td>
                                                            <td><?php echo $this->options ['forumLocked']; ?></td>
                                                            <td><?php echo Text::_('COM_KUNENA_LOCKEDDESC'); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td><?php echo Text::_('COM_KUNENA_REV'); ?></td>
                                                            <td><?php echo $this->options ['forumReview']; ?></td>
                                                            <td><?php echo Text::_('COM_KUNENA_REVDESC'); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td><?php echo Text::_('COM_KUNENA_CATEGORY_ANONYMOUS_ALLOW'); ?>
                                                                :
                                                            </td>
                                                            <td><?php echo $this->options ['allowAnonymous']; ?></td>
                                                            <td><?php echo Text::_('COM_KUNENA_CATEGORY_ANONYMOUS_ALLOW_DESC'); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td><?php echo Text::_('COM_KUNENA_CATEGORY_ANONYMOUS_DEFAULT'); ?>
                                                                :
                                                            </td>
                                                            <td><?php echo $this->options ['postAnonymous']; ?></td>
                                                            <td><?php echo Text::_('COM_KUNENA_CATEGORY_ANONYMOUS_DEFAULT_DESC'); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td><?php echo Text::_('COM_KUNENA_A_POLL_CATEGORIES_ALLOWED'); ?>
                                                                :
                                                            </td>
                                                            <td><?php echo $this->options ['allowPolls']; ?></td>
                                                            <td><?php echo Text::_('COM_KUNENA_A_POLL_CATEGORIES_ALLOWED_DESC'); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td><?php echo Text::_('COM_KUNENA_CATEGORY_CHANNELS'); ?>:
                                                            </td>
                                                            <td><?php echo $this->options ['channels']; ?></td>
                                                            <td><?php echo Text::_('COM_KUNENA_CATEGORY_CHANNELS_DESC'); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td><?php echo Text::_('COM_KUNENA_CATEGORY_TOPIC_ORDERING'); ?>
                                                                :
                                                            </td>
                                                            <td><?php echo $this->options ['topicOrdering']; ?></td>
                                                            <td><?php echo Text::_('COM_KUNENA_CATEGORY_TOPIC_ORDERING_DESC'); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td><?php echo Text::_('COM_KUNENA_A_CATEGORY_TOPICICONSET'); ?>
                                                                :
                                                            </td>
                                                            <td><?php echo $this->options ['categoryIconset']; ?></td>
                                                            <td><?php echo Text::_('COM_KUNENA_A_POLL_CATEGORY_TOPICICONSET_DESC'); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td><?php echo Text::_('COM_KUNENA_CATEGORY_INACTIVE_LOCK_DAYS'); ?>
                                                                :
                                                            </td>
                                                            <td>
                                                                <input class="form-control" type="number" min="0" step="1" name="params[inactiveLockDays]"
                                                                       value="<?php echo $this->escape((string) $this->category->params->get('inactiveLockDays', '')); ?>"/>
                                                            </td>
                                                            <td><?php echo Text::_('COM_KUNENA_CATEGORY_INACTIVE_LOCK_DAYS_DESC'); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td><?php echo Text::_('COM_KUNENA_RATING_CATEGORIES_ALLOWED'); ?>
                                                                :
                                                            </td>
                                                            <td><?php echo $this->options ['allowRatings']; ?></td>
                                                            <td><?php echo Text::_('COM_KUNENA_RATING_CATEGORIES_ALLOWED_DESC'); ?></td>
                                                        </tr>
                                                    </table>
                                                </fieldset>
                                            </div>
                                        <?php endif; ?>
                                        <div class="tab-pane fade" id="tab-display" role="tabpanel"
                                             aria-labelledby="tab-display-tab">
                                            <fieldset>
                                                <legend><?php echo Text::_('COM_KUNENA_A_CATEGORY_CFG_DISPLAY_INDEX_LEGEND'); ?></legend>
                                                <table class="table table-striped">
                                                    <tr>
                                                        <td><?php echo Text::_('COM_KUNENA_A_CATEGORY_CFG_DISPLAY_PARENT'); ?></td>
                                                        <td class="w-25"><?php echo $this->options ['display_parent']; ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_CATEGORY_CFG_DISPLAY_PARENT_DESC'); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo Text::_('COM_KUNENA_A_CATEGORY_CFG_DISPLAY_CHILDREN'); ?></td>
                                                        <td class="w-25"><?php echo $this->options ['display_children']; ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_CATEGORY_CFG_DISPLAY_CHILDREN_DESC'); ?></td>
                                                    </tr>
                                                </table>
                                            </fieldset>
                                        </div>
                                        <?php if (KunenaMultilanguage::hasLanguages()) :
                                            $translations = $this->category->id ? KunenaCategoryTranslation::getAll((int) $this->category->id) : [];
                                            ?>
                                            <div class="tab-pane fade" id="tab-translations" role="tabpanel"
                                                 aria-labelledby="tab-translations-tab">
                                                <p class="text-muted"><?php echo Text::_('COM_KUNENA_CATEGORY_TRANSLATIONS_DESC'); ?></p>
                                                <?php foreach (KunenaMultilanguage::getLanguages() as $code => $title) :
                                                    $row = $translations[$code] ?? null;
                                                    ?>
                                                    <fieldset class="mb-4">
                                                        <legend class="fs-6 fw-semibold"><?php echo $this->escape($title); ?> <small class="text-muted">(<?php echo $this->escape($code); ?>)</small></legend>
                                                        <table class="table table-striped">
                                                            <tr>
                                                                <td class="w-25"><?php echo Text::_('COM_KUNENA_NAMEADD'); ?></td>
                                                                <td><input class="inputbox form-control w-100" type="text" name="translations[<?php echo $this->escape($code); ?>][name]"
                                                                           value="<?php echo $this->escape($row->name ?? ''); ?>"/></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?php echo Text::_('COM_KUNENA_DESCRIPTIONADD'); ?></td>
                                                                <td><textarea class="inputbox form-control w-100" rows="4"
                                                                              name="translations[<?php echo $this->escape($code); ?>][description]"><?php echo $this->escape($row->description ?? ''); ?></textarea></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?php echo Text::_('COM_KUNENA_HEADERADD'); ?></td>
                                                                <td><textarea class="inputbox form-control w-100" rows="4"
                                                                              name="translations[<?php echo $this->escape($code); ?>][headerdesc]"><?php echo $this->escape($row->headerdesc ?? ''); ?></textarea></td>
                                                            </tr>
                                                        </table>
                                                    </fieldset>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!$this->category->id || !$this->category->isSection()) :
                                            ?>
                                            <div class="tab-pane fade" id="tab-mods" role="tabpanel"
                                                 aria-labelledby="tab-mods-tab">
                                                <fieldset>
                                                    <legend><?php echo Text::_('COM_KUNENA_MODSASSIGNED'); ?></legend>

                                                    <table class="table table-bordered table-striped">
                                                        <thead>
                                                        <tr>
                                                            <th><?php echo Text::_('COM_KUNENA_USERNAME'); ?></th>
                                                            <th><?php echo Text::_('COM_KUNENA_USRL_REALNAME'); ?></th>
                                                            <th class="col-md-1"><?php echo Text::_('JGRID_HEADING_ID'); ?></th>
                                                        </tr>
                                                        </thead>

                                                        <tbody>
                                                        <?php $i = 0;

                                                        if (empty($this->moderators)) :
                                                            ?>
                                                            <tr>
                                                                <td colspan="5"
                                                                    align="center"><?php echo Text::_('COM_KUNENA_NOMODS') ?></td>
                                                            </tr>
                                                        <?php else :
                                                            foreach ($this->moderators as $ml) :
                                                                ?>
                                                                <tr>
                                                                    <td><?php echo $this->escape($ml->username); ?></td>
                                                                    <td><?php echo $this->escape($ml->name); ?></td>
                                                                    <td><?php echo $this->escape($ml->userid); ?></td>
                                                                </tr>
                                                            <?php endforeach;
                                                        endif; ?>
                                                        </tbody>
                                                    </table>
                                                </fieldset>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                </form>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <div class="float-end small">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </div>
</div>
