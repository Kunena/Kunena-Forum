<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsisb4
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

if ($this->topicicontype == 'fa'):
?>
<li class="kwho-<?php echo $this->type ?>" alt="<?php echo Text::_($this->rank->rank_title) ?>">
	<i class="fas fa-circle"></i><i class="fas fa-circle" <?php echo $this->stylesecond ?>></i><i class="fas fa-circle" <?php echo $this->stylethird ?>></i><i class="fas fa-circle" <?php echo $this->stylefourth ?>></i><i class="fas fa-circle" <?php echo $this->stylelast ?>></i><?php echo $this->rank->rank_special ? '<i class="fas fa-circle"></i>' : '' ?>
</li>
<?php else: ?>
<li class="kwho-<?php echo $this->type ?>" alt="<?php echo Text::_($this->rank->rank_title) ?>">
	<span class="glyphicon glyphicon-one-fine-dot"></span><span class="glyphicon glyphicon-one-fine-dot" <?php echo $this->stylesecond ?>></span><span class="glyphicon glyphicon-one-fine-dot" <?php echo $this->stylethird ?>></span><span class="glyphicon glyphicon-one-fine-dot" <?php echo $this->stylefourth ?>></span><span class="glyphicon glyphicon-one-fine-dot" <?php echo $this->stylelast ?>></span><?php echo $this->rank->rank_special ? '<span class="glyphicon glyphicon-one-fine-dot"></span>' : '' ?>
</li>
<?php endif; ?>