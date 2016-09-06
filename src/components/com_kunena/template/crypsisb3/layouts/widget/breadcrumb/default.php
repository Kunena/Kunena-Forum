<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Widget
 *
 * @copyright   (C) 2008 - 2016 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

$pathway = $this->breadcrumb->getPathway();
$item = array_shift($pathway);

if ($item) : ?>
<div>
	<ul class="breadcrumb">
		<li class="active">
			<span class="divider glyphicon glyphicon-home"></span>
			<a href="<?php echo $item->link; ?>" rel="nofollow"><?php echo $this->escape($item->name); ?></a>
		</li>

		<?php foreach($pathway as $item) : ?>
		<li>
			<a href="<?php echo $item->link; ?>" rel="nofollow"><?php echo $this->escape($item->name); ?></a>
		</li>
		<?php endforeach; ?>

	</ul>
</div>
<?php endif; ?>
