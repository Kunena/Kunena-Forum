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
use Kunena\Forum\Libraries\Tools\KunenaProfileSpam;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$items    = $this->tool['items'];
$maxPosts = $this->tool['maxPosts'];
$short    = static fn (string $text) => mb_strimwidth(trim((string) preg_replace('/\s+/u', ' ', $text)), 0, 160, '…');
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero<?php echo $items ? ' is-problem' : '' ?>">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-id-card" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title">
                    <?php echo $items ? Text::plural('COM_KUNENA_TOOLS_PROFILESPAM_N_FOUND', \count($items)) : Text::_('COM_KUNENA_TOOLS_PROFILESPAM_NONE') ?>
                </h2>
                <p class="kd-hero__meta"><?php echo Text::_('COM_KUNENA_TOOLS_PROFILESPAM_INTRO') ?></p>
            </div>
        </div>
    </section>

    <form action="<?php echo Route::_('index.php') ?>" method="get" class="kd-panel kd-form">
        <input type="hidden" name="option" value="com_kunena">
        <input type="hidden" name="view" value="tools">
        <input type="hidden" name="layout" value="profilespam">
        <div class="kd-form__field">
            <label for="kd-profilespam-posts"><?php echo Text::_('COM_KUNENA_TOOLS_PROFILESPAM_POSTS') ?></label>
            <select class="form-select" id="kd-profilespam-posts" name="maxposts">
                <?php foreach (KunenaProfileSpam::MAX_POSTS as $value) : ?>
                    <option value="<?php echo $value ?>"<?php echo $value === $maxPosts ? ' selected' : '' ?>><?php echo Text::plural('COM_KUNENA_TOOLS_PROFILESPAM_AT_MOST', $value) ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div>
            <button type="submit" class="kd-btn kd-btn--ghost">
                <span class="fa-solid fa-filter" aria-hidden="true"></span>
                <?php echo Text::_('JSEARCH_FILTER_SUBMIT') ?>
            </button>
        </div>
    </form>

    <?php if ($items) : ?>
        <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post">
            <input type="hidden" name="task" value="tools.clearProfileSpam">
            <input type="hidden" name="maxposts" value="<?php echo $maxPosts ?>">
            <?php echo HTMLHelper::_('form.token') ?>

            <div class="kd-scroll">
                <table class="table">
                    <thead>
                        <tr>
                            <td class="w-1 text-center">
                                <input type="checkbox" class="form-check-input" data-kd-all="kd-profile"
                                       aria-label="<?php echo $this->escape(Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_SELECT_ALL')) ?>">
                            </td>
                            <th scope="col" class="w-20"><?php echo Text::_('COM_KUNENA_TOOLS_PROFILESPAM_USER') ?></th>
                            <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_PROFILESPAM_LINKS') ?></th>
                            <th scope="col" class="w-5 text-end"><?php echo Text::_('COM_KUNENA_TOOLS_TOPICS_POSTS') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $row) :
                            $id = (int) $row['userid']; ?>
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" class="form-check-input kd-profile" name="ids[]" value="<?php echo $id ?>" id="kd-profile-<?php echo $id ?>">
                                </td>
                                <th scope="row">
                                    <label for="kd-profile-<?php echo $id ?>"><?php echo $this->escape((string) $row['username']) ?></label>
                                    <?php if ((int) $row['block']) : ?>
                                        <span class="kd-pill kd-pill--warn"><?php echo Text::_('COM_KUNENA_TOOLS_PROFILESPAM_BLOCKED') ?></span>
                                    <?php endif ?>
                                    <br><span class="kd-form__hint"><?php echo HTMLHelper::_('date', (string) $row['registerDate'], Text::_('DATE_FORMAT_LC4')) ?></span>
                                    <br><a class="kd-form__hint" href="<?php echo Route::_('index.php?option=com_kunena&view=tools&layout=usercleanup&userid=' . $id) ?>"><?php echo Text::_('COM_KUNENA_TOOLS_PROFILESPAM_CLEANUP') ?> ›</a>
                                </th>
                                <td>
                                    <?php foreach ($row['links'] as $field => $count) : ?>
                                        <div>
                                            <span class="kd-pill"><?php echo Text::_('COM_KUNENA_TOOLS_PROFILESPAM_FIELD_' . strtoupper($field)) ?> · <?php echo (int) $count ?></span>
                                            <span class="kd-form__hint"><?php echo $this->escape($short((string) $row[$field])) ?></span>
                                        </div>
                                    <?php endforeach ?>
                                </td>
                                <td class="text-end"><?php echo (int) $row['posts'] ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>

            <?php if ($this->canAdmin) : ?>
                <div class="kd-form__actions">
                    <button type="submit" class="kd-btn kd-btn--danger" data-kd-confirm>
                        <span class="fa-solid fa-eraser" aria-hidden="true"></span>
                        <?php echo Text::_('COM_KUNENA_TOOLS_PROFILESPAM_CLEAR') ?>
                    </button>
                    <span class="kd-form__hint"><?php echo Text::_('COM_KUNENA_TOOLS_PROFILESPAM_CLEAR_HINT') ?></span>
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
