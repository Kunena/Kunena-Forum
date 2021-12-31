<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsisb3
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

$catid = Factory::getApplication()->input->getInt('catid', 0);
?>
<script>
	jQuery(function ($) {
		$("#jumpto option[value=<?php echo $catid ;?>]").prop("selected", "selected");
	})
</script>
<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena'); ?>" id="jumpto" name="jumpto" method="post"
      target="_self">
	<input type="hidden" name="view" value="category"/>
	<input type="hidden" name="task" value="jump"/>
	<span><?php echo $this->categorylist; ?></span>
</form>
