<?php
/**
 * Kunena Component
 * @package Kunena.Template.Default20
 * @subpackage User
 *
 * @copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="box-module">
	<div class="block-wrapper box-color box-border box-border_radius">
		<div class="editsettings block">
			<div class="headerbox-wrapper box-full">
				<div class="header">
					<h2 class="header"><a rel="kedit-user-forum-settings"><?php echo JText::_('COM_KUNENA_PROFILE_EDIT_SETTINGS_TITLE') ?></a></h2>
				</div>
			</div>
			<div class="detailsbox-wrapper innerspacer">
				<div class="detailsbox box-full box-border box-border_radius box-shadow">
					<ul class="kform user-edit-information-list clear" id="kedit-user-forum-settings">
						<?php foreach ($this->settings as $setting) : ?>
						<li class="user-edit-information-row box-hover box-hover_list-row clear">
							<div class="form-label">
								<label for="k<?php echo $setting->name ?>"><?php echo $setting->label ?></label>
							</div>
							<div class="form-field">
								<?php echo $setting->field ?>
							</div>
						</li>
						<?php endforeach ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>