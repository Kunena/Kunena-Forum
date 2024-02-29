<?php

/**
 * Kunena Component
 *
 * @package       Kunena.Administrator.Template
 * @subpackage    Logs
 *
 * @copyright     Copyright (C) 2008 - 2024 Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Kunena\Forum\Libraries\Date\KunenaDate;
use Kunena\Forum\Libraries\Html\KunenaParser;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Version\KunenaVersion;

/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('table.columns')
    ->useScript('multiselect')
    ->useScript('jquery');
$inlineScript = <<<INLINESCRIPT
let showExtraData = document.querySelectorAll('.show-extradata');
let extraData = document.querySelectorAll('.extradata');
let selectToClipboard = document.querySelectorAll('.select_to_clipboard');

if (extraData) {
	extraData.forEach((data) => {
		data.style.display = 'none';
	});
}

if (showExtraData) {
	showExtraData.forEach((extraData) => {
		extraData.addEventListener("click", function() {
			event.preventDefault();
			if (this.nextElementSibling.style.display == 'none') {
				this.nextElementSibling.style.display = 'block';
			} else {
				this.nextElementSibling.style.display = 'none'
			}
		});
	});
};

if (selectToClipboard) {
    selectToClipboard.forEach(element => element.addEventListener("click", () => copySelectedText(element)));
};

function copySelectedText(element) {
  // Get the text content of the clicked element
  const text = element.textContent;

  // Create a temporary element to hold the text
  const tempElement = document.createElement("textarea");
  tempElement.value = text;
  tempElement.style.position = "absolute";
  tempElement.style.left = "-9999px";
  document.body.appendChild(tempElement);

  // Select the text in the temporary element
  tempElement.select();

  // Copy the selected text to the clipboard
  const result = document.execCommand("copy");

  // Remove the temporary element
  document.body.removeChild(tempElement);

  // Optionally show a confirmation message
  if (result) {
    console.log("Text copied to clipboard!");
  } else {
    console.log("Text could not be copied. Please try again.");
  }
};           
INLINESCRIPT;
$wa->addInlineScript($inlineScript, [], ['type' => 'module']);

$app       = Factory::getApplication();
$user      = $this->getCurrentUser();
$userId    = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=logs'); ?>" method="post" name="adminForm" id="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                <?php
                // Search tools bar
                echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]);
                ?>
                <table class="table itemList" id="logList">
                    <thead>
                        <tr>
                            <th scope="col" class="w-1 text-center">
                                <?php echo !$this->group ? HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder) : 'Count'; ?>
                            </th>
                            <th scope="col" class="w-5">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_LOG_TIME_SORT_LABEL', 'time', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" class="w-5">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_LOG_TYPE_SORT_LABEL', 'type', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" class="w-5">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_LOG_OPERATION_SORT_LABEL', 'operation', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" class="w-10">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_LOG_USER_SORT_LABEL', 'user', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" class="w-10">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_LOG_CATEGORY_SORT_LABEL', 'category', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" class="w-10">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_LOG_TOPIC_SORT_LABEL', 'topic', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" class="w-10">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_LOG_TARGET_USER_SORT_LABEL', 'target_user', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" class="w-10">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_KUNENA_GEN_IP', 'ip', $listDirn, $listOrder); ?>
                            </th>
                            <?php if (!$this->group) : ?>
                                <th scope="col" class="w-10">
                                    <?php echo Text::_('COM_KUNENA_LOG_MANAGER') ?>
                                </th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 0;
                        foreach ($this->items as $item) :
                            $date = new KunenaDate($item->time);
                        ?>
                            <tr>
                                <th class="w-1 text-center">
                                    <?php echo !$this->group ? $this->escape($item->id) : (int) $item->count; ?>
                                </th>
                                <td>
                                    <?php echo $date->toSql(); ?>
                                </td>
                                <td>
                                    <?php echo !$this->group || isset($this->group['type']) ? $this->escape($this->getType((int) $item->type)) : ''; ?>
                                </td>
                                <td>
                                    <?php echo !$this->group || isset($this->group['operation']) ? Text::_("COM_KUNENA_{$item->operation}") : ''; ?>
                                </td>
                                <td>
                                    <?php echo !$this->group || isset($this->group['user']) ? $this->escape($item->user_username) . ' <small>(' . $this->escape($item->user_id) . ')</small>' . '<br />' . $this->escape($item->user_name) : ''; ?>
                                </td>
                                <td>
                                    <?php echo !$this->group || isset($this->group['category']) ? $item->category_name . ' <small>(' . $this->escape($item->category_id) . ')</small>' : ''; ?>
                                </td>
                                <td>
                                    <?php echo !$this->group || isset($this->group['topic']) ? $item->topic_subject . ' <small>(' . $this->escape($item->topic_id) . ')</small>' : ''; ?>
                                </td>
                                <td>
                                    <?php echo !$this->group || isset($this->group['target_user']) ? $this->escape($item->targetuser_username) . ' <small>(' . $this->escape($item->target_user) . ')</small>' . '<br />' . $this->escape($item->targetuser_name) : ''; ?>
                                </td>
                                <td>
                                    <?php echo !$this->group || isset($this->group['ip']) ? $this->escape($item->ip) : ''; ?>
                                </td>
                                <?php if (!$this->group) : ?>
                                    <td>
                                        <?php if (isset($item->extra_data) && !empty($item->extra_data)) : ?>
                                        <?php endif; ?>
                                        <a class="show-extradata" href="#">
                                            <?php if ($this->escape($this->getType($item->type)) != 'ACT') : ?>
                                                <span class="icon-warning" aria-hidden="true"></span>
                                            <?php else : ?>
                                                <span class="icon-edit" aria-hidden="true"></span>
                                            <?php endif; ?>
                                            <?php echo !$this->group || isset($this->group['type']) ? $this->escape($this->getType($item->type)) : ''; ?>
                                        </a>
                                        <div style="display:none;" class="extradata">
                                            <?php echo '<p class="mt-2 select_to_clipboard wrapword">' . KunenaParser::plainBBCode((string) $item->data) . '</p>'; ?>
                                        </div>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php
                            $i++;
                        endforeach; ?>
                    </tbody>
                </table>

                <?php // load the pagination. 
                ?>
                <?php echo $this->pagination->getListFooter(); ?>

                <template id="joomla-dialog-clean"><?php echo $this->loadTemplate('clean'); ?></template>

                <input type="hidden" name="task" value="" />
                <input type="hidden" name="boxchecked" value="1" />
                <?php echo HTMLHelper::_('form.token'); ?>
            </div>
        </div>
    </div>
</form>
<div class="mt-3 text-center small">
    <?php echo KunenaVersion::getLongVersionHTML(); ?>
</div>