<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Route\KunenaRoute;

?>

<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena'); ?>" id="jumpto" name="jumpto" method="post"
      target="_self">
    <input type="hidden" name="view" value="category"/>
    <input type="hidden" name="task" value="jump"/>
    <div class="selector col-12 col-sm-6 col-lg-4 mb-3 d-flex gap-2">
        <?php echo $this->categorylist; ?>
        <button type="submit" class="btn btn-outline-primary"><?php echo Text::_('COM_KUNENA_GO'); ?></button>
    </div>
</form>
