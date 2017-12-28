<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Widget
 *
 * @copyright   (C) 2008 - 2018 Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

$pathway = $this->breadcrumb->getPathway();
$item = array_shift($pathway);

if ($item) : ?>
<ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
	<li class="active" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
		<?php echo KunenaIcons::home(); ?>
		<a itemprop="item" href="<?php echo $item->link; ?>"><?php echo $this->escape($item->name); ?></a>
	</li>

	<?php foreach($pathway as $item) : ?>
	<li class="divider"><?php echo KunenaIcons::chevronright(); ?></li>
	<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
		<a itemprop="item" href="<?php echo $item->link; ?>"><?php echo $this->escape($item->name); ?></a>
	</li>
	<?php endforeach; ?>

</ol>
<?php endif; ?>
