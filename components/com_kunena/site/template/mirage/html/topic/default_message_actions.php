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
<div class="innerbox-wrapper innerspacer kbox-full">
	<div class="buttonbar innerblock">
		<ul class="list-unstyled buttons-message">
			<?php
			if (empty($this->message_closed)) {
				echo implode(' ', $this->messageButtons->getProperties());
			} else {
				echo "<li>{$this->message_closed}</li>";
			}
			?>
		</ul>
	</div>
</div>