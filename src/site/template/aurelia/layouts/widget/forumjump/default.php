<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Kunena\Forum\Libraries\Route\KunenaRoute;

?>

<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena'); ?>" id="jumpto" name="jumpto" method="post"
      target="_self">
    <input type="hidden" name="view" value="category"/>
    <input type="hidden" name="task" value="jump"/>
    <div class="selector col-3"><?php echo $this->categorylist; ?></div>
</form>
