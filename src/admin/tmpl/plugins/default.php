<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_plugins
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Version;
use Kunena\Forum\Libraries\Version\KunenaVersion;

/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('table.columns')
    ->useScript('multiselect');
$app           = Factory::getApplication();
$user          = $this->getCurrentUser();
$userId        = $user->id;
$listOrder     = $this->escape($this->state->get('list.ordering'));
$listDirn      = $this->escape($this->state->get('list.direction'));
$joomlaVersion = new Version();
?>
<form action="index.php?option=com_kunena&view=plugins" method="post" name="adminForm" id="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                <?php
                // Search tools bar
                echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]);
                ?>

                <table class="table itemList" id="pluginList">
                    <thead>
                        <tr>
                            <td class="w-1 text-center">
                                <?php echo HTMLHelper::_('grid.checkall'); ?>
                            </td>
                            <th scope="col" class="w-1 text-center">
                                <?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'enabled', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" class="title">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_PLUGINS_NAME_HEADING', 'name', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" class="w-10 d-none d-md-table-cell">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_PLUGINS_FOLDER_HEADING', 'folder', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" class="w-10 d-none d-md-table-cell">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_PLUGINS_ELEMENT_HEADING', 'element', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" class="w-10 d-none d-md-table-cell">
                                <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ACCESS', 'access', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" class="w-5 d-none d-md-table-cell">
                                <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'extension_id', $listDirn, $listOrder); ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($this->items as $i => $item) :
                            $canEdit    = $user->authorise('core.edit', 'com_plugins');
                            $canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $user->id || is_null($item->checked_out);
                            $canChange  = $user->authorise('core.edit.state', 'com_plugins') && $canCheckin;
                            $link = Route::_('index.php?option=com_plugins&client_id=0&task=plugin.edit&extension_id=' . $item->extension_id . '&tmpl=component&layout=modal', false);
                            if (version_compare($joomlaVersion->getShortVersion(), '5.1', '<')) {
                                $linkModal = HTMLHelper::_(
                                    'link',
                                    '#plugin' . $item->extension_id . 'Modal',
                                    $item->name,
                                    'data-bs-toggle="modal" id="title-' . $item->extension_id . '"'
                                );
                            } else {
                                $popupOptions = [
                                    'popupType'  => 'iframe',
                                    'textHeader' => Text::sprintf('COM_KUNENA_PLUGINS_MANAGER_PLUGIN', $item->name),
                                    'src'        => $link,
                                ];
                                $linkModal = HTMLHelper::_(
                                    'link',
                                    '#',
                                    $item->name,
                                    [
                                        'class'                 => 'alert-link',
                                        'data-joomla-dialog'    => $this->escape(json_encode($popupOptions, JSON_UNESCAPED_SLASHES)),
                                        'data-checkin-url'      => Route::_('index.php?option=com_plugins&task=plugins.checkin&format=json&cid[]=' . $item->extension_id),
                                        'data-close-on-message' => '',
                                        'data-reload-on-close'  => '',
                                    ],
                                );
                            }
                            if (!empty($item->note)) {
                                $linkModal .= "<div class=\"small\">" . Text::sprintf('JGLOBAL_LIST_NOTE', $this->escape($item->note)) . "</div>";
                            }
                        ?>
                            <tr class="row<?php echo $i % 2; ?>" data-dragable-group="<?php echo $item->folder; ?>">
                                <td class="text-center">
                                    <?php echo HTMLHelper::_('grid.id', $i, $item->extension_id, false, 'cid', 'cb', $item->name); ?>
                                </td>
                                <td class="text-center">
                                    <?php echo HTMLHelper::_('jgrid.published', $item->enabled, $i, 'plugins.', $canChange); ?>
                                </td>
                                <th scope="row">
                                    <?php if ($item->checked_out) : ?>
                                        <?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'plugins.', $canCheckin); ?>
                                    <?php endif; ?>
                                    <?php if ($canEdit) : ?>
                                        <?php echo $linkModal; ?>
                                        <?php if (version_compare($joomlaVersion->getShortVersion(), '5.1', '<')) : ?>
                                            <?php echo HTMLHelper::_(
                                                'bootstrap.renderModal',
                                                'plugin' . $item->extension_id . 'Modal',
                                                array(
                                                    'url'         => $link,
                                                    'title'       => Text::_($item->name),
                                                    'height'      => '400px',
                                                    'width'       => '800px',
                                                    'bodyHeight'  => '70',
                                                    'modalWidth'  => '80',
                                                    'closeButton' => false,
                                                    'backdrop'    => 'static',
                                                    'keyboard'    => false,
                                                    'footer'      => '<button type="button" class="btn btn-primary"'
                                                        . ' onclick="toggleInlineHelp({iframeSelector: \'#plugin' . $item->extension_id . 'Modal\'})">'
                                                        . Text::_('JINLINEHELP') . '</button>'
                                                        . '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"'
                                                        . ' onclick="Joomla.iframeButtonClick({iframeSelector: \'#plugin' . $item->extension_id . 'Modal\', buttonSelector: \'#closeBtn\'})">'
                                                        . Text::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</button>'
                                                        . '<button type="button" class="btn btn-primary" data-bs-dismiss="modal"'
                                                        . ' onclick="Joomla.iframeButtonClick({iframeSelector: \'#plugin' . $item->extension_id . 'Modal\', buttonSelector: \'#saveBtn\'})">'
                                                        . Text::_('JSAVE') . '</button>'
                                                        . '<button type="button" class="btn btn-success"'
                                                        . ' onclick="Joomla.iframeButtonClick({iframeSelector: \'#plugin' . $item->extension_id . 'Modal\', buttonSelector: \'#applyBtn\'})">'
                                                        . Text::_('JAPPLY') . '</button>',
                                                )
                                            ); ?>
                                        <?php endif; ?>
                                    <?php else : ?>
                                        <?php echo $item->name; ?>
                                    <?php endif; ?>
                                </th>
                                <td class="small d-none d-md-table-cell">
                                    <?php echo $this->escape($item->folder); ?>
                                </td>
                                <td class="small d-none d-md-table-cell">
                                    <?php echo $this->escape($item->element); ?>
                                </td>
                                <td class="small d-none d-md-table-cell">
                                    <?php echo $this->escape($item->access_level); ?>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <?php echo (int) $item->extension_id; ?>
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
            </div>
        </div>
    </div>
</form>
<div class="mt-3 text-center small">
    <?php echo KunenaVersion::getLongVersionHTML(); ?>
</div>
<?php if (version_compare($joomlaVersion->getShortVersion(), '5.1', '<')) : ?>
    <script>
        Array.from(document.querySelectorAll('.modal')).forEach(modalEl => {
            modalEl.addEventListener('hidden.bs.modal', () => {
                setTimeout(() => {
                    window.parent.location.reload();
                }, 1000);
            });
        });
        toggleInlineHelp = options => {
            const iframe = document.querySelector(`${options.iframeSelector} iframe`);
            if (iframe) {
                [].slice.call(iframe.contentWindow.document.querySelectorAll(`div.hide-aware-inline-help`)).forEach(elDiv => {
                    // Toggle the visibility of the node by toggling the 'd-none' Bootstrap class.
                    elDiv.classList.toggle('d-none'); // The ID of the description whose visibility is toggled.

                    const myId = elDiv.id; // The ID of the control described by this node (same ID, minus the '-desc' suffix).

                    const controlId = myId ? myId.substr(0, myId.length - 5) : null; // Get the control described by this node.

                    const elControl = controlId ? document.getElementById(controlId) : null; // Is this node hidden?

                    const isHidden = elDiv.classList.contains('d-none'); // If we do not have a control we will exit early

                    if (!controlId || !elControl) {
                        return;
                    } // Unset the aria-describedby attribute in the control when the description is hidden and vice–versa.


                    if (isHidden && elControl.hasAttribute('aria-describedby')) {
                        elControl.removeAttribute('aria-describedby');
                    } else if (!isHidden) {
                        elControl.setAttribute('aria-describedby', myId);
                    }
                });
            }
        };
    </script>
<?php endif; ?>