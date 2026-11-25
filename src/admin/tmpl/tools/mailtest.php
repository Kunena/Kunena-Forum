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
use Kunena\Forum\Libraries\Version\KunenaVersion;

$settings = $this->tool['settings'];
$problems = $this->tool['problems'];
$yesNo    = static fn (bool $value) => Text::_($value ? 'JYES' : 'JNO');
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero<?php echo $problems ? ' is-problem' : '' ?>">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-envelope-circle-check" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title">
                    <?php echo $problems ? Text::plural('COM_KUNENA_TOOLS_MAILTEST_N_PROBLEMS', \count($problems)) : Text::_('COM_KUNENA_TOOLS_MAILTEST_OK') ?>
                </h2>
                <p class="kd-hero__meta"><?php echo Text::_('COM_KUNENA_TOOLS_MAILTEST_INTRO') ?></p>
            </div>
        </div>
    </section>

    <?php if ($problems) : ?>
        <ul class="kd-checks mb-4">
            <?php foreach ($problems as $problem) : ?>
                <li class="kd-check is-problem">
                    <span class="kd-check__icon fa-solid fa-triangle-exclamation" aria-hidden="true"></span>
                    <div class="kd-check__body"><span class="kd-check__title"><?php echo Text::_($problem) ?></span></div>
                </li>
            <?php endforeach ?>
        </ul>
    <?php endif ?>

    <div class="kd-columns">
        <section class="kd-panel" aria-labelledby="kd-mail-settings">
            <h3 id="kd-mail-settings" class="kd-panel__title"><?php echo Text::_('COM_KUNENA_TOOLS_MAILTEST_SETTINGS') ?></h3>
            <dl class="kd-kv">
                <dt><?php echo Text::_('COM_KUNENA_TOOLS_MAILTEST_MAILONLINE') ?></dt>
                <dd><?php echo $yesNo($settings['mailonline']) ?></dd>
                <dt><?php echo Text::_('COM_KUNENA_TOOLS_MAILTEST_MAILER') ?></dt>
                <dd><code><?php echo $this->escape($settings['mailer']) ?></code><?php echo $settings['mailer'] === 'smtp' && $settings['smtphost'] !== '' ? ' · ' . $this->escape($settings['smtphost']) : '' ?></dd>
                <dt><?php echo Text::_('COM_KUNENA_TOOLS_MAILTEST_FROM') ?></dt>
                <dd><?php echo $this->escape($settings['fromName']) ?> &lt;<?php echo $this->escape($settings['from']) ?>&gt;</dd>
                <dt><?php echo Text::_('COM_KUNENA_TOOLS_MAILTEST_SENDER') ?></dt>
                <dd><?php echo $this->escape($settings['sender']) ?></dd>
                <dt><?php echo Text::_('COM_KUNENA_TOOLS_MAILTEST_SEND_EMAILS') ?></dt>
                <dd><?php echo $yesNo($settings['sendEmails']) ?></dd>
                <dt><?php echo Text::_('COM_KUNENA_TOOLS_MAILTEST_TEMPLATES') ?></dt>
                <dd><?php echo $yesNo($settings['templates']) ?></dd>
                <dt><?php echo Text::_('COM_KUNENA_TOOLS_MAILTEST_QUEUE') ?></dt>
                <dd>
                    <?php echo (int) $settings['queue'] ?>
                    · <a href="<?php echo Route::_('index.php?option=com_kunena&view=tools&layout=scheduledtasks') ?>"><?php echo Text::_('COM_KUNENA_TOOLS_TASKS') ?></a>
                </dd>
            </dl>
            <div class="kd-form__actions">
                <a class="kd-btn kd-btn--ghost" href="<?php echo Route::_('index.php?option=com_config') ?>"><?php echo Text::_('COM_KUNENA_TOOLS_MAILTEST_JOOMLA_SETTINGS') ?></a>
                <a class="kd-btn kd-btn--ghost" href="<?php echo Route::_('index.php?option=com_kunena&view=config') ?>"><?php echo Text::_('COM_KUNENA_TOOLS_MAILTEST_KUNENA_SETTINGS') ?></a>
            </div>
        </section>

        <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>" method="post" class="kd-panel"
              aria-labelledby="kd-mail-send">
            <input type="hidden" name="task" value="tools.sendTestMail">
            <?php echo HTMLHelper::_('form.token') ?>
            <h3 id="kd-mail-send" class="kd-panel__title"><?php echo Text::_('COM_KUNENA_TOOLS_MAILTEST_SEND') ?></h3>
            <div class="kd-form__field">
                <label for="kd-mail-to"><?php echo Text::_('COM_KUNENA_TOOLS_MAILTEST_TO') ?></label>
                <input type="email" class="form-control" id="kd-mail-to" name="to" value="<?php echo $this->escape($this->tool['me']) ?>" required>
                <span class="kd-form__hint"><?php echo Text::_('COM_KUNENA_TOOLS_MAILTEST_TO_HINT') ?></span>
            </div>
            <?php if ($this->canAdmin) : ?>
                <div class="kd-form__actions">
                    <button type="submit" class="kd-btn kd-btn--primary">
                        <span class="fa-solid fa-paper-plane" aria-hidden="true"></span>
                        <?php echo Text::_('COM_KUNENA_TOOLS_MAILTEST_SEND') ?>
                    </button>
                </div>
            <?php else : ?>
                <div class="alert alert-info"><?php echo Text::_('COM_KUNENA_TOOLS_ADMIN_ONLY') ?></div>
            <?php endif ?>
        </form>
    </div>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
