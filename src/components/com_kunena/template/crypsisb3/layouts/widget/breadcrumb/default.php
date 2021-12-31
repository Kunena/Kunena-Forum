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

$pathway = $this->breadcrumb->getPathway();
$item    = array_shift($pathway);
$position = 2;

if ($item)
	:
	?>
	<ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">
		<li class="active" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
			<?php echo KunenaIcons::home(); ?>
			<a itemprop="item" href="<?php echo $item->link; ?>"><span itemprop="name"><?php echo $this->escape($item->name); ?></span></a>
			<meta itemprop="position" content="1"/>
		</li>

		<?php foreach ($pathway as $item)
			:
			?>
			<li class="divider"><?php echo KunenaIcons::chevronright(); ?></li>
			<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
				<a itemprop="item" href="<?php echo $item->link; ?>"><span itemprop="name"><?php echo $this->escape($item->name); ?></span></a>
				<meta itemprop="position" content="<?php echo $position++; ?>"/>
			</li>
		<?php endforeach; ?>

	</ol>
<?php endif;
