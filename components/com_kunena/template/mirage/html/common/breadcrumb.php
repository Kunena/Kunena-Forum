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
	<div class="kmodule">
		<div class="box-wrapper">
			<div class="breadcrumb-kbox kbox box-color box-border box-border_radius box-shadow">
				<div class="breadcrumb-wrapper">
					<ul class="list-unstyled breadcrumb-path">
						<li><a class="link" href="<?php echo $item->link ?>"><?php echo $item->name ?></a></li>
						<?php foreach ($this->pathway as $item) : ?>
						<li><span class="inline-divider">&#47;</span><a class="link" href="<?php echo $item->link ?>"><?php echo $item->name ?></a></li>
						<?php endforeach ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div class="spacer"></div>
<?php endif ?>