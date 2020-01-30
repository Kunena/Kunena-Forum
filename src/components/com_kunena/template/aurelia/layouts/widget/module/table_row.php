<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Widget
 *
 * @copyright       Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
**/

namespace Kunena\Forum\Site;

defined('_JEXEC') or die();

use function defined;

$modules = $this->renderPosition();

if (!$modules)
{
	return;
}
?>
<!-- Module position: <?php echo $this->position; ?> -->
<tr>
	<td colspan="<?php echo $this->cols; ?>">
		<?php echo $modules; ?>
	</td>
</tr>
