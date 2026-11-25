<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Tools
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Tools\KunenaAnnouncementsReview;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$items   = $this->tool['items'];
$shown   = array_filter($items, static fn ($item) => \in_array($item['state'], ['expired', 'old'], true));
$date    = static fn (int $time) => $time ? HTMLHelper::_('date', $time, Text::_('DATE_FORMAT_LC4')) : '—';
$listing = Route::link('site', 'index.php?option=com_kunena&view=announcement&layout=listing');
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero<?php echo $shown ? ' is-problem' : '' ?>">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-bullhorn" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title"><?php echo $items ? Text::plural('COM_KUNENA_TOOLS_ANNOUNCEMENTS_N_FOUND', \count($items), (int) $this->tool['total']) : Text::_('COM_KUNENA_TOOLS_ANNOUNCEMENTS_NONE') ?></h2>
                <p class="kd-hero__meta">
                    <?php echo Text::sprintf('COM_KUNENA_TOOLS_ANNOUNCEMENTS_INTRO', KunenaAnnouncementsReview::OLD_DAYS) ?>
                    <a href="<?php echo $this->escape($listing) ?>" target="_blank" rel="noopener"><?php echo Text::_('COM_KUNENA_TOOLS_ANNOUNCEMENTS_OPEN') ?> ›</a>
                </p>
            </div>
        </div>
    </section>

    <?php if ($items) : ?>
        <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post">
            <input type="hidden" name="task" value="tools.unpublishAnnouncements">
            <?php echo HTMLHelper::_('form.token') ?>

            <div class="kd-scroll">
                <table class="table">
                    <thead>
                        <tr>
                            <td class="w-1"></td>
                            <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_ANNOUNCEMENTS_TITLE') ?></th>
                            <th scope="col" class="w-20"><?php echo Text::_('COM_KUNENA_TOOLS_BANS_PROBLEMS') ?></th>
                            <th scope="col" class="w-15"><?php echo Text::_('COM_KUNENA_TOOLS_ANNOUNCEMENTS_CREATED') ?></th>
                            <th scope="col" class="w-20"><?php echo Text::_('COM_KUNENA_TOOLS_ANNOUNCEMENTS_SHOWN') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item) :
                            $id = (int) $item['id']; ?>
                            <tr>
                                <td class="text-center">
                                    <?php if ($item['state'] !== 'unpublished') : ?>
                                        <input type="checkbox" class="form-check-input" name="ids[]" value="<?php echo $id ?>" id="kd-announcement-<?php echo $id ?>">
                                    <?php endif ?>
                                </td>
                                <th scope="row">
                                    <label for="kd-announcement-<?php echo $id ?>"><?php echo $this->escape($item['title']) ?></label>
                                    <br><a class="kd-form__hint" href="<?php echo $this->escape(Route::link('site', 'index.php?option=com_kunena&view=announcement&layout=default&id=' . $id)) ?>" target="_blank" rel="noopener"><?php echo Text::_('COM_KUNENA_TOOLS_TITLES_VIEW') ?> ›</a>
                                </th>
                                <td><span class="kd-pill<?php echo \in_array($item['state'], ['expired', 'old'], true) ? ' kd-pill--warn' : '' ?>"><?php echo Text::_('COM_KUNENA_TOOLS_ANNOUNCEMENTS_' . strtoupper($item['state'])) ?></span></td>
                                <td><?php echo $date($item['created']) ?></td>
                                <td><?php echo $date($item['up']) ?> – <?php echo $date($item['down']) ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>

            <?php if ($this->canAdmin && array_filter($items, static fn ($item) => $item['state'] !== 'unpublished')) : ?>
                <div class="kd-form__actions">
                    <button type="submit" class="kd-btn kd-btn--danger" data-kd-confirm>
                        <span class="fa-solid fa-eye-slash" aria-hidden="true"></span>
                        <?php echo Text::_('COM_KUNENA_TOOLS_ANNOUNCEMENTS_UNPUBLISH') ?>
                    </button>
                    <span class="kd-form__hint"><?php echo Text::_('COM_KUNENA_TOOLS_ANNOUNCEMENTS_UNPUBLISH_HINT') ?></span>
                </div>
            <?php endif ?>
        </form>
    <?php endif ?>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
