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
use Kunena\Forum\Libraries\Tools\KunenaDuplicatePosts;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$groups = $this->tool['groups'];
$copies = array_sum(array_map(static fn ($group) => \count($group) - 1, $groups));
$link   = static fn (array $m) => Route::link('site', 'index.php?option=com_kunena&view=topic&catid=' . (int) $m['catid'] . '&id=' . (int) $m['thread'] . '&mesid=' . (int) $m['id']) . '#' . (int) $m['id'];
$plain  = static fn (string $text) => mb_substr(trim(preg_replace('/\s+/u', ' ', preg_replace('/\[[^\]]{1,40}\]/u', '', $text) ?? $text) ?? $text), 0, 220);
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero<?php echo $groups ? ' is-problem' : '' ?>">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-clone" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title">
                    <?php echo $groups ? Text::plural('COM_KUNENA_TOOLS_DUPLICATES_N_FOUND', $copies, \count($groups)) : Text::_('COM_KUNENA_TOOLS_DUPLICATES_NONE') ?>
                </h2>
                <p class="kd-hero__meta"><?php echo Text::sprintf('COM_KUNENA_TOOLS_DUPLICATES_INTRO', KunenaDuplicatePosts::SCAN_LIMIT, KunenaDuplicatePosts::MIN_LENGTH) ?></p>
            </div>
        </div>
    </section>

    <?php if ($groups) : ?>
        <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post">
            <input type="hidden" name="task" value="tools.trashDuplicates">
            <?php echo HTMLHelper::_('form.token') ?>

            <?php foreach ($groups as $i => $group) : ?>
                <section class="kd-panel" aria-labelledby="kd-dup-<?php echo $i ?>">
                    <h3 id="kd-dup-<?php echo $i ?>" class="kd-panel__title"><?php echo Text::plural('COM_KUNENA_TOOLS_DUPLICATES_N_COPIES', \count($group)) ?></h3>
                    <p class="kd-form__hint"><?php echo $this->escape($plain((string) $group[0]['message'])) ?></p>
                    <table class="table">
                        <tbody>
                            <?php foreach ($group as $position => $row) :
                                $id = (int) $row['id']; ?>
                                <tr>
                                    <td class="w-1 text-center">
                                        <?php if ($position > 0) : ?>
                                            <input type="checkbox" class="form-check-input" name="ids[]" value="<?php echo $id ?>" id="kd-dup-m<?php echo $id ?>" checked>
                                        <?php endif ?>
                                    </td>
                                    <th scope="row">
                                        <label for="kd-dup-m<?php echo $id ?>" class="visually-hidden"><?php echo $this->escape($row['subject']) ?></label>
                                        <a href="<?php echo $this->escape($link($row)) ?>" target="_blank" rel="noopener"><?php echo $this->escape($row['subject']) ?></a>
                                        <?php if ($position === 0) : ?>
                                            <span class="kd-pill kd-pill--ok"><?php echo Text::_('COM_KUNENA_TOOLS_DUPLICATES_KEPT') ?></span>
                                        <?php endif ?>
                                    </th>
                                    <td class="w-20">
                                        <?php if ((int) $row['userid'] > 0) : ?>
                                            <a href="<?php echo Route::_('index.php?option=com_kunena&view=tools&layout=usercleanup&userid=' . (int) $row['userid']) ?>"><?php echo $this->escape($row['name']) ?></a>
                                        <?php else : ?>
                                            <?php echo $this->escape($row['name']) ?>
                                        <?php endif ?>
                                    </td>
                                    <td class="w-15"><?php echo HTMLHelper::_('date', (int) $row['time'], Text::_('DATE_FORMAT_LC5')) ?></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </section>
            <?php endforeach ?>

            <?php if ($this->canAdmin) : ?>
                <div class="kd-form__actions">
                    <button type="submit" class="kd-btn kd-btn--danger" data-kd-confirm>
                        <span class="fa-solid fa-trash-can" aria-hidden="true"></span>
                        <?php echo Text::_('COM_KUNENA_TOOLS_DUPLICATES_TRASH') ?>
                    </button>
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
