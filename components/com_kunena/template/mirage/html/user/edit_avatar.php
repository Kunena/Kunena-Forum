<?php
/**
 * Kunena Component
 * @package Kunena.Template.Mirage
 * @subpackage User
 *
 * @copyright (C) 2008 - 2012 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
?>
<div class="kmodule user-edit_avatar">
	<div class="kbox-wrapper kbox-full">
		<div class="user-edit_avatar-kbox kbox kbox-full kbox-color kbox-border kbox-border_radius kbox-border_radius-vchild kbox-shadow kbox-animate">
			<div class="headerbox-wrapper kbox-full">
				<div class="header">
					<h2 class="header"><a rel="kchange-avatar-image"><?php echo JText::_('COM_KUNENA_PROFILE_EDIT_AVATAR_TITLE') ?></a></h2>
				</div>
			</div>
			<div class="detailsbox-wrapper innerspacer kbox-full">
				<div class="detailsbox kbox-full kbox-border kbox-border_radius kbox-shadow">
					<ul class="kform user-edit-information-list kbox-full" id="kchange-avatar-image">
						<?php if ($this->profile->avatar): ?>
						<li class="user-edit-information-row kbox-hover kbox-hover_list-row kbox-full">
							<div class="form-label">
								<div class="innerspacer-left kbox-full">
									<label for="kavatar-keep"><?php echo JText::_('COM_KUNENA_PROFILE_AVATAR_KEEP') ?></label>
								</div>
							</div>
							<div class="form-field">
								<div class="innerspacer kbox-full">
									<input id="kavatar-keep" type="radio" name="avatar" value="keep" checked="checked" />
								</div>
							</div>
						</li>
						<li class="user-edit-information-row kbox-hover kbox-hover_list-row kbox-full">
							<div class="form-label">
								<div class="innerspacer-left kbox-full">
									<label for="kavatar-delete"><?php echo JText::_('COM_KUNENA_PROFILE_AVATAR_DELETE') ?></label>
								</div>
							</div>
							<div class="form-field">
								<div class="innerspacer kbox-full">
									<input id="kavatar-delete" type="radio" name="avatar" value="delete" />
								</div>
							</div>
						</li>
						<?php endif; ?>
						<?php if ($this->config->allowavatarupload) : ?>
						<li class="user-edit-information-row kbox-hover kbox-hover_list-row kbox-full">
							<div class="form-label">
								<div class="innerspacer-left kbox-full">
									<label for="kavatar-upload"><?php echo JText::_('COM_KUNENA_PROFILE_AVATAR_GALLERY') ?></label>
								</div>
							</div>
							<div class="form-field">
								<div class="innerspacer kbox-full">
									<input type="file" name="avatarfile" class="button" id="kavatar-upload" />
								</div>
							</div>
						</li>
						<?php endif ?>
						<?php if ($this->config->allowavatargallery) : ?>
						<li class="user-edit-information-row kbox-hover kbox-hover_list-row kbox-full">
							<div class="form-label">
								<div class="innerspacer-left kbox-full">
									<label for="avatar_category_select"><?php echo JText::_('COM_KUNENA_PROFILE_AVATAR_GALLERY') ?></label>
									<input id="kunena_url_avatargallery" type="hidden" value="<?php echo $this->me->getUrl(true, 'edit') ?>" />
								</div>
							</div>
							<div class="form-field">
								<div class="innerspacer kbox-full">
									<?php echo $this->galleries ?>
									<div class="clr"></div>
									<ul class="list-unstyled checkbox-outline">
									<?php foreach ($this->galleryimg as $id=>$avatarimg) : ?>
										<li class="checkbox-outline-item">
											<input id="kavatar<?php echo $id ?>" type="radio" name="avatar" value="<?php echo 'gallery/' . ($this->gallery ? $this->gallery.'/':'') . $avatarimg ?>"/>
											<label for="kavatar<?php echo $id ?>"><img src="<?php echo $this->galleryurl .'/'. ($this->gallery ? $this->gallery.'/':'') . $avatarimg ?>" alt="" /></label>
										</li>
										<?php endforeach ?>
									</ul>
								</div>
							</div>
						</li>
						<?php endif ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
