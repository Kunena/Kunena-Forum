<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Category
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryIcon;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryTranslation;
use Kunena\Forum\Libraries\Language\KunenaMultilanguage;
use Kunena\Forum\Libraries\Route\KunenaRoute;

HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('dropdown.init');

Text::script('COM_KUNENA_CATEGORIES_ERROR_CHOOSE_ANOTHER_ALIAS');

Factory::getApplication()->getDocument()->addScript(Uri::root() . 'media/kunena/core/js/category-edit.js');
?>

<div class="card">
    <div class="card-header">
        <i class="icon-list-view"></i>
        <?php echo Text::_('COM_KUNENA_CPANEL_LABEL_CATEGORIES') ?>
        : <?php echo $this->category->exists() ? $this->escape($this->category->name) : Text::_((int) $this->category->parentid ? 'COM_KUNENA_CATEGORY_NEW_FRONTEND' : 'COM_KUNENA_CATEGORY_NEW_SECTION'); ?>
    </div>
    <div class="card-body">
        <form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=category&catid=' . (int) $this->category->id) ?>"
              method="post" id="adminForm" enctype="multipart/form-data"
              name="adminForm">
            <input type="hidden" name="task" value="save"/>
            <input type="hidden" name="catid" value="<?php echo \intval($this->category->id); ?>"/>
            <?php echo HTMLHelper::_('form.token'); ?>

            <article class="data-block">
                <div class="data-container">
                    <div class="tabbable-panel">
                        <div class="tabbable-line">
                            <ul class="nav nav-tabs">
                                <li class="nav-item active">
                                    <a class="nav-link active" href="#tab-general" data-bs-toggle="tab">
                                        <?php echo Text::_('COM_KUNENA_BASICSFORUMINFO'); ?>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#tab-access" data-bs-toggle="tab">
                                        <?php echo Text::_('COM_KUNENA_CATEGORY_PERMISSIONS'); ?>
                                    </a>
                                </li>
                                <?php if (!$this->category->id || !$this->category->isSection()) :
                                    ?>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#tab-settings" data-bs-toggle="tab">
                                            <?php echo Text::_('COM_KUNENA_ADVANCEDDESCINFO'); ?>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="#tab-display" data-bs-toggle="tab">
                                        <?php echo Text::_('COM_KUNENA_A_CATEGORY_CFG_TAB_DISPLAY'); ?>
                                    </a>
                                </li>
                                <?php if (KunenaMultilanguage::hasLanguages()) : ?>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#tab-translations" data-bs-toggle="tab">
                                            <?php echo Text::_('COM_KUNENA_CATEGORY_TRANSLATIONS'); ?>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (!$this->category->id || !$this->category->isSection()) :
                                    ?>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#tab-mods" data-bs-toggle="tab">
                                            <?php echo Text::_('COM_KUNENA_MODHEADER'); ?>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                            <div class="tab-content d-block"
                                 style="padding-bottom: 9px; border-bottom: 1px solid #ddd;">
                                <div class="tab-pane active" id="tab-general">
                                    <fieldset class="table-responsive">
                                        <table class="table table-striped">
                                            <tr>
                                                <td style="width:20%;"><?php echo Text::_('COM_KUNENA_PARENT'); ?></td>
                                                <td style="width:20%;">
                                                    <?php echo $this->lists['categories']; ?>
                                                    <p><?php echo Text::_('COM_KUNENA_PARENTDESC'); ?></p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width:20%;"><?php echo Text::_('COM_KUNENA_NAMEADD'); ?></td>
                                                <td style="width:20%;">
                                                    <label>
                                                        <input class="inputbox form-control" type="text" name="name"
                                                               size="80"
                                                               value="<?php echo $this->escape($this->category->name); ?>"/>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width:20%;"><?php echo Text::_('COM_KUNENA_A_CATEGORY_ALIAS'); ?></td>
                                                <td style="width:20%;">
                                                    <label for="jform_aliases"></label><input class="inputbox form-control" id="jform_aliases"
                                                                                              type="text"
                                                                                              name="alias" size="80"
                                                                                              value="<?php echo $this->escape($this->category->alias); ?>"/>
                                                    <?php
                                                    if ($this->lists['aliases']) :
                                                        ?>
                                                        <?php echo '<span id="aliascheck">' . $this->lists['aliases'] . '</span>'; ?>
                                                    <?php endif ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width:20%;"><?php echo Text::_('COM_KUNENA_PUBLISHED'); ?>:</td>
                                                <td style="width:20%;"><?php echo $this->lists['published']; ?></td>
                                            </tr>
                                            <?php if (KunenaMultilanguage::hasLanguages()) : ?>
                                                <tr>
                                                    <td style="width:20%;"><?php echo Text::_('COM_KUNENA_CATEGORY_LANGUAGE'); ?></td>
                                                    <td style="width:20%;">
                                                        <select name="language" class="form-select">
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
                                                <td style="width:20%;"><?php echo Text::_('COM_KUNENA_ICON'); ?></td>
                                                <td style="width:20%;">
                                                    <label>
                                                        <input class="inputbox form-control" type="text" name="icon"
                                                               size="80"
                                                               value="<?php echo $this->escape($this->category->icon); ?>"/>
                                                    </label>
                                                    <p><?php echo Text::_('COM_KUNENA_ICON_DESC'); ?></p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width:20%;"><?php echo Text::_('COM_KUNENA_CATEGORY_ICON_IMAGE'); ?></td>
                                                <td style="width:20%;">
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
                                                    <input class="form-control" type="file" name="icon_image" accept="image/png,image/jpeg,image/gif,image/webp"/>
                                                    <p><?php echo Text::sprintf('COM_KUNENA_CATEGORY_ICON_IMAGE_DESC', (int) (KunenaCategoryIcon::MAX_SIZE / 1024)); ?></p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width:20%;"><?php echo Text::_('COM_KUNENA_CLASS_SFX'); ?></td>
                                                <td style="width:20%;">
                                                    <label>
                                                        <input class="inputbox form-control" type="text"
                                                               name="class_sfx"
                                                               size="20"
                                                               maxlength="20"
                                                               value="<?php echo $this->escape($this->category->class_sfx); ?>"/>
                                                    </label>
                                                    <p><?php echo Text::_('COM_KUNENA_CLASS_SFXDESC'); ?></p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width:20%;"><?php echo Text::_('COM_KUNENA_DESCRIPTIONADD'); ?></td>
                                                <td style="width:20%;">
                                                <textarea class="inputbox form-control" cols="50" rows="6"
                                                          name="description"
                                                          id="description"
                                                          style="width: 500px;"><?php echo $this->escape($this->category->description); ?></textarea>
                                                    <p><?php echo Text::_('COM_KUNENA_DESCRIPTIONADD_DESC'); ?></p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width:20%;"><?php echo Text::_('COM_KUNENA_HEADERADD'); ?></td>
                                                <td style="width:20%;">
                                                <textarea class="inputbox form-control" cols="50" rows="6"
                                                          name="headerdesc"
                                                          id="headerdesc"
                                                          style="width: 500px;"><?php echo $this->escape($this->category->headerdesc); ?></textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width:20%;"><?php echo Text::_('COM_KUNENA_CATEGORY_TOPIC_TEMPLATE'); ?></td>
                                                <td style="width:20%;">
                                                <textarea class="inputbox form-control" cols="50" rows="6"
                                                          name="topictemplate" id="topictemplate"
                                                          style="width: 500px;"><?php echo $this->escape($this->category->topictemplate); ?></textarea>
                                                </td>
                                            </tr>
                                        </table>
                                    </fieldset>
                                </div>

                                <div class="tab-pane" id="tab-access">
                                    <fieldset class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                            <tr>
                                                <td style="width:20%;"><?php echo Text::_('COM_KUNENA_A_ACCESSTYPE_TITLE'); ?></td>
                                                <td style="width:30%;"><?php echo $this->lists['accesstypes']; ?></td>
                                                <td style="width:50%;"><?php echo Text::_('COM_KUNENA_A_ACCESSTYPE_DESC'); ?></td>
                                            </tr>
                                            </thead>
                                            <?php foreach ($this->lists['accesslists'] as $accesstype => $accesslist) {
                                                foreach ($accesslist as $accessinput) :
                                                    ?>
                                                    <tr class="kaccess kaccess-<?php echo $accesstype ?>"
                                                        style="<?php echo $this->category->accesstype != $accesstype ? 'display:none' : '' ?>">
                                                        <td style="width:20%;"><?php echo $accessinput['title'] ?></td>
                                                        <td style="width:20%;"><?php echo $accessinput['input'] ?></td>
                                                        <td style="width:20%;"><?php echo $accessinput['desc'] ?></td>
                                                    </tr>
                                                <?php endforeach;
                                            } ?>
                                        </table>
                                    </fieldset>
                                </div>

                                <?php if (!$this->category->id || !$this->category->isSection()) :
                                    ?>
                                    <div class="tab-pane" id="tab-settings">
                                        <fieldset class="table-responsive">
                                            <table class="table table-striped">
                                                <tr>
                                                    <td style="width:20%;"><?php echo Text::_('COM_KUNENA_LOCKED1'); ?></td>
                                                    <td style="width:30%;"><?php echo $this->lists['forumLocked']; ?></td>
                                                    <td style="width:50%;"><?php echo Text::_('COM_KUNENA_LOCKEDDESC'); ?></td>
                                                </tr>
                                                <tr>
                                                    <td style="width:20%;"><?php echo Text::_('COM_KUNENA_REV'); ?></td>
                                                    <td style="width:30%;"><?php echo $this->lists['forumReview']; ?></td>
                                                    <td style="width:50%;"><?php echo Text::_('COM_KUNENA_REVDESC'); ?></td>
                                                </tr>
                                                <tr>
                                                    <td style="width:20%;"><?php echo Text::_('COM_KUNENA_CATEGORY_ANONYMOUS_ALLOW'); ?>
                                                        :
                                                    </td>
                                                    <td style="width:30%;"><?php echo $this->lists['allowAnonymous']; ?></td>
                                                    <td style="width:50%;"><?php echo Text::_('COM_KUNENA_CATEGORY_ANONYMOUS_ALLOW_DESC'); ?></td>
                                                </tr>
                                                <tr>
                                                    <td style="width:20%;"><?php echo Text::_('COM_KUNENA_CATEGORY_ANONYMOUS_DEFAULT'); ?>
                                                        :
                                                    </td>
                                                    <td style="width:30%;"><?php echo $this->lists['postAnonymous']; ?></td>
                                                    <td style="width:50%;"><?php echo Text::_('COM_KUNENA_CATEGORY_ANONYMOUS_DEFAULT_DESC'); ?></td>
                                                </tr>
                                                <tr>
                                                    <td style="width:20%;"><?php echo Text::_('COM_KUNENA_A_POLL_CATEGORIES_ALLOWED'); ?>
                                                        :
                                                    </td>
                                                    <td style="width:30%;"><?php echo $this->lists['allowPolls']; ?></td>
                                                    <td style="width:50%;"><?php echo Text::_('COM_KUNENA_A_POLL_CATEGORIES_ALLOWED_DESC'); ?></td>
                                                </tr>
                                                <tr>
                                                    <td style="width:20%;"><?php echo Text::_('COM_KUNENA_CATEGORY_CHANNELS'); ?>
                                                        :
                                                    </td>
                                                    <td style="width:30%;"><?php echo $this->lists['channels']; ?></td>
                                                    <td style="width:50%;"><?php echo Text::_('COM_KUNENA_CATEGORY_CHANNELS_DESC'); ?></td>
                                                </tr>
                                                <tr>
                                                    <td style="width:20%;"><?php echo Text::_('COM_KUNENA_CATEGORY_TOPIC_ORDERING'); ?>
                                                        :
                                                    </td>
                                                    <td style="width:30%;"><?php echo $this->lists['topicOrdering']; ?></td>
                                                    <td style="width:50%;"><?php echo Text::_('COM_KUNENA_CATEGORY_TOPIC_ORDERING_DESC'); ?></td>
                                                </tr>
                                                <tr>
                                                    <td style="width:20%;"><?php echo Text::_('COM_KUNENA_A_CATEGORY_TOPICICONSET'); ?>
                                                        :
                                                    </td>
                                                    <td style="width:30%;"><?php echo $this->lists['categoryIconset']; ?></td>
                                                    <td style="width:50%;"><?php echo Text::_('COM_KUNENA_A_POLL_CATEGORY_TOPICICONSET_DESC'); ?></td>
                                                </tr>
                                                <tr>
                                                    <td style="width:20%;"><?php echo Text::_('COM_KUNENA_RATING_CATEGORIES_ALLOWED'); ?>
                                                        :
                                                    </td>
                                                    <td style="width:30%;"><?php echo $this->lists['allowRatings']; ?></td>
                                                    <td style="width:50%;"><?php echo Text::_('COM_KUNENA_RATING_CATEGORIES_ALLOWED_DESC'); ?></td>
                                                </tr>
                                                <tr>
                                                    <td style="width:20%;"><?php echo Text::_('COM_KUNENA_CATEGORY_TEASER'); ?>:</td>
                                                    <td style="width:30%;">
                                                        <?php $teaser = (string) $this->category->params->get('teaser', ''); ?>
                                                        <select class="form-select" name="params[teaser]">
                                                            <option value=""<?php echo $teaser === '' ? ' selected="selected"' : ''; ?>><?php echo Text::_('COM_KUNENA_CATEGORY_TEASER_GLOBAL'); ?></option>
                                                            <option value="1"<?php echo $teaser === '1' ? ' selected="selected"' : ''; ?>><?php echo Text::_('COM_KUNENA_CATEGORY_TEASER_HIDE'); ?></option>
                                                            <option value="0"<?php echo $teaser === '0' ? ' selected="selected"' : ''; ?>><?php echo Text::_('COM_KUNENA_CATEGORY_TEASER_SHOW'); ?></option>
                                                        </select>
                                                    </td>
                                                    <td style="width:50%;"><?php echo Text::_('COM_KUNENA_CATEGORY_TEASER_DESC'); ?></td>
                                                </tr>
                                                <tr>
                                                    <td style="width:20%;"><?php echo Text::_('COM_KUNENA_CATEGORY_OWN_TOPICS_ONLY'); ?>:</td>
                                                    <td style="width:30%;">
                                                        <select class="form-select" name="params[ownTopicsOnly]">
                                                            <option value="0"><?php echo Text::_('JNO'); ?></option>
                                                            <option value="1"<?php echo $this->category->params->get('ownTopicsOnly') ? ' selected="selected"' : ''; ?>><?php echo Text::_('JYES'); ?></option>
                                                        </select>
                                                    </td>
                                                    <td style="width:50%;"><?php echo Text::_('COM_KUNENA_CATEGORY_OWN_TOPICS_ONLY_DESC'); ?></td>
                                                </tr>
                                                <tr>
                                                    <td style="width:20%;"><?php echo Text::_('COM_KUNENA_CATEGORY_INACTIVE_LOCK_DAYS'); ?>:</td>
                                                    <td style="width:30%;">
                                                        <input class="form-control" type="number" min="0" step="1" name="params[inactiveLockDays]"
                                                               value="<?php echo $this->escape((string) $this->category->params->get('inactiveLockDays', '')); ?>"/>
                                                    </td>
                                                    <td style="width:50%;"><?php echo Text::_('COM_KUNENA_CATEGORY_INACTIVE_LOCK_DAYS_DESC'); ?></td>
                                                </tr>
                                            </table>
                                        </fieldset>
                                    </div>
                                <?php endif; ?>
                                <div class="tab-pane" id="tab-display">
                                    <fieldset class="table-responsive">
                                        <legend><?php echo Text::_('COM_KUNENA_A_CATEGORY_CFG_DISPLAY_INDEX_LEGEND'); ?></legend>
                                        <table class="table table-striped">
                                            <tr>
                                                <td style="width:20%;"><?php echo Text::_('COM_KUNENA_A_CATEGORY_CFG_DISPLAY_PARENT'); ?></td>
                                                <td style="width:30%;"><?php echo $this->lists['display_parent']; ?></td>
                                                <td style="width:50%;"><?php echo Text::_('COM_KUNENA_A_CATEGORY_CFG_DISPLAY_PARENT_DESC'); ?></td>
                                            </tr>
                                            <tr>
                                                <td style="width:20%;"><?php echo Text::_('COM_KUNENA_A_CATEGORY_CFG_DISPLAY_CHILDREN'); ?></td>
                                                <td style="width:30%;"><?php echo $this->lists['display_children']; ?></td>
                                                <td style="width:50%;"><?php echo Text::_('COM_KUNENA_A_CATEGORY_CFG_DISPLAY_CHILDREN_DESC'); ?></td>
                                            </tr>
                                        </table>
                                    </fieldset>
                                </div>
                                <?php if (KunenaMultilanguage::hasLanguages()) :
                                    $translations = $this->category->id ? KunenaCategoryTranslation::getAll((int) $this->category->id) : [];
                                    ?>
                                    <div class="tab-pane" id="tab-translations">
                                        <p class="text-muted"><?php echo Text::_('COM_KUNENA_CATEGORY_TRANSLATIONS_DESC'); ?></p>
                                        <?php foreach (KunenaMultilanguage::getLanguages() as $code => $title) :
                                            $row = $translations[$code] ?? null;
                                            ?>
                                            <fieldset class="mb-4">
                                                <legend class="fs-6 fw-semibold"><?php echo $this->escape($title); ?> <small class="text-muted">(<?php echo $this->escape($code); ?>)</small></legend>
                                                <table class="table table-striped">
                                                    <tr>
                                                        <td style="width:20%;"><?php echo Text::_('COM_KUNENA_NAMEADD'); ?></td>
                                                        <td><input class="inputbox form-control" type="text" name="translations[<?php echo $this->escape($code); ?>][name]"
                                                                   value="<?php echo $this->escape($row->name ?? ''); ?>"/></td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo Text::_('COM_KUNENA_DESCRIPTIONADD'); ?></td>
                                                        <td><textarea class="inputbox form-control" rows="4"
                                                                      name="translations[<?php echo $this->escape($code); ?>][description]"><?php echo $this->escape($row->description ?? ''); ?></textarea></td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo Text::_('COM_KUNENA_HEADERADD'); ?></td>
                                                        <td><textarea class="inputbox form-control" rows="4"
                                                                      name="translations[<?php echo $this->escape($code); ?>][headerdesc]"><?php echo $this->escape($row->headerdesc ?? ''); ?></textarea></td>
                                                    </tr>
                                                </table>
                                            </fieldset>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!$this->category->id || !$this->category->isSection()) :
                                    ?>
                                    <div class="tab-pane" id="tab-mods">
                                        <fieldset class="table-responsive">
                                            <legend><?php echo Text::_('COM_KUNENA_MODSASSIGNED'); ?></legend>

                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                <tr>
                                                    <th style="width:20%;"><?php echo Text::_('COM_KUNENA_USERNAME'); ?></th>
                                                    <th style="width:30%;"><?php echo Text::_('COM_KUNENA_USRL_REALNAME'); ?></th>
                                                    <th style="width:50%;"><?php echo Text::_('JGRID_HEADING_ID'); ?></th>
                                                </tr>
                                                </thead>

                                                <tbody>
                                                <?php if (empty($this->moderators)) :
                                                    ?>
                                                    <tr>
                                                        <td colspan="5"
                                                            style="text-align:center;"><?php echo Text::_('COM_KUNENA_NOMODS') ?></td>
                                                    </tr>
                                                <?php else :
                                                    foreach ($this->moderators as $ml) :
                                                        ?>
                                                        <tr>
                                                            <td style="width:20%;"><?php echo $this->escape($ml->username); ?></td>
                                                            <td style="width:20%;"><?php echo $this->escape($ml->name); ?></td>
                                                            <td style="width:20%;"><?php echo $this->escape($ml->userid); ?></td>
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
            <div class="clearfix"></div>
            <div class="form-group row pt-3">
                <div class="col-md-10 center">
                    <input name="submit" class="btn btn-outline-primary" type="submit"
                           value="<?php echo Text::_('COM_KUNENA_SAVE'); ?>"/>
                    <input onclick="window.history.back();" name="cancel" class="btn btn-outline-default border"
                           type="button"
                           value="<?php echo Text::_('COM_KUNENA_CANCEL'); ?>"/>
                </div>
            </div>
        </form>
        <?php if ($this->category->exists()) : ?>
            <form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=category&catid=' . (int) $this->category->id); ?>" method="post"
                  class="mt-4 pt-3 border-top"
                  onsubmit="return confirm(<?php echo $this->escape(json_encode(Text::sprintf('COM_KUNENA_CATEGORY_DELETE_CONFIRM', $this->category->name, (int) $this->category->getTopics(), (int) $this->category->getPosts()))); ?>);">
                <input type="hidden" name="task" value="delete"/>
                <input type="hidden" name="catid" value="<?php echo \intval($this->category->id); ?>"/>
                <?php echo HTMLHelper::_('form.token'); ?>
                <button type="submit" class="btn btn-outline-danger"><?php echo Text::_('COM_KUNENA_CATEGORY_DELETE'); ?></button>
            </form>
        <?php endif; ?>
    </div>
</div>
