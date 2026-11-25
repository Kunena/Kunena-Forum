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

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$categories = $this->tool['categories'];
$nobody     = array_filter($categories, static fn ($c) => $c['nobody']);
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero<?php echo $nobody ? ' is-problem' : '' ?>">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-table-cells" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title">
                    <?php echo $nobody ? Text::plural('COM_KUNENA_TOOLS_ACCESS_N_NOBODY', \count($nobody)) : Text::plural('COM_KUNENA_TOOLS_ACCESS_N_CATEGORIES', \count($categories)) ?>
                </h2>
                <p class="kd-hero__meta"><?php echo Text::_('COM_KUNENA_TOOLS_ACCESS_INTRO') ?></p>
            </div>
        </div>
        <div class="kd-hero__actions">
            <a class="kd-btn kd-btn--ghost" href="<?php echo Route::_('index.php?option=com_kunena&view=tools&layout=permissions') ?>">
                <span class="fa-solid fa-user-shield" aria-hidden="true"></span>
                <?php echo Text::_('COM_KUNENA_TOOLS_PERMS') ?>
            </a>
        </div>
    </section>

    <div class="kd-scroll">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_QUEUE_CATEGORY') ?></th>
                    <th scope="col" class="w-15"><?php echo Text::_('COM_KUNENA_TOOLS_PERMS_ACCESS') ?></th>
                    <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_ACCESS_READERS') ?></th>
                    <th scope="col" class="w-10 text-end"><?php echo Text::_('COM_KUNENA_TOOLS_ACCESS_MODERATORS') ?></th>
                    <th scope="col" class="w-10"><?php echo Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_STATUS') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category) : ?>
                    <tr>
                        <th scope="row">
                            <span style="padding-inline-start: <?php echo (int) $category['depth'] * 1.25 ?>rem">
                                <a href="<?php echo Route::_('index.php?option=com_kunena&view=category&layout=edit&catid=' . (int) $category['id']) ?>"><?php echo $this->escape($category['name']) ?></a>
                            </span>
                        </th>
                        <td>
                            <code class="kd-check__code"><?php echo $this->escape($category['type']) ?></code>
                            <?php if ($category['level'] !== '') : ?>
                                <br><?php echo $this->escape($category['level']) ?>
                            <?php endif ?>
                        </td>
                        <td>
                            <?php if ($category['everybody']) : ?>
                                <span class="kd-pill kd-pill--ok"><?php echo Text::_('COM_KUNENA_TOOLS_ACCESS_EVERYBODY') ?></span>
                            <?php elseif ($category['nobody']) : ?>
                                <span class="kd-pill kd-pill--warn"><?php echo Text::_('COM_KUNENA_TOOLS_ACCESS_NOBODY') ?></span>
                            <?php else : ?>
                                <ul class="kd-tags">
                                    <?php foreach ($category['readers'] as $reader) : ?>
                                        <li><?php echo $this->escape(Text::_($reader['title'])) ?><?php echo $reader['children'] ? ' +' : '' ?></li>
                                    <?php endforeach ?>
                                </ul>
                            <?php endif ?>
                        </td>
                        <td class="text-end"><?php echo (int) $category['moderators'] ?: '—' ?></td>
                        <td>
                            <?php if (!$category['published']) : ?>
                                <span class="kd-pill kd-pill--warn"><?php echo Text::_('JUNPUBLISHED') ?></span>
                            <?php elseif ($category['locked']) : ?>
                                <span class="kd-pill kd-pill--warn"><?php echo Text::_('COM_KUNENA_TOOLS_PERMS_LOCKED') ?></span>
                            <?php else : ?>
                                <span class="kd-pill kd-pill--ok"><?php echo Text::_('JPUBLISHED') ?></span>
                            <?php endif ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
    <p class="kd-form__hint"><?php echo Text::_('COM_KUNENA_TOOLS_ACCESS_HINT') ?></p>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
