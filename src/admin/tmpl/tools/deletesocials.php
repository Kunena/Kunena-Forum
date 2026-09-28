<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator.Template
 * @subpackage      Deletesocials
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
                    <?php $socials = \is_array($this->listSocialsNetwork ?? null) ? $this->listSocialsNetwork : []; ?>

                    <fieldset>
                        <legend><?php echo Text::_('COM_KUNENA_ADMIN_DELETE_SOCIALS'); ?></legend>
                        <table class="table table-bordered table-striped">
                            <tr>
                                <td colspan="4"><?php echo Text::_('COM_KUNENA_ADMIN_DELETE_SOCIALS_SELECT') ?></td>
                                <td colspan="4">
                                    <?php if ($socials) : ?>
                                        <select name="socials[]" multiple size="<?php echo min(10, count($socials)); ?>">
                                            <?php foreach ($socials as $social) : ?>
                                                <option value="<?php echo $this->escape($social); ?>"><?php echo $this->escape($social); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php else : ?>
                                        <span class="text-muted"><?php echo Text::_('COM_KUNENA_ADMIN_DELETE_SOCIALS_NONE_FOUND'); ?></span>
                                    <?php endif; ?>
                                </td>
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
