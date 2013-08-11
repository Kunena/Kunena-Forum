<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Common
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<?php if (($item = array_shift($this->pathway)) !== null) : ?>
	<div class="kmodule common-breadcrumb">
		<div class="kbox-wrapper kbox-full">
			<div class="common-breadcrumb-kbox kbox kbox-full kbox-color kbox-border kbox-border_radius kbox-shadow kbox-animate innerspacer-horizontal">
				<ul class="list-unstyled breadcrumb-path">
					<li><a class="link" href="<?php echo $item->link ?>"><?php echo $item->name ?></a></li>
					<?php foreach ($this->pathway as $item) :
						if(end($this->pathway) != $item) :
					?>
							<li><span class="inline-divider">&#47;</span><a class="link" href="<?php echo $item->link ?>"><?php echo $item->name ?></a></li>
						<?php else: ?>
							<li><span class="inline-divider">&#47;</span><span><?php echo $item->name ?></span></li>
						<?php endif ?>
					<?php endforeach ?>
				</ul>
			</div>
		</div>
	</div>

<?php endif ?>
<?php $this->displayModulePosition ( 'kunena_breadcrumb' ) ?>
