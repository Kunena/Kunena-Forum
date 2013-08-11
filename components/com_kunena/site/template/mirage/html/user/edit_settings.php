<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage User
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="kmodule user-edit_settings">
	<div class="kbox-wrapper kbox-full">
		<div class="user-edit_settings-kbox kbox kbox-full kbox-color kbox-border kbox-border_radius kbox-border_radius-vchild kbox-shadow kbox-animate">
			<div class="headerbox-wrapper kbox-full">
				<div class="header">
					<h2 class="header"><a rel="kedit-user-forum-settings"><?php echo JText::_('COM_KUNENA_PROFILE_EDIT_SETTINGS_TITLE') ?></a></h2>
				</div>
			</div>
			<div class="detailsbox-wrapper innerspacer kbox-full">
				<div class="detailsbox kbox-full kbox-border kbox-border_radius kbox-shadow">
					<ul class="kform user-edit-information-list kbox-full" id="kedit-user-forum-settings">
						<?php foreach ($this->settings as $setting) : ?>
						<li class="user-edit-information-row kbox-hover kbox-hover_list-row kbox-full">
							<div class="form-label">
								<div class="innerspacer-left kbox-full">
									<label for="k<?php echo $setting->name ?>"><?php echo $setting->label ?></label>
								</div>
							</div>
							<div class="form-field">
								<div class="innerspacer kbox-full">
									<?php echo $setting->field ?>
								</div>
							</div>
						</li>
						<?php endforeach ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
