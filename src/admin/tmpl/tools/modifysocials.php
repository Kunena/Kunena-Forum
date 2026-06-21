<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Modifysocials
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
        <div id="j-main-container" class="col-md-10" role="main">
            <div class="card card-block bg-faded p-2">

                <form action="<?php echo KunenaRoute::_('administrator/index.php?option=com_kunena&view=tools') ?>"
                      method="post" id="adminForm"
                      name="adminForm">
                    <input type="hidden" name="task" value=""/>
                    <?php echo HTMLHelper::_('form.token'); ?>

                    <fieldset>
                        <legend><?php echo Text::_('COM_KUNENA_ADMIN_MODIFY_SOCIALS'); ?></legend>
                        <table class="table table-bordered table-striped">
                            <tr>
                                <td colspan="4"><?php echo Text::_('COM_KUNENA_ADMIN_SETUP_SOCIALS_NAME_OF_SOCIAL_NETWORK_TO_ADD') ?></td>
                                <td colspan="4"><input type="text" name="name" required size="20" /></td>
                            </tr>                            
                            <tr>
                                <td colspan="4"><?php echo Text::_('COM_KUNENA_ADMIN_SETUP_SOCIALS_LANGUAGEKEY_OF_SOCIAL_NETWORK_TO_ADD') ?></td>
                                <td colspan="4"><input type="text" name="languagekey" required size="20" /></td>
                            </tr>
                            <tr>
                                <td colspan="4"><?php echo Text::_('COM_KUNENA_ADMIN_SETUP_SOCIALS_NOURL_OF_SOCIAL_NETWORK_TO_ADD') ?></td>
                                <td colspan="4">
                                    <select name="nourl">
                                    <option value="0"><?php echo Text::_('COM_KUNENA_ADMIN_SETUP_SOCIALS_NOURL_SELECT_ZERO') ?></option>
                                    <option value="1"><?php echo Text::_('COM_KUNENA_ADMIN_SETUP_SOCIALS_NOURL_SELECT_ONE') ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4"><?php echo Text::_('COM_KUNENA_ADMIN_SETUP_SOCIALS_PROFILEURL_OF_SOCIAL_NETWORK_TO_ADD') ?></td>
                                <td colspan="4"><input type="text" name="profileurl" required size="20" /></td>
                            </tr>
                            <tr>
                                <td colspan="4"><?php echo Text::_('COM_KUNENA_ADMIN_SETUP_SOCIALS_FA_OF_SOCIAL_NETWORK_TO_ADD') ?></td>
                                <td colspan="4"><input type="text" name="fa" required size="20" /></td>
                            </tr>                            
                        </table>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
    <div class="pull-right small">
        <?php echo KunenaVersion::getLongVersionHTML(); ?>
    </div>
</div>
