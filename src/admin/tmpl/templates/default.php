<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Templates
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Version\KunenaVersion;

/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('table.columns')
    ->useScript('multiselect');

$app       = Factory::getApplication();
$user      = $this->getCurrentUser();
$userId    = $user->get('id');
?>

<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=templates') ?>" method="post" id="adminForm" name="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                <div class="btn-group pull-right d-none d-md-block">
                    <label for="limit" class="element-invisible"><?php echo Text::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <td class="w-1 text-center"></td>
                            <th scope="col" class="w-10 d-none d-md-table-cell">
                                <?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_NAME'); ?>
                            </th>
                            <th scope="col" class="w-1 text-center">
                                <?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_DEFAULT'); ?>
                            </th>
                            <th scope="col" class="w-20 d-none d-md-table-cell">
                                <?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_AUTHOR'); ?>
                            </th>
                            <th scope="col" class="w-10 d-none d-md-table-cell">
                                <?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_VERSION'); ?>
                            </th>
                            <th scope="col" class="w-10 d-none d-md-table-cell">
                                <?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_DATE'); ?>
                            </th>
                            <th scope="col" class="w-15 d-none d-md-table-cell">
                                <?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_AUTHOR_URL'); ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 0;
                        $img_no  = '<span class="icon-unfeatured" aria-hidden="true"></span>';
                        $img_yes = '<span class="icon-color-featured icon-star" aria-hidden="true"></span>';

                        foreach ($this->templates as $id => $row) :
                            $templatepublishTask = $row->published ? 'template.unpublish' : 'template.publish';
                        ?>
                            <tr>
                                <td class="text-center">
                                    <input type="radio" id="cb<?php echo $i; ?>" name="cid[]" value="<?php echo $this->escape($row->directory); ?>" onclick="Joomla.isChecked(this.checked);" />
                                </td>
                                <th>
                                    <?php $img_path = Uri::root(true) . '/components/com_kunena/template/' . $row->directory . '/assets/images/template_thumbnail.png'; ?>
                                    <span class="editlinktip hasTip" title="<?php echo $this->escape($row->name . '::<img loading=lazy border="1" src="' . $this->escape($img_path) . '" name="imagelib" alt="' . Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_NO_PREVIEW') . '" width="200" height="145" />'); ?>">
                                        <a href="<?php echo Route::_('index.php?option=com_kunena&view=template&layout=edit&name=' . $this->escape($row->directory)); ?>" title="<?php echo $this->escape($row->name); ?>">
                                            <?php echo $this->escape($row->name); ?></a>
                                    </span>
                                </th>
                                <td class="d-none d-md-table-cell text-center tbody-icon">
                                    <a href="javascript: void(0);" onclick="return Joomla.listItemTask('cb<?php echo $i; ?>', '<?php echo $templatepublishTask; ?>')">
                                        <?php echo $row->published ? $img_yes : $img_no; ?>
                                    </a>
                                </td>
                                <td>
                                    <?php echo $row->authorEmail ? '<a href="mailto:' . $this->escape($row->authorEmail) . '">' . $this->escape($row->author) . '</a>' : $this->escape($row->author); ?>
                                </td>
                                <td>
                                    <?php echo $this->escape($row->version); ?>
                                </td>
                                <td>
                                    <?php echo $this->escape($row->creationdate); ?>
                                </td>
                                <td>
                                    <a href="<?php echo $this->escape($row->authorUrl); ?>" target="_blank" rel="noopener noreferrer"><?php echo $this->escape($row->authorUrl); ?></a>
                                </td>
                            </tr>
                        <?php $i++;
                        endforeach; ?>
                    </tbody>
                </table>
                <h2 class="mt-5"><?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_PREMIUM'); ?></h2>
                <?php if ($this->templatesxml !== false) : ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="w-10"><?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_PRICE'); ?></th>
                                <th class="w-5"><?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_TYPE'); ?></th>
                                <th class="w-5"><?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_VERSION'); ?></th>
                                <th class="w-15"><?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_TEMPLATE_NAME'); ?></th>
                                <th class="w-10"><?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_DATE'); ?></th>
                                <th class="w-10"><?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_AUTHOR'); ?></th>
                                <th class="w-10"><?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_DOWNLOAD'); ?></th>
                                <th class="w-25"><?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_AUTHOR_URL'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($this->templatesxml as $row) : ?>
                                <tr>
                                    <td>
                                        <?php echo $row->price; ?>
                                    </td>
                                    <td>
                                        <?php echo $row->type == 'paid-template' ? Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_PAID') : Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_FREE'); ?>
                                    </td>
                                    <td>
                                        <?php echo $row->version; ?>
                                    </td>
                                    <th>
                                        <span class="editlinktip hasTip" title="<?php echo $row->name . $this->escape('::<img loading=lazy border="1" src="' . $row->thumbnail . '" name="imagelib" alt="' . Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_NO_PREVIEW') . '" width="200" height="145" />'); ?>">
                                            <a href="<?php echo $row->detailsurl; ?>" target="_blank" rel="noopener noreferrer"><?php echo $row->name; ?></a>
                                        </span>
                                    </th>
                                    <td>
                                        <?php echo $row->created; ?>
                                    </td>
                                    <td>
                                        <a href="mailto:<?php echo $row->authoremail; ?>"><?php echo $row->author; ?></a>
                                    </td>
                                    <td>
                                        <a href="<?php echo $row->detailsurl; ?>" target="_blank" rel="noopener noreferrer"><?php echo Text::_('COM_KUNENA_A_TEMPLATE_MANAGER_DOWNLOAD'); ?></a>
                                    </td>
                                    <td>
                                        <a href="<?php echo $row->authorurl; ?>" target="_blank" rel="noopener noreferrer"><?php echo $row->authorurl; ?></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <div>
                        <div class="alert alert-danger"><?php echo Text::_('COM_KUNENA_ADMIN_TEMPLATE_MANAGER_ERROR_CANNOT_CONNECT_TO_KUNENA_SERVER'); ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?php // load the pagination. 
                ?>
                <?php echo $this->pagination->getListFooter(); ?>

                <input type="hidden" name="task" value="" />
                <input type="hidden" name="boxchecked" value="0" />
                <?php echo HTMLHelper::_('form.token'); ?>
            </div>
        </div>
    </div>
</form>
<div class="mt-3 text-center small">
    <?php echo KunenaVersion::getLongVersionHTML(); ?>
</div>