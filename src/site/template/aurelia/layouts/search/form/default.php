<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Search
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Icons\KunenaIcons;
use Kunena\Forum\Libraries\Route\KunenaRoute;

// FIXME: change into JForm.

// TODO: Add generic form version

$this->ktemplate = KunenaFactory::getTemplate();
$bootstrap = $this->ktemplate->params->get('bootstrap');

HTMLHelper::_('behavior.multiselect');

if ($bootstrap) {
    HTMLHelper::_('dropdown.init');
}

echo $this->subLayout('Widget/Datepicker');

// Load tribute script and css to have autocomplte on user field
$this->addScript('tribute.js');
$this->addStyleSheet('tribute.css');
$this->addScript('assets/js/search.js');
?>

<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=search'); ?>" method="post">
    <input type="hidden" name="task" value="results" />
    <?php if ($this->me->exists()) : ?>
        <input type="hidden" id="kurl_users" name="kurl_users" value="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user&layout=listmention&format=raw') ?>" />
    <?php endif; ?>
    <?php echo HTMLHelper::_('form.token'); ?>
    <div class="accordion" id="main-search">
        <div class="accordion-item">
            <h2 class="accordion-header" id="searchHeader">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#search"><?php echo Text::_('COM_KUNENA_SEARCH_ADVSEARCH'); ?></button>
            </h2>
            <div class="accordion-collapse collapse show" id="search">
                <div class="accordion-body">
                    <div class="row g-3">
                        <fieldset class="col-md-6">
                            <legend class="fs-6 fw-semibold"><?php echo Text::_('COM_KUNENA_SEARCH_SEARCHBY_KEYWORD'); ?></legend>
                            <div class="row g-2">
                                <div class="col-lg-6">
                                    <label for="kkeywordsearch" class="visually-hidden"><?php echo Text::_('COM_KUNENA_SEARCH_KEYWORDS'); ?></label>
                                    <input id="kkeywordsearch" type="text" name="query" class="form-control" value="<?php echo $this->escape($this->state->get('searchwords')); ?>" placeholder="<?php echo Text::_('COM_KUNENA_SEARCH_KEYWORDS'); ?>" />
                                </div>
                                <div class="col-lg-6">
                                    <label for="mode" class="visually-hidden"><?php echo Text::_('COM_KUNENA_SEARCH_SEARCHBY_KEYWORD'); ?></label>
                                    <?php $this->displayModeList('mode'); ?>
                                </div>
                            </div>
                        </fieldset>

                        <?php if (!$this->config->pubProfile && !Factory::getApplication()->getIdentity()->guest || $this->config->pubProfile) : ?>
                            <fieldset class="col-md-6">
                                <legend class="fs-6 fw-semibold"><?php echo Text::_('COM_KUNENA_SEARCH_SEARCHBY_USER'); ?></legend>
                                <div class="row g-2 align-items-center">
                                    <div class="col-sm-6">
                                        <label for="kusersearch" class="visually-hidden"><?php echo Text::_('COM_KUNENA_SEARCH_UNAME'); ?></label>
                                        <input id="kusersearch" type="text" name="searchuser" class="form-control" value="<?php echo $this->escape($this->state->get('query.searchuser')); ?>" placeholder="<?php echo Text::_('COM_KUNENA_SEARCH_UNAME'); ?>" />
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="exactname" id="kexactname" value="1" <?php if ($this->state->get('query.exactname')) {
                                                                                                                                            echo ' checked="checked" ';
                                                                                                                                        } ?> />
                                            <label class="form-check-label" for="kexactname"><?php echo Text::_('COM_KUNENA_SEARCH_EXACT'); ?></label>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="accordion accordion-flush" id="detail-searchoptions">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="searchoptionsHeader">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#searchoptions"><?php echo Text::_('COM_KUNENA_SEARCH_OPTIONS'); ?></button>
                        </h2>
                        <div class="accordion-collapse collapse show" id="searchoptions">
                            <div class="accordion-body">
                                <div class="row g-3">
                                    <fieldset class="col-md-4">
                                        <legend class="fs-6 fw-semibold">
                                            <?php echo Text::_('COM_KUNENA_SEARCH_FIND_POSTS'); ?>
                                        </legend>
                                        <label for="date" class="visually-hidden"><?php echo Text::_('COM_KUNENA_SEARCH_FIND_POSTS'); ?></label>
                                        <?php $this->displayDateList('date'); ?>
                                        <label for="beforeafter" class="visually-hidden"><?php echo Text::_('COM_KUNENA_SEARCH_FIND_POSTS'); ?></label>
                                        <?php $this->displayBeforeAfterList('beforeafter'); ?>
                                    </fieldset>

                                    <fieldset class="col-md-4">
                                        <legend class="fs-6 fw-semibold">
                                            <?php echo Text::_('COM_KUNENA_SEARCH_SORTBY'); ?>
                                        </legend>
                                        <label for="sort" class="visually-hidden"><?php echo Text::_('COM_KUNENA_SEARCH_SORTBY'); ?></label>
                                        <?php $this->displaySortByList('sort'); ?>
                                        <label for="order" class="visually-hidden"><?php echo Text::_('COM_KUNENA_SEARCH_SORTBY'); ?></label>
                                        <?php $this->displayOrderList('order'); ?>
                                    </fieldset>

                                    <fieldset class="col-md-4">
                                        <legend class="fs-6 fw-semibold">
                                            <?php echo Text::_('COM_KUNENA_SEARCH_AT_A_SPECIFIC_DATE'); ?>
                                        </legend>
                                        <div id="searchatdate" class="input-group date" data-date-format="<?php echo $this->config->datePickerFormat; ?>">
                                            <label for="searchatdate-input" class="visually-hidden"><?php echo Text::_('COM_KUNENA_SEARCH_AT_A_SPECIFIC_DATE'); ?></label>
                                            <input type="text" class="form-control" id="searchatdate-input" name="searchatdate" data-date-format="<?php echo $this->config->datePickerFormat; ?>">
                                            <button
                                                class="btn btn-outline-secondary"
                                                type="button"
                                                id="button-searchatdate"
                                                aria-label="<?php echo Text::_('COM_KUNENA_SEARCH_LABEL_SELECT_DATE'); ?>">
                                                <?php echo KunenaIcons::calendar(); ?>
                                            </button>
                                        </div>
                                    </fieldset>
                                </div>

                                <div class="row g-3 mt-1">
                                    <div class="col-md-6">
                                        <fieldset class="mb-3">
                                            <legend class="fs-6 fw-semibold">
                                                <?php echo Text::_('COM_KUNENA_SEARCH_START'); ?>
                                            </legend>
                                            <div class="row g-2">
                                                <div class="col-sm-6">
                                                    <label for="klimitstart" class="visually-hidden"><?php echo Text::_('COM_KUNENA_SEARCH_START'); ?></label>
                                                    <input id="klimitstart" type="text" name="limitstart" class="form-control" value="<?php echo $this->escape($this->state->get('list.start')); ?>" size="5" />
                                                </div>
                                                <div class="col-sm-6">
                                                    <label for="limit" class="visually-hidden"><?php echo Text::_('COM_KUNENA_SEARCH_START'); ?></label>
                                                    <?php $this->displayLimitList('limit'); ?>
                                                </div>
                                            </div>
                                        </fieldset>

                                        <?php if ($this->isModerator) :
                                        ?>
                                            <fieldset>
                                                <legend class="fs-6 fw-semibold">
                                                    <?php echo Text::_('COM_KUNENA_SEARCH_SHOW'); ?>
                                                </legend>
                                                <?php
                                                $showOptions = [
                                                    0 => 'COM_KUNENA_SEARCH_SHOW_NORMAL',
                                                    1 => 'COM_KUNENA_SEARCH_SHOW_UNAPPROVED',
                                                    2 => 'COM_KUNENA_SEARCH_SHOW_TRASHED',
                                                ];

                                                foreach ($showOptions as $showValue => $showText) : ?>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="show" id="kshow<?php echo $showValue; ?>" value="<?php echo $showValue; ?>" <?php if ($this->state->get('query.show') == $showValue) {
                                                                                                                                                                                            echo 'checked="checked"';
                                                                                                                                                                                        } ?> />
                                                        <label class="form-check-label" for="kshow<?php echo $showValue; ?>"><?php echo Text::_($showText); ?></label>
                                                    </div>
                                                <?php endforeach; ?>
                                            </fieldset>
                                        <?php endif; ?>
                                    </div>

                                    <fieldset class="col-md-6">
                                        <legend class="fs-6 fw-semibold">
                                            <?php echo Text::_('COM_KUNENA_SEARCH_SEARCHIN'); ?>
                                        </legend>
                                        <label for="categorylist" class="visually-hidden"><?php echo Text::_('COM_KUNENA_SEARCH_SEARCHIN'); ?></label>
                                        <?php $this->displayCategoryList('categorylist', 'class="form-select mb-2" size="10" multiple'); ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="childforums" id="kchildforums" value="1" <?php if ($this->state->get('query.childforums')) {
                                                                                                                                                echo 'checked="checked"';
                                                                                                                                            } ?> />
                                            <label class="form-check-label" for="kchildforums"><?php echo Text::_('COM_KUNENA_SEARCH_SEARCHIN_CHILDREN'); ?></label>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center p-3">
                        <button type="submit" class="btn btn-outline-primary">
                            <?php echo KunenaIcons::search(); ?><?php echo ' ' . Text::_('COM_KUNENA_SEARCH_SEND') . ' '; ?>
                        </button>
                        <button type="reset" class="btn btn-outline-primary" onclick="window.history.back();">
                            <?php echo KunenaIcons::cancel(); ?><?php echo ' ' . Text::_('COM_KUNENA_CANCEL') . ' '; ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
