<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Topic
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

$base = Uri::getInstance()->toString(['scheme', 'host', 'port', 'path']);

// Changing a filter reloads the list in the background (the form works without the script too).
$this->addScript('assets/js/modlog.js');
?>

<h1><?php echo Text::_('COM_KUNENA_MODLOG'); ?></h1>
<p class="text-muted"><?php echo Text::_($this->isAdmin ? 'COM_KUNENA_MODLOG_DESC_ADMIN' : 'COM_KUNENA_MODLOG_DESC'); ?></p>

<?php if (!$this->collecting && $this->isAdmin) : ?>
    <div class="alert alert-warning"><?php echo Text::_('COM_KUNENA_MODLOG_NOT_COLLECTING'); ?></div>
<?php endif; ?>

<form action="<?php echo $this->escape($base); ?>" method="get" id="kmodlog-filter" class="row g-2 align-items-center mb-3" role="search">
    <div class="col-12 col-md-3">
        <select name="fop" class="form-select form-select-sm" aria-label="<?php echo Text::_('COM_KUNENA_MODLOG_ACTION'); ?>">
            <option value=""><?php echo Text::_('COM_KUNENA_MODLOG_ALL_ACTIONS'); ?></option>
            <?php foreach ($this->operations as $value => $label) : ?>
                <option value="<?php echo $this->escape($value); ?>"<?php echo $this->logFilters['operation'] === $value ? ' selected' : ''; ?>><?php echo $this->escape($label); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-12 col-md-3">
        <select name="fcat" class="form-select form-select-sm" aria-label="<?php echo Text::_('COM_KUNENA_CATEGORY'); ?>">
            <option value="0"><?php echo Text::_('COM_KUNENA_FILTER_ALL_CATEGORIES'); ?></option>
            <?php foreach ($this->logCategories as $id => $name) : ?>
                <option value="<?php echo (int) $id; ?>"<?php echo (int) $this->logFilters['catid'] === (int) $id ? ' selected' : ''; ?>><?php echo $this->escape($name); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-12 col-md-3">
        <input type="search" name="fuser" value="<?php echo $this->escape($this->logFilters['user']); ?>" class="form-control form-control-sm"
               placeholder="<?php echo Text::_('COM_KUNENA_MODLOG_MODERATOR'); ?>" aria-label="<?php echo Text::_('COM_KUNENA_MODLOG_MODERATOR'); ?>" autocomplete="off"/>
    </div>
    <div class="col-12 col-md-3 text-md-end">
        <button type="submit" class="btn btn-sm btn-outline-primary"><?php echo Text::_('COM_KUNENA_FILTER_APPLY'); ?></button>
        <a href="<?php echo $this->escape($base); ?>" class="btn btn-sm btn-outline-secondary" data-kmodlog-reset><?php echo Text::_('COM_KUNENA_FILTER_RESET'); ?></a>
    </div>
</form>

<div id="kmodlog-results">
<?php if (empty($this->entries)) : ?>
    <div class="alert alert-info"><?php echo Text::_('COM_KUNENA_MODLOG_EMPTY'); ?></div>
<?php else : ?>
    <div class="float-start" data-kmodlog-pagination>
        <?php echo $this->subLayout('Widget/Pagination/List')->set('pagination', $this->pagination->setDisplayedPages(4))->set('display', true); ?>
    </div>
    <div class="clearfix"></div>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead>
            <tr>
                <th scope="col"><?php echo Text::_('COM_KUNENA_MODLOG_TIME'); ?></th>
                <th scope="col"><?php echo Text::_('COM_KUNENA_MODLOG_MODERATOR'); ?></th>
                <th scope="col"><?php echo Text::_('COM_KUNENA_MODLOG_ACTION'); ?></th>
                <th scope="col"><?php echo Text::_('COM_KUNENA_CATEGORY'); ?></th>
                <th scope="col"><?php echo Text::_('COM_KUNENA_MODLOG_TOPIC'); ?></th>
                <th scope="col"><?php echo Text::_('COM_KUNENA_MODLOG_TARGET_USER'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($this->entries as $entry) :
                $topic = $this->topics[(int) $entry->topic_id] ?? null;
                ?>
                <tr>
                    <td class="text-nowrap"><?php echo HTMLHelper::_('date', (int) $entry->time, Text::_('DATE_FORMAT_LC5')); ?></td>
                    <td><?php echo $entry->user_id ? KunenaUserHelper::get((int) $entry->user_id)->getLink() : ''; ?></td>
                    <td><?php echo $this->escape($this->operations[$entry->operation] ?? $entry->operation); ?></td>
                    <td><?php echo $this->escape((string) $entry->category_name); ?></td>
                    <td>
                        <?php if ($topic && $topic->exists()) : ?>
                            <a href="<?php echo $topic->getUrl(); ?>"><?php echo $this->escape($topic->subject); ?></a>
                        <?php else : ?>
                            <?php echo $this->escape((string) $entry->topic_subject); ?>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $entry->target_user ? KunenaUserHelper::get((int) $entry->target_user)->getLink() : ''; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="float-start" data-kmodlog-pagination>
        <?php echo $this->subLayout('Widget/Pagination/List')->set('pagination', $this->pagination)->set('display', true); ?>
    </div>
    <div class="clearfix"></div>
<?php endif; ?>
</div>
