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
use Kunena\Forum\Libraries\Tools\KunenaTranslationCheck;
use Kunena\Forum\Libraries\Version\KunenaVersion;

$summary  = $this->tool['summary'];
$tag      = $this->tool['language'];
$detail   = $this->tool['detail'];
// The languages of the site count: the others are shipped with Kunena but nobody sees them.
$installed = array_filter($summary, static fn ($row) => $row['installed']);
$problems  = array_sum(array_column($installed, 'problems'));
$percent  = static fn (array $row) => $row['texts'] ? (int) floor(100 * ($row['texts'] - $row['missing']) / $row['texts']) : 100;
?>

<div id="kunena" class="kd">
    <section class="kd-hero kd-diag__hero<?php echo $problems ? ' is-problem' : '' ?>">
        <div class="kd-hero__brand">
            <span class="kd-diag__heroicon fa-solid fa-language" aria-hidden="true"></span>
            <div>
                <h2 class="kd-hero__title">
                    <?php echo $problems ? Text::plural('COM_KUNENA_TOOLS_TRANSLATIONS_N_PROBLEMS', $problems) : Text::_('COM_KUNENA_TOOLS_TRANSLATIONS_NO_PROBLEMS') ?>
                </h2>
                <p class="kd-hero__meta"><?php echo Text::_('COM_KUNENA_TOOLS_TRANSLATIONS_INTRO') ?></p>
            </div>
        </div>
    </section>

    <?php if (!$summary) : ?>
        <div class="alert alert-info"><?php echo Text::_('COM_KUNENA_TOOLS_TRANSLATIONS_ONLY_ENGLISH') ?></div>
    <?php else : ?>
        <div class="kd-scroll">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_MAILS_LANGUAGE') ?></th>
                        <th scope="col" class="w-20"><?php echo Text::_('COM_KUNENA_TOOLS_TRANSLATIONS_COMPLETE') ?></th>
                        <th scope="col" class="w-10 text-end"><?php echo Text::_('COM_KUNENA_TOOLS_TRANSLATIONS_MISSING') ?></th>
                        <th scope="col" class="w-10 text-end"><?php echo Text::_('COM_KUNENA_TOOLS_TRANSLATIONS_PROBLEMS') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($summary as $code => $row) : ?>
                        <tr<?php echo $code === $tag ? ' class="table-active"' : '' ?>>
                            <th scope="row">
                                <a href="<?php echo Route::_('index.php?option=com_kunena&view=tools&layout=translations&language=' . $code) ?>"><?php echo $this->escape($row['name']) ?></a>
                                <?php if ($row['name'] !== $code) : ?>
                                    <span class="kd-form__hint"><?php echo $this->escape($code) ?></span>
                                <?php endif ?>
                                <?php if (!$row['installed']) : ?>
                                    <span class="kd-pill"><?php echo Text::_('COM_KUNENA_TOOLS_TRANSLATIONS_NOT_INSTALLED') ?></span>
                                <?php endif ?>
                            </th>
                            <td>
                                <div class="progress" role="progressbar" aria-label="<?php echo $this->escape(Text::_('COM_KUNENA_TOOLS_TRANSLATIONS_COMPLETE')) ?>"
                                     aria-valuenow="<?php echo $percent($row) ?>" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar" style="width: <?php echo $percent($row) ?>%"><?php echo $percent($row) ?>%</div>
                                </div>
                            </td>
                            <td class="text-end"><?php echo (int) $row['missing'] ?></td>
                            <td class="text-end">
                                <?php if ($row['problems']) : ?>
                                    <span class="kd-pill kd-pill--warn"><?php echo (int) $row['problems'] ?></span>
                                    <?php if ($row['errors']) : ?>
                                        <br><span class="kd-form__hint"><?php echo Text::plural('COM_KUNENA_TOOLS_TRANSLATIONS_N_ERRORS', (int) $row['errors']) ?></span>
                                    <?php endif ?>
                                <?php else : ?>
                                    0
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php endif ?>

    <?php if ($detail) : ?>
        <section class="kd-panel" aria-labelledby="kd-tr-problems">
            <h3 id="kd-tr-problems" class="kd-panel__title">
                <?php echo Text::sprintf('COM_KUNENA_TOOLS_TRANSLATIONS_PROBLEMS_OF', $this->escape($summary[$tag]['name'] ?? $tag)) ?>
            </h3>
            <?php if (!$detail['problems']) : ?>
                <p class="kd-form__hint"><?php echo Text::_('COM_KUNENA_TOOLS_TRANSLATIONS_NO_PROBLEMS') ?></p>
            <?php else : ?>
                <div class="kd-scroll">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col"><?php echo Text::_('COM_KUNENA_TOOLS_TRANSLATIONS_TEXT') ?></th>
                                <th scope="col" class="w-30">English</th>
                                <th scope="col" class="w-30"><?php echo $this->escape($summary[$tag]['name'] ?? $tag) ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (\array_slice($detail['problems'], 0, KunenaTranslationCheck::LIST_LIMIT) as $problem) : ?>
                                <tr>
                                    <th scope="row">
                                        <code><?php echo $this->escape($problem['key']) ?></code>
                                        <br><span class="kd-form__hint"><?php echo $this->escape($problem['file']) ?> ·
                                            <?php echo Text::_('COM_KUNENA_TOOLS_TRANSLATIONS_KIND_' . strtoupper($problem['kind'])) ?></span>
                                    </th>
                                    <td><?php echo $this->escape($problem['english']) ?></td>
                                    <td><?php echo $this->escape($problem['text']) ?></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            <?php endif ?>
        </section>

        <?php if ($detail['missing']) : ?>
            <section class="kd-panel" aria-labelledby="kd-tr-missing">
                <h3 id="kd-tr-missing" class="kd-panel__title"><?php echo Text::plural('COM_KUNENA_TOOLS_TRANSLATIONS_N_MISSING', \count($detail['missing'])) ?></h3>
                <?php if (\count($detail['missing']) > KunenaTranslationCheck::LIST_LIMIT) : ?>
                    <p class="kd-form__hint"><?php echo Text::sprintf('COM_KUNENA_TOOLS_TRANSLATIONS_FIRST_N', KunenaTranslationCheck::LIST_LIMIT) ?></p>
                <?php endif ?>
                <div class="kd-scroll">
                    <table class="table">
                        <tbody>
                            <?php foreach (\array_slice($detail['missing'], 0, KunenaTranslationCheck::LIST_LIMIT) as $item) : ?>
                                <tr>
                                    <th scope="row"><code><?php echo $this->escape($item['key']) ?></code>
                                        <br><span class="kd-form__hint"><?php echo $this->escape($item['file']) ?></span></th>
                                    <td class="w-50"><?php echo $this->escape($item['english']) ?></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </section>
        <?php endif ?>
    <?php endif ?>

    <footer class="kd-footer">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </footer>
</div>
