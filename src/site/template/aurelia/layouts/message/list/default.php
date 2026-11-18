<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Message
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Kunena\Forum\Libraries\Icons\KunenaIcons;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\Template\KunenaTemplate;

$view    = Factory::getApplication()->getInput()->getWord('view');
// In the tabs of a profile the list is a part of the page, whose h1 is the name of the user: one h1 per page (the input of
// a tab has no view, the request of the page has).
$heading = Factory::getApplication()->getInput()->getWord('view') === 'user' ? 'h2' : 'h1';

// A page can show this list more than once (in the tabs of a profile): the ids of its elements stay unique on the page.
$ids = [
    'timeselectMobile' => $this->uniqueId('timeselect-mobile'),
    'form'             => $this->uniqueId('ktopicsform'),
    'bottom'           => $this->uniqueId('forumbottom'),
    'time'             => $this->uniqueId('filter-time'),
    'timeselect'       => $this->uniqueId('timeselect'),
    'top'              => $this->uniqueId('forumtop'),
    'task'             => $this->uniqueId('kchecktask'),
    'target'           => $this->uniqueId('kchecktarget'),
    'selMobile'        => $this->uniqueId('sel-mobile'),
    'sel'              => $this->uniqueId('sel'),
];

?>

<div class="row">
    <div class="col-md-12">
        <div class="float-start">
            <<?php echo $heading; ?>>
                <?php echo $this->escape($this->headerText); ?>
                <small class="d-none d-sm-block">
                    (<?php echo Text::sprintf($this->messagemore, $this->formatLargeNumber($this->pagination->total)); ?>
                    )
                </small>

                <?php // ToDo:: <span class="badge bg-success"> <?php echo $this->topics->count->unread; ?/></span>
                ?>
            </<?php echo $heading; ?>>
        </div>

<div class="float-end" id="<?php echo $ids['time']; ?>">
    <h2 class="filter-sel float-end"></h2>
    <form action="<?php echo $this->escape(Uri::getInstance()->toString()); ?>"
        id="<?php echo $ids['timeselect']; ?>" name="timeselect"
        method="post" target="_self" class="form-inline d-none d-sm-block">
        <div class="d-flex gap-2 align-items-center">
            <?php $this->displayTimeFilter($ids['sel']); ?>
            <button type="submit" class="btn btn-sm btn-outline-primary"><?php echo Text::_('COM_KUNENA_FILTER_APPLY'); ?></button>
        </div>
        <?php echo HTMLHelper::_('form.token'); ?>
    </form>
    <form action="<?php echo $this->escape(Uri::getInstance()->toString()); ?>"
        id="<?php echo $ids['timeselectMobile']; ?>" name="timeselect-mobile"
        method="post" target="_self" class="timefilter d-block d-sm-none">
        <div class="d-flex gap-2 align-items-center">
            <?php $this->displayTimeFilter($ids['selMobile']); ?>
            <button type="submit" class="btn btn-sm btn-outline-primary"><?php echo Text::_('COM_KUNENA_FILTER_APPLY'); ?></button>
        </div>
        <?php echo HTMLHelper::_('form.token'); ?>
    </form>
</div>
    </div>
</div>

<div class="float-end">
    <?php echo $this->subLayout('Widget/Search')
        ->set('catid', 'all')
        ->setLayout('topic'); ?>
</div>

<div class="float-start">
    <?php echo $this->subLayout('Widget/Pagination/List')
        ->set('pagination', $this->pagination->setDisplayedPages(4))
        ->set('display', true); ?>
</div>

<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=topics'); ?>" method="post"
    name="ktopicsform" id="<?php echo $ids['form']; ?>">
    <?php echo HTMLHelper::_('form.token'); ?>

    <table class="table<?php echo KunenaTemplate::getInstance()->borderless(); ?>">
        <thead>
            <?php if (empty($this->messages)) : ?>
                <tr>
                    <th scope="row">&nbsp;</th>
                </tr>
            <?php else : ?>
                <tr>
                <th scope="col" class="center d-none d-md-table-cell">
                    <a id="<?php echo $ids['top']; ?>"> </a>
                    <a href="#<?php echo $ids['bottom']; ?>" rel="nofollow" aria-label="<?php echo Text::_('COM_KUNENA_GEN_GOTOBOTTOM'); ?>">
                        <?php echo KunenaIcons::arrowdown(); ?>
                    </a>
                </th>
                <th scope="col" class="d-none d-md-table-cell"><?php echo Text::_('COM_KUNENA_GEN_SUBJECT'); ?></th>
                <th scope="col" class="d-none d-md-table-cell"><?php echo Text::_('COM_KUNENA_GEN_REPLIES'); ?>
                    / <?php echo Text::_('COM_KUNENA_GEN_HITS'); ?></th>
                <th scope="col" class="d-none d-md-table-cell"><?php echo Text::_('COM_KUNENA_GEN_LAST_POST'); ?></th>

                <?php if (!empty($this->actions)) : ?>
                    <th scope="col" class="center"><label>
                            <input class="kcheckall" type="checkbox" name="toggle" value="" aria-label="<?php echo Text::_('COM_KUNENA_CHECK_ALL'); ?>" />
                        </label></th>
                <?php endif; ?>
                </tr>
            <?php endif; ?>
        </thead>


        <tbody class="message-list">
            <?php
            foreach ($this->messages as $i => $message) {
                echo $this->subLayout('Message/Row')
                    ->set('message', $message)
                    ->set('position', $i)
                    ->set('checkbox', !empty($this->actions));
            }
            ?>
        </tbody>

        <tfoot>
            <?php if (!empty($this->messages)) : ?>
                <tr>
                    <th scope="col" class="center d-none d-md-table-cell">
                        <a id="<?php echo $ids['bottom']; ?>"> </a>
                        <a href="#<?php echo $ids['top']; ?>" rel="nofollow" aria-label="<?php echo $this->escape(Text::_('COM_KUNENA_GEN_GOTOTOP')); ?>">
                            <span class="dropdown-divider"></span>
                            <?php echo KunenaIcons::arrowup(); ?>
                        </a>
                    </th>
                    <?php if (!empty($this->actions)) : ?>
                        <th scope="col" colspan="4" class="d-none d-md-table-cell">
                            <div class="form-group">
                                <div class="input-group" role="group">
                                    <?php if (!empty($this->moreUri)) {
                                        echo HTMLHelper::_('kunenaforum.link', $this->moreUri, Text::_('COM_KUNENA_MORE'), null, 'btn btn-outline-primary float-start', 'nofollow');
                                    } ?>
                                    <?php
                                    if (!empty($this->actions)) :
                                    ?>
                                        <?php echo HTMLHelper::_('select.genericlist', $this->actions, 'task', 'class="form-select kchecktask" aria-label="' . Text::_('COM_KUNENA_BULK_ACTIONS_LABEL') . '"', 'value', 'text', 0, $ids['task']); ?>
                                        <?php
                                        if (isset($this->actions['move'])) :
                                            $options = [HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_BULK_CHOOSE_DESTINATION'))];
                                            echo HTMLHelper::_('kunenaforum.categorylist', 'target', 0, $options, [], 'class="form-select fbs" disabled="disabled"', 'value', 'text', 0, $ids['target']);
                                        endif; ?>
                                        <input type="submit" name="kcheckgo" class="btn btn-outline-primary border"
                                            value="<?php echo Text::_('COM_KUNENA_GO') ?>" />
                                    <?php endif; ?>
                                </div>
                            </div>
                        </th>
                    <?php endif; ?>
                </tr>
            <?php endif; ?>
        </tfoot>
    </table>
</form>

<div class="float-start">
    <?php echo $this->subLayout('Widget/Pagination/List')
        ->set('pagination', $this->pagination->setDisplayedPages(4))
        ->set('display', true); ?>
</div>


<div class="clearfix"></div>


<div class="clearfix"></div>
