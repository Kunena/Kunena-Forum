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

$files   = $this->tool['files'];
$folders = $this->tool['folders'];
$limit   = 300;
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero<?php echo $files ? ' is-problem' : '' ?>">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-<?php echo $files ? 'code-compare' : 'circle-check' ?>" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title">
                    <?php if (!$this->tool['dev']) : ?>
                        <?php echo Text::_('COM_KUNENA_TOOLS_TOKENS_NOT_DEV') ?>
                    <?php elseif (!$folders) : ?>
                        <?php echo Text::_('COM_KUNENA_TOOLS_TOKENS_NO_LINK') ?>
                    <?php else : ?>
                        <?php echo $files ? Text::plural('COM_KUNENA_TOOLS_TOKENS_N_FOUND', \count($files)) : Text::_('COM_KUNENA_TOOLS_TOKENS_NONE') ?>
                    <?php endif ?>
                </h2>
                <p class="kd-hero__meta"><?php echo Text::_('COM_KUNENA_TOOLS_TOKENS_INTRO') ?></p>
            </div>
        </div>
        <?php if ($files && $this->canAdmin) : ?>
            <form class="kd-hero__actions" action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post">
                <input type="hidden" name="task" value="tools.restoreTokens">
                <?php echo HTMLHelper::_('form.token') ?>
                <button type="submit" class="kd-btn kd-btn--primary" data-kd-confirm>
                    <span class="fa-solid fa-rotate-left" aria-hidden="true"></span>
                    <?php echo Text::_('COM_KUNENA_TOOLS_TOKENS_RESTORE') ?>
                </button>
            </form>
        <?php endif ?>
    </section>

    <?php if ($folders) : ?>
        <section class="kd-section" aria-labelledby="kd-tokens-folders">
            <h3 id="kd-tokens-folders" class="kd-section__title"><?php echo Text::_('COM_KUNENA_TOOLS_TOKENS_FOLDERS') ?></h3>
            <ul class="kd-tags">
                <?php foreach ($folders as $folder) : ?>
                    <li><code><?php echo $this->escape($folder) ?></code></li>
                <?php endforeach ?>
            </ul>
        </section>
    <?php endif ?>

    <?php if ($files) : ?>
        <div class="kd-scroll">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_PRUNE_FILES_FILE') ?></th>
                        <th scope="col" class="w-10 text-end"><?php echo Text::_('COM_KUNENA_TOOLS_TOKENS_LINES') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (\array_slice($files, 0, $limit, true) as $path => $lines) : ?>
                        <tr>
                            <th scope="row"><code class="kd-check__code"><?php echo $this->escape($path) ?></code></th>
                            <td class="text-end"><?php echo (int) $lines ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
        <?php if (\count($files) > $limit) : ?>
            <p class="kd-form__hint"><?php echo Text::sprintf('COM_KUNENA_TOOLS_N_MORE', \count($files) - $limit) ?></p>
        <?php endif ?>
    <?php endif ?>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
