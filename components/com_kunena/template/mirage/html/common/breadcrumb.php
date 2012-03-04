<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Common
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

$item = array_shift($this->pathway);
?>
<?php if ($item) : ?>
	<div class="box-module">
		<div class="box-wrapper box-full box-border_radius box-shadow">
			<div class="breadcrumb block">
				<div class="breadcrumb-wrapper">
					<ul class="list-unstyled breadcrumb-path">
						<li><a class="link" href="<?php echo $item->link ?>"><?php echo $item->name ?></a></li>
						<?php foreach ($this->pathway as $item) : ?>
						<li> &#47; <a class="link" href="<?php echo $item->link ?>"><?php echo $item->name ?></a></li>
						<?php endforeach ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div class="spacer"></div>
<?php endif ?>