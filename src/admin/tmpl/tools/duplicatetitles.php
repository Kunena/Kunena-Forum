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
use Kunena\Forum\Libraries\Tools\KunenaDuplicateTitles;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$groups = $this->tool['groups'];
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero<?php echo $groups ? ' is-problem' : '' ?>">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-heading" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title">
                    <?php echo $groups ? Text::plural('COM_KUNENA_TOOLS_TITLES_N_FOUND', \count($groups)) : Text::_('COM_KUNENA_TOOLS_TITLES_NONE') ?>
                </h2>
                <p class="kd-hero__meta"><?php echo Text::sprintf('COM_KUNENA_TOOLS_TITLES_INTRO', (int) $this->tool['compared'], KunenaDuplicateTitles::MIN_LENGTH) ?></p>
            </div>
        </div>
    </section>

    <?php if ($groups) : ?>
        <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post">
            <input type="hidden" name="task" value="tools.trashDuplicateTitles">
            <?php echo HTMLHelper::_('form.token') ?>

            <?php foreach ($groups as $group) :
                $category = (int) $group[0]['category_id']; ?>
                <section class="kd-panel">
                    <h3 class="kd-panel__title">
                        <?php echo $this->escape((string) $group[0]['subject']) ?>
                        <span class="kd-form__hint">· <?php echo $this->escape($this->tool['categories'][$category] ?? '#' . $category) ?></span>
                    </h3>
                    <div class="kd-scroll">
                        <table class="table">
                            <tbody>
                                <?php foreach ($group as $index => $topic) :
                                    $id = (int) $topic['id']; ?>
                                    <tr>
                                        <td class="w-1 text-center">
                                            <?php // The first topic of a group stays: only the later ones can be chosen. ?>
                                            <?php if ($index > 0) : ?>
                                                <input type="checkbox" class="form-check-input" name="ids[]" value="<?php echo $id ?>" id="kd-title-<?php echo $id ?>">
                                            <?php endif ?>
                                        </td>
                                        <th scope="row">
                                            <label for="kd-title-<?php echo $id ?>"><?php echo $this->escape((string) $topic['subject']) ?></label>
                                            <?php if ($index === 0) : ?>
                                                <span class="kd-pill"><?php echo Text::_('COM_KUNENA_TOOLS_TITLES_FIRST') ?></span>
                                            <?php endif ?>
                                        </th>
                                        <td class="w-15 text-end"><?php echo Text::plural('COM_KUNENA_TOOLS_TITLES_N_POSTS', (int) $topic['posts']) ?></td>
                                        <td class="w-15"><?php echo HTMLHelper::_('date', (int) $topic['first_post_time'], Text::_('DATE_FORMAT_LC4')) ?></td>
                                        <td class="w-15 text-end">
                                            <a href="<?php echo Route::_('index.php?option=com_kunena&view=topic&layout=edit&id=' . $id) ?>"><?php echo Text::_('JACTION_EDIT') ?></a>
                                            · <a href="<?php echo $this->escape(Route::link('site', 'index.php?option=com_kunena&view=topic&catid=' . $category . '&id=' . $id)) ?>" target="_blank" rel="noopener"><?php echo Text::_('COM_KUNENA_TOOLS_TITLES_VIEW') ?></a>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            <?php endforeach ?>

            <?php if ($this->canAdmin) : ?>
                <div class="kd-form__actions">
                    <button type="submit" class="kd-btn kd-btn--danger" data-kd-confirm>
                        <span class="fa-solid fa-trash" aria-hidden="true"></span>
                        <?php echo Text::_('COM_KUNENA_TOOLS_TITLES_TRASH') ?>
                    </button>
                    <span class="kd-form__hint"><?php echo Text::_('COM_KUNENA_TOOLS_TITLES_TRASH_HINT') ?></span>
                </div>
            <?php else : ?>
                <div class="alert alert-info"><?php echo Text::_('COM_KUNENA_TOOLS_ADMIN_ONLY') ?></div>
            <?php endif ?>
        </form>
    <?php endif ?>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
