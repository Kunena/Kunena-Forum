<?php
/**
 * Kunena Component
 * @package Kunena.Template.Default20
 * @subpackage Topic
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
							<div class="kbuttonbar-post">
								<ul class="kmessage-buttons">
									<?php
									if (empty($this->message_closed)) {
										foreach ($this->messageButtons->getProperties() as $button) {
											echo "<li>$button</li>";
										}
									} else {
										echo "<li>{$this->message_closed}</li>";
									}
									?>
								</ul>
							</div>