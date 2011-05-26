<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>

							<div class="kblock keditsettings">
								<h2 class="kheader"><a rel="kedit-user-forum-settings"><?php echo JText::_('COM_KUNENA_PROFILE_EDIT_SETTINGS_TITLE') ?></a></h2>

								<ul class="kform kedit-user-information clearfix" id="kedit-user-forum-settings">
									<?php foreach ($this->settings as $setting) : ?>
									<li class="kedit-user-information-row krow-<?php echo $this->row() ?>">
										<div class="kform-label">
											<label for="k<?php echo $setting->name ?>"><?php echo $setting->label ?></label>
										</div>
										<div class="kform-field">
											<?php echo $setting->field ?>
										</div>
									</li>
									<?php endforeach ?>
								</ul>
							</div>