<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage Topic
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="innerbox-wrapper innerspacer-top kbox-full">
	<ul class="buttonbar buttons-topic">
		<?php echo implode(' ', $this->topicButtons->getProperties()) ?>
	</ul>
</div>