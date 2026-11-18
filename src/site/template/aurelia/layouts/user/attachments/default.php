<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.User
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Icons\KunenaIcons;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

HTMLHelper::_('behavior.core');

$attachments = $this->attachments;
?>
<h3>
    <?php echo $this->headerText; ?>
</h3>

<div class="kfrontend shadow-lg rounded mt-4 border">
    <form action="<?php echo KunenaRoute::_('index.php?option=com_kunena&view=user'); ?>" method="post" id="adminForm"
        name="adminForm">
        <input type="hidden" name="task" value="delfile" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo HTMLHelper::_('form.token'); ?>

        <table class="table table-bordered table-striped table-hover">
            <caption class="visually-hidden"><?php echo $this->headerText; ?></caption>
            <thead>
                <tr>
                    <th scope="col" class="col-md-1 center">
                        #
                    </th>
                    <?php if ($this->me->userid == $this->profile->userid || KunenaUserHelper::getMyself()->isModerator()) :
                    ?>
                        <th scope="col" class="col-md-1 center">
                            <label>
                                <input type="checkbox" name="checkall-toggle" value="cid"
                                    data-bs-toggle="tooltip" title="<?php echo Text::_('COM_KUNENA_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                            </label>
                            <span class="visually-hidden"><?php echo Text::_('COM_KUNENA_CHECK_ALL'); ?></span>
                        </th>
                    <?php endif; ?>
                    <th scope="col" class="col-md-1 center">
                        <?php echo Text::_('COM_KUNENA_FILETYPE'); ?>
                    </th>
                    <th scope="col" class="col-md-2">
                        <?php echo Text::_('COM_KUNENA_FILENAME'); ?>
                    </th>
                    <th scope="col" class="col-md-2">
                        <?php echo Text::_('COM_KUNENA_FILESIZE'); ?>
                    </th>
                    <th scope="col" class="col-md-2">
                        <?php echo Text::_('COM_KUNENA_ATTACHMENT_MANAGER_TOPIC'); ?>
                    </th>
                    <th scope="col" class="col-md-1 center">
                        <?php echo Text::_('COM_KUNENA_PREVIEW'); ?>
                    </th>
                    <?php if ($this->me->userid == $this->profile->userid || KunenaUserHelper::getMyself()->isModerator()) :
                    ?>
                        <th scope="col" class="col-md-1 center">
                            <?php echo Text::_('COM_KUNENA_DELETE'); ?>
                        </th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (!$attachments) :
                ?>
                    <tr>
                        <td colspan="8">
                            <?php echo Text::_('COM_KUNENA_USER_NO_ATTACHMENTS'); ?>
                        </td>
                    </tr>
                    <?php else :
                    $i = $this->pagination->limitstart;

                    foreach ($attachments as $attachment) :
                        $message = $attachment->getMessage();
                        $canDelete = $attachment->isAuthorised('delete');

                        // The file of a message which the reader may not see stays out of the list (its name, its picture
                        // and the topic): without "Protect Attachments" the right of the file alone does not look at the message.
                        $readable = $message->exists() ? $message->isAuthorised('read', $this->me) : $attachment->userid == $this->me->userid;

                        // The file of an anonymous message (the message has no longer the user of the file) would tell who wrote
                        // it: only for the user himself and the moderators.
                        if (
                            $readable && $message->exists() && (int) $message->userid !== (int) $attachment->userid
                            && (int) $attachment->userid !== (int) $this->me->userid && !$this->me->isModerator($message->getCategory())
                        ) {
                            $readable = false;
                        }

                        if ($readable && $attachment->isAuthorised('read', $this->me)) :
                    ?>
                            <tr>
                                <td class="center"><?php echo ++$i; ?></td>
                                <?php
                                if ($canDelete) :
                                ?>
                                    <td class="center">
                                        <?php echo HTMLHelper::_('grid.id', $i, \intval($attachment->id)); ?>
                                    </td>
                                <?php endif; ?>
                                <td class="center">
                                    <?php echo $attachment->isImage() ? KunenaIcons::picture() : KunenaIcons::file(); ?>
                                </td>
                                <td>
                                    <?php echo $attachment->getShortName(10, 5); ?>
                                </td>
                                <td>
                                    <?php echo HTMLHelper::_('kunenaforum.formatfilesize', $attachment->size, 2); ?>
                                </td>
                                <td>
                                    <?php
                                    if ($message->exists()) {
                                        echo $this->getTopicLink($message->getTopic(), $message, null, null, '', null, false, true);
                                    } else {
                                        echo Text::_('COM_KUNENA_USER_ATTACHMENT_MESSAGE_NOT_EXIST');
                                    }
                                    ?>
                                </td>
                                <td class="center">
                                    <?php echo $attachment->getLayout()->render('thumbnail'); ?>
                                </td>
                                <?php if ($canDelete) :
                                ?>
                                    <td class="center">
                                        <button type="button" class="btn center" command="show-modal" commandfor="modaldelete<?php echo $i ?>"
                                            aria-label="<?php echo $this->escape(Text::sprintf('COM_KUNENA_FILES_DELETE_NAMED', $attachment->getFilename(false))); ?>"><?php echo KunenaIcons::delete(); ?></button>

                                        <dialog id="modaldelete<?php echo $i ?>" class="kunena-dialog" aria-labelledby="modaldelete<?php echo $i ?>Label" closedby="any">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title"
                                                        id="modaldelete<?php echo $i ?>Label"><?php echo Text::_('COM_KUNENA_FILES_CONFIRMATION_DELETE_MODAL_LABEL') ?></h4>
                                                    <button type="button" class="btn-close" command="close" commandfor="modaldelete<?php echo $i ?>"
                                                        aria-label="<?php echo Text::_('JLIB_HTML_BEHAVIOR_CLOSE'); ?>"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p><?php echo Text::sprintf('COM_KUNENA_FILES_DELETE_MODAL_DESCRIPTION', $attachment->getFilename(), HTMLHelper::_('kunenaforum.formatfilesize', $attachment->size, 2)); ?></p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline-primary border"
                                                        onclick="return Joomla.listItemTask('cb<?php echo $i; ?>','delfile');"><?php echo Text::_('COM_KUNENA_FILES_CONFIRM_DELETE_MODAL_BUTTON') ?></button>
                                                    <?php // The safe choice has the focus: the file stays. ?>
                                                    <button type="button" command="close" commandfor="modaldelete<?php echo $i ?>" autofocus
                                                        class="btn btn-outline-primary"><?php echo Text::_('COM_KUNENA_FILES_CANCEL_DELETE_MODAL_BUTTON') ?></button>
                                                </div>
                                            </div>
                                        </dialog>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <?php if ($attachments && $this->me->userid == $this->profile->userid || $attachments && KunenaUserHelper::getMyself()->isModerator()) :
        ?>
            <button type="button" class="btn btn-outline-primary border float-end" command="show-modal"
                commandfor="modaldeleteall"><?php echo Text::_('COM_KUNENA_FILES_DELETE'); ?></button>

            <dialog id="modaldeleteall" class="kunena-dialog" aria-labelledby="modaldeleteallLabel" closedby="any">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"
                            id="modaldeleteallLabel"><?php echo Text::_('COM_KUNENA_FILES_CONFIRMATION_DELETE_MODAL_LABEL') ?></h4>
                        <button type="button" class="btn-close" command="close" commandfor="modaldeleteall"
                            aria-label="<?php echo Text::_('JLIB_HTML_BEHAVIOR_CLOSE'); ?>"></button>
                    </div>
                    <div class="modal-body">
                        <p><?php echo Text::_('COM_KUNENA_FILES_DELETE_SELECTED_MODAL_DESCRIPTION'); ?></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary border"
                            id="modaldeteleallsubmit"><?php echo Text::_('COM_KUNENA_FILES_CONFIRM_DELETE_MODAL_BUTTON') ?></button>
                        <button type="button" class="btn btn-outline-primary" command="close" commandfor="modaldeleteall" autofocus><?php echo Text::_('COM_KUNENA_FILES_CANCEL_DELETE_MODAL_BUTTON') ?></button>
                    </div>
                </div>
            </dialog>
        <?php endif; ?>
    </form>
</div>

<div class="float-start">
    <?php echo $this->subLayout('Widget/Pagination/List')
        ->set('pagination', $this->pagination->setDisplayedPages(4))
        ->set('display', true); ?>
</div>
<div class="clearfix"></div>