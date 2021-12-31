<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Categories
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
                      method="post" id="adminForm"
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
										<?php if (!$this->category->id || !$this->category->isSection())
											:
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
										<?php if (!$this->category->id || !$this->category->isSection())
											:
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
                                                            <input class="inputbox form-control" type="text" name="name"
                                                                   size="80"
                                                                   value="<?php echo $this->escape($this->category->name); ?>"/>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo Text::_('COM_KUNENA_A_CATEGORY_ALIAS'); ?></td>
                                                        <td>
                                                            <input class="inputbox form-control" id="jform_aliases"
                                                                   type="text"
                                                                   name="alias" size="80"
                                                                   value="<?php echo $this->escape($this->category->alias); ?>"/>
															<span id="aliasinfo"></span>
															<?php
															if ($this->options ['aliases'])
																:
																?>
																<?php echo '<span id="aliascheck">' . $this->options ['aliases'] . '</span>'; ?>
															<?php endif ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo Text::_('COM_KUNENA_PUBLISHED'); ?>:</td>
                                                        <td><?php echo $this->options ['published']; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo Text::_('COM_KUNENA_ICON'); ?></td>
                                                        <td>
                                                            <input class="inputbox form-control" type="text" name="icon"
                                                                   size="80"
                                                                   value="<?php echo $this->escape($this->category->icon); ?>"/>
                                                            <p><?php echo Text::_('COM_KUNENA_ICON_DESC'); ?></p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo Text::_('COM_KUNENA_CLASS_SFX'); ?></td>
                                                        <td>
                                                            <input class="inputbox form-control" type="text"
                                                                   name="class_sfx"
                                                                   size="20"
                                                                   maxlength="20"
                                                                   value="<?php echo $this->escape($this->category->class_sfx); ?>"/>
                                                            <p><?php echo Text::_('COM_KUNENA_CLASS_SFXDESC'); ?></p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo Text::_('COM_KUNENA_DESCRIPTIONADD'); ?></td>
                                                        <td>
															<textarea class="inputbox form-control" cols="50" rows="6"
                                                                      name="description"
                                                                      id="description"
                                                                      style="width: 500px;"><?php echo $this->escape($this->category->description); ?></textarea>
                                                            <p><?php echo Text::_('COM_KUNENA_DESCRIPTIONADD_DESC'); ?></p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo Text::_('COM_KUNENA_HEADERADD'); ?></td>
                                                        <td>
															<textarea class="inputbox form-control" cols="50" rows="6"
                                                                      name="headerdesc"
                                                                      id="headerdesc"
                                                                      style="width: 500px;"><?php echo $this->escape($this->category->headerdesc); ?></textarea>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo Text::_('COM_KUNENA_CATEGORY_TOPIC_TEMPLATE'); ?></td>
                                                        <td>
															<textarea class="inputbox form-control" cols="50" rows="6"
                                                                      name="topictemplate" id="topictemplate"
                                                                      style="width: 500px;"><?php echo $this->escape($this->category->topictemplate); ?></textarea>
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
													<?php foreach ($this->options ['accesslists'] as $accesstype => $accesslist)
													{
														foreach ($accesslist as $accessinput)
														    :
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

										<?php if (!$this->category->id || !$this->category->isSection())
											:
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
                                                        <td><?php echo $this->options ['display_parent']; ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_CATEGORY_CFG_DISPLAY_PARENT_DESC'); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo Text::_('COM_KUNENA_A_CATEGORY_CFG_DISPLAY_CHILDREN'); ?></td>
                                                        <td><?php echo $this->options ['display_children']; ?></td>
                                                        <td><?php echo Text::_('COM_KUNENA_A_CATEGORY_CFG_DISPLAY_CHILDREN_DESC'); ?></td>
                                                    </tr>
                                                </table>
                                            </fieldset>
                                        </div>
										<?php if (!$this->category->id || !$this->category->isSection())
											:
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

														if (empty($this->moderators))
															:
															?>
                                                            <tr>
                                                                <td colspan="5"
                                                                    align="center"><?php echo Text::_('COM_KUNENA_NOMODS') ?></td>
                                                            </tr>
														<?php else
															:
															foreach ($this->moderators as $ml)
																:
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
    <div class="pull-right small">
		<?php echo KunenaVersion::getLongVersionHTML(); ?>
    </div>
</div>
