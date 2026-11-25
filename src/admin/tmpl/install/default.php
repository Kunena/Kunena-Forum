<?php

/**
 * Kunena Component
 *
 * @package       Kunena.Administrator.Template
 * @subpackage    Install
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$status  = $this->status;
$pending = $status['pending'];
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero<?php echo $pending ? ' is-problem' : '' ?>">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-code-branch" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title">
                    <?php echo $pending ? Text::plural('COM_KUNENA_GIT_INSTALL_N_PENDING', \count($pending)) : Text::_('COM_KUNENA_GIT_INSTALL_UP_TO_DATE') ?>
                </h2>
                <p class="kd-hero__meta"><?php echo Text::_('COM_KUNENA_GIT_INSTALL_INTRO') ?></p>
            </div>
        </div>
        <?php if ($pending && $this->canAdmin) : ?>
            <div class="kd-hero__actions">
                <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=install') ?>" method="post">
                    <input type="hidden" name="task" value="install.update">
                    <?php echo HTMLHelper::_('form.token') ?>
                    <button type="submit" class="kd-btn kd-btn--primary" data-kd-confirm="<?php echo $this->escape(Text::_('COM_KUNENA_GIT_INSTALL_CONFIRM')) ?>">
                        <span class="fa-solid fa-database" aria-hidden="true"></span>
                        <?php echo Text::_('COM_KUNENA_GIT_INSTALL_UPDATE') ?>
                    </button>
                </form>
            </div>
        <?php endif ?>
    </section>

    <div class="kd-panel">
        <dl class="kd-kv">
            <dt><?php echo Text::_('COM_KUNENA_GIT_INSTALL_CODE') ?></dt>
            <dd><code><?php echo $this->escape(KunenaForum::version()) ?></code></dd>
            <dt><?php echo Text::_('COM_KUNENA_GIT_INSTALL_SITE') ?></dt>
            <dd><code><?php echo $this->escape($status['current'] !== '' ? $status['current'] : '—') ?></code></dd>
            <dt><?php echo Text::_('COM_KUNENA_GIT_INSTALL_LATEST') ?></dt>
            <dd><code><?php echo $this->escape($status['latest']) ?></code></dd>
            <dt><?php echo Text::_('COM_KUNENA_GIT_INSTALL_FILES') ?></dt>
            <dd><?php echo $this->escape(implode(', ', $status['files'])) ?></dd>
            <dt><?php echo Text::_('COM_KUNENA_GIT_INSTALL_STATEMENTS') ?></dt>
            <dd><?php echo Text::sprintf('COM_KUNENA_GIT_INSTALL_COUNTS', (int) $status['applied'], \count($pending), (int) $status['skipped']) ?></dd>
        </dl>
    </div>

    <?php if ($pending) : ?>
        <section class="kd-panel" aria-labelledby="kd-git-pending">
            <h3 id="kd-git-pending" class="kd-panel__title"><?php echo Text::_('COM_KUNENA_GIT_INSTALL_PENDING') ?></h3>
            <?php foreach ($pending as $statement) : ?>
                <p class="kd-form__hint"><?php echo $this->escape($statement['file']) ?></p>
                <pre class="kd-mail-text"><?php echo $this->escape($statement['query']) ?></pre>
            <?php endforeach ?>
        </section>
    <?php endif ?>

    <p class="kd-form__hint"><?php echo Text::_('COM_KUNENA_GIT_INSTALL_SKIPPED_HINT') ?></p>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
