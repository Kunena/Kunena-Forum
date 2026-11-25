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
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$tables = $this->tool['tables'];
$bytes  = static fn ($value) => HTMLHelper::_('number.bytes', (int) $value);
$size   = array_sum(array_column($tables, 'data')) + array_sum(array_column($tables, 'index'));
$free   = array_sum(array_column($tables, 'free'));
?>

<form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post">
    <input type="hidden" name="task" value="tools.databaseTables">
    <?php echo HTMLHelper::_('form.token') ?>

    <div id="kunena" class="kd">
        <section class="kd-hero kd-diag__hero">
            <div class="kd-hero__brand">
                <span class="kd-diag__heroicon fa-solid fa-table" aria-hidden="true"></span>
                <div>
                    <h2 class="kd-hero__title">
                        <?php echo $this->tool['supported']
                            ? Text::sprintf('COM_KUNENA_TOOLS_TABLES_TOTAL', \count($tables), $bytes($size), $bytes($free))
                            : Text::_('COM_KUNENA_TOOLS_TABLES_UNSUPPORTED') ?>
                    </h2>
                    <p class="kd-hero__meta"><?php echo Text::_('COM_KUNENA_TOOLS_TABLES_INTRO') ?></p>
                </div>
            </div>
            <?php if ($tables && $this->canAdmin) : ?>
                <div class="kd-hero__actions">
                    <button type="submit" class="kd-btn kd-btn--primary" name="operation" value="optimize" data-kd-confirm="<?php echo $this->escape(Text::_('COM_KUNENA_TOOLS_TABLES_CONFIRM')) ?>">
                        <span class="fa-solid fa-gauge-high" aria-hidden="true"></span>
                        <?php echo Text::_('COM_KUNENA_TOOLS_TABLES_OPTIMIZE') ?>
                    </button>
                    <button type="submit" class="kd-btn kd-btn--ghost" name="operation" value="analyze">
                        <span class="fa-solid fa-chart-simple" aria-hidden="true"></span>
                        <?php echo Text::_('COM_KUNENA_TOOLS_TABLES_ANALYZE') ?>
                    </button>
                </div>
            <?php endif ?>
        </section>

        <?php if ($tables) : ?>
            <div class="kd-scroll">
                <table class="table">
                    <thead>
                        <tr>
                            <td class="w-1 text-center">
                                <input type="checkbox" class="form-check-input" data-kd-all="kd-table"
                                       aria-label="<?php echo $this->escape(Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_SELECT_ALL')) ?>">
                            </td>
                            <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_TABLES_TABLE') ?></th>
                            <th scope="col" class="text-end"><?php echo Text::_('COM_KUNENA_TOOLS_TABLES_ROWS') ?></th>
                            <th scope="col" class="text-end"><?php echo Text::_('COM_KUNENA_TOOLS_TABLES_DATA') ?></th>
                            <th scope="col" class="text-end d-none d-md-table-cell"><?php echo Text::_('COM_KUNENA_TOOLS_TABLES_INDEX') ?></th>
                            <th scope="col" class="text-end"><?php echo Text::_('COM_KUNENA_TOOLS_TABLES_FREE') ?></th>
                            <th scope="col" class="d-none d-lg-table-cell"><?php echo Text::_('COM_KUNENA_TOOLS_TABLES_ENGINE') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tables as $name => $table) : ?>
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" class="form-check-input kd-table" name="tables[]" value="<?php echo $this->escape($name) ?>"
                                           id="kd-table-<?php echo $this->escape($name) ?>"<?php echo $table['free'] > 0 ? ' checked' : '' ?>>
                                </td>
                                <th scope="row"><label for="kd-table-<?php echo $this->escape($name) ?>"><code class="kd-check__code"><?php echo $this->escape($name) ?></code></label></th>
                                <td class="text-end"><?php echo number_format($table['rows'], 0, '', ' ') ?></td>
                                <td class="text-end"><?php echo $bytes($table['data']) ?></td>
                                <td class="text-end d-none d-md-table-cell"><?php echo $bytes($table['index']) ?></td>
                                <td class="text-end"><?php echo $table['free'] ? $bytes($table['free']) : '—' ?></td>
                                <td class="d-none d-lg-table-cell"><?php echo $this->escape($table['engine']) ?> · <span class="kd-form__hint"><?php echo $this->escape($table['collation']) ?></span></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
            <p class="kd-form__hint"><?php echo Text::_('COM_KUNENA_TOOLS_TABLES_HINT') ?></p>
        <?php endif ?>

        <footer class="kd-footer">
            <?php echo KunenaVersion::getLongVersionHTML(); ?>
        </footer>
    </div>
</form>
