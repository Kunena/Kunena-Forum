<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      SyncUsers
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

?>

<div id="kunena" class="container-fluid">
    <div class="row">
        <div id="j-main-container" class="col-md-12" role="main">
            <div class="card card-block bg-faded p-2">
                <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>"
                      method="post" id="adminForm"
                      name="adminForm">
                    <input type="hidden" name="task" value="recount"/>
                    <?php echo HTMLHelper::_('form.token'); ?>

                    <fieldset>
                        <legend><?php echo Text::_('COM_KUNENA_A_RECOUNT'); ?></legend>
                        <table class="table table-bordered table-striped">
                            <tr>
                                <td><?php echo Text::_('COM_KUNENA_A_RECOUNT_TOPICS'); ?></td>
                                <td><label>
                                        <input type="checkbox" checked="checked" name="topics" value="1"/>
                                    </label></td>
                                <td><?php echo Text::_('COM_KUNENA_A_RECOUNT_TOPICS_DESC'); ?></td>
                            </tr>
                            <tr>
                                <td><?php echo Text::_('COM_KUNENA_A_RECOUNT_USERTOPICS'); ?></td>
                                <td><label>
                                        <input type="checkbox" checked="checked" name="usertopics" value="1"/>
                                    </label></td>
                                <td><?php echo Text::_('COM_KUNENA_A_RECOUNT_USERTOPICS_DESC'); ?></td>
                            </tr>
                            <tr>
                                <td><?php echo Text::_('COM_KUNENA_A_RECOUNT_CATEGORIES'); ?></td>
                                <td><label>
                                        <input type="checkbox" checked="checked" name="categories" value="1"/>
                                    </label></td>
                                <td><?php echo Text::_('COM_KUNENA_A_RECOUNT_CATEGORIES_DESC'); ?></td>
                            </tr>
                            <tr>
                                <td><?php echo Text::_('COM_KUNENA_A_RECOUNT_USERS'); ?></td>
                                <td><label>
                                        <input type="checkbox" checked="checked" name="users" value="1"/>
                                    </label></td>
                                <td><?php echo Text::_('COM_KUNENA_A_RECOUNT_USERS_DESC'); ?></td>
                            </tr>
                        </table>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
    <div class="float-end small">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </div>
</div>
