<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Smilies
 *
 * @copyright       Copyright (C) 2008 - 2024 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Version\KunenaVersion;

/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('table.columns')
    ->useScript('multiselect');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>
<div class="row">
    <div class="col-md-12">
        <div id="j-main-container" class="j-main-container">
            <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'emoticaons']); ?>
            <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'emoticons', Text::_('COM_KUNENA_A_EMOTICONS')); ?>
            <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=smilies') ?>" method="post" id="adminForm" name="adminForm">
                <?php
                // Search tools bar
                echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]);
                ?>
                <table class="table itemList" id="smileyList">
                    <thead>
                        <tr>
                            <td class="w-1 text-center">
                                <?php echo HTMLHelper::_('grid.checkall'); ?>
                            </td>
                            <th scope="col" class="w-5 text-center">
                                <?php echo Text::_('COM_KUNENA_EMOTICON'); ?>
                            </th>
                            <th scope="col" class="w-10">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_EMOTICONS_CODE', 'code', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" class="w-50">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_EMOTICONS_URL', 'location', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" class="w-1 text-center d-none d-lg-table-cell">
                                <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 0;
                        foreach ($this->items as $id => $row) :
                        ?>
                            <tr>
                                <td class="text-center">
                                    <?php echo HTMLHelper::_('grid.id', $i, intval($row->id)) ?>
                                </td>
                                <td class="d-none d-md-table-cell center">
                                    <a href="<?php echo Route::_('index.php?option=com_kunena&view=smiley&layout=edit&id=' . (int) $row->id); ?>">
                                        <img loading=lazy src="<?php echo $this->escape($this->ktemplate->getSmileyPath($row->location, true)); ?>" alt="<?php echo $this->escape($row->location); ?>" />
                                    </a>
                                </td>
                                <td>
                                    <?php echo $this->escape($row->code); ?>
                                </td>
                                <td>
                                    <?php echo $this->escape($row->location); ?>
                                </td>
                                <td>
                                    <?php echo $this->escape($row->id); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php // load the pagination. 
                ?>
                <?php echo $this->pagination->getListFooter(); ?>

                <input type="hidden" name="task" value="" />
                <input type="hidden" name="boxchecked" value="0" />
                <?php echo HTMLHelper::_('form.token'); ?>
            </form>
            <?php echo HTMLHelper::_('uitab.endTab'); ?>
            <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'upload', Text::_('COM_KUNENA_A_EMOTICONS_UPLOAD')); ?>
            <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena') ?>" id="uploadForm" method="post" enctype="multipart/form-data">
                <input type="file" id="file-upload" class="btn btn-outline-primary" name="Filedata" />
                <input type="submit" id="file-upload-submit" class="btn btn-outline-primary" value="<?php echo Text::_('COM_KUNENA_A_START_UPLOAD'); ?>" />
                <input type="hidden" name="task" value="smilies.smileyUpload" />
                <input type="hidden" name="boxchecked" value="0" />
                <?php echo HTMLHelper::_('form.token'); ?>
            </form>
            <?php echo HTMLHelper::_('uitab.endTab'); ?>
        </div>
    </div>
</div>
<div class="mt-3 text-center small">
    <?php echo KunenaVersion::getLongVersionHTML(); ?>
</div>