<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Agora
 * @subpackage      Layout.Announcement
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Icons\KunenaIcons;
use Kunena\Forum\Libraries\Route\KunenaRoute;

echo $this->subLayout('Widget/Datepicker');

// The calendar beside each date, joined to its field (the datepicker opens on the element .input-group.date).
$calendar = '<span class="input-group-text" aria-hidden="true">' . KunenaIcons::calendar() . '</span>';
$dates    = [
    ['created', 'COM_KUNENA_ANN_DATE', 'addcreated'],
    ['publish_up', 'COM_KUNENA_ANN_DATE_UP', 'publish_up'],
    ['publish_down', 'COM_KUNENA_ANN_DATE_DOWN', 'publish_down'],
];
?>
<h2 class="agora-ann-edit-title">
    <?php echo Text::_('COM_KUNENA_ANN_ANNOUNCEMENTS'); ?>:
    <?php echo $this->announcement->exists() ? Text::_('COM_KUNENA_ANN_EDIT') : Text::_('COM_KUNENA_ANN_ADD'); ?>
</h2>

<div class="agora-ann-edit">
    <form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=announcement'); ?>" method="post"
          name="editform" id="editform">
        <input type="hidden" name="task" value="save"/>
        <?php echo $this->displayInput('id'); ?>
        <?php echo HTMLHelper::_('form.token'); ?>

        <div class="mb-3">
            <label class="form-label" for="ann-title"><?php echo Text::_('COM_KUNENA_ANN_TITLE'); ?></label>
            <?php echo $this->displayInput('title', 'id="ann-title" class="form-control" required placeholder="' . Text::_('COM_KUNENA_ANN_LABEL_PLACEHOLDER_TITLE') . '"'); ?>
        </div>

        <div class="mb-3">
            <label class="form-label" for="ann-short"><?php echo Text::_('COM_KUNENA_ANN_SORTTEXT'); ?></label>
            <?php echo $this->displayInput('sdescription', 'id="ann-short" rows="4" class="form-control" required placeholder="' . Text::_('COM_KUNENA_ANN_LABEL_PLACEHOLDER_SDESCRIPTION') . '"'); ?>
        </div>

        <div class="mb-3">
            <label class="form-label" for="ann-long"><?php echo Text::_('COM_KUNENA_ANN_LONGTEXT'); ?></label>
            <?php echo $this->displayInput('description', 'id="ann-long" rows="10" class="form-control" placeholder="' . Text::_('COM_KUNENA_ANN_LABEL_PLACEHOLDER_DESCRITPION') . '"'); ?>
        </div>

        <div class="row g-3 mb-3">
            <?php foreach ($dates as [$field, $label, $id]) : ?>
                <div class="col-md-4">
                    <span class="form-label d-block" id="<?php echo $id; ?>-label"><?php echo Text::_($label); ?></span>
                    <div class="input-group date" role="group" aria-labelledby="<?php echo $id; ?>-label">
                        <?php echo $this->displayInput($field, $calendar, $id); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="row g-3">
            <div class="col-6 col-md-4">
                <label class="form-label" for="ann-showdate"><?php echo Text::_('COM_KUNENA_ANN_SHOWDATE'); ?></label>
                <?php echo $this->displayInput('showdate', 'class="form-select"', 'ann-showdate'); ?>
            </div>
            <div class="col-6 col-md-4">
                <label class="form-label" for="ann-publish"><?php echo Text::_('COM_KUNENA_ANN_PUBLISH'); ?></label>
                <?php echo $this->displayInput('published', 'class="form-select"', 'ann-publish'); ?>
            </div>
        </div>

        <div class="agora-ann-edit-actions">
            <input onclick="window.history.back();" name="cancel" class="btn btn-outline-secondary" type="button"
                   value="<?php echo Text::_('COM_KUNENA_CANCEL'); ?>"/>
            <input name="submit" class="btn btn-primary" type="submit" value="<?php echo Text::_('COM_KUNENA_SAVE'); ?>"/>
        </div>
    </form>
</div>
