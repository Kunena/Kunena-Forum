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
		<div class="user-edit_avatar-kbox kbox kbox-full kbox-color kbox-border kbox-border_radius kbox-border_radius-vchild kbox-shadow">
			<div class="headerbox-wrapper kbox-full">
				<div class="header">
					<h2 class="header"><a rel="kchange-avatar-image"><?php echo JText::_('COM_KUNENA_PROFILE_EDIT_AVATAR_TITLE') ?></a></h2>
				</div>
			</div>
			<div class="detailsbox-wrapper innerspacer kbox-full">
				<div class="detailsbox kbox-full kbox-border kbox-border_radius kbox-shadow">
					<ul class="kform user-edit-information-list clear" id="kchange-avatar-image">
						<?php if ($this->profile->avatar): ?>
						<li class="user-edit-information-row kbox-hover kbox-hover_list-row clear">
							<div class="form-label">
								<label for="kavatar-keep"><?php echo JText::_('COM_KUNENA_PROFILE_AVATAR_KEEP') ?></label>
							</div>
							<div class="form-field">
								<input id="kavatar-keep" type="radio" name="avatar" value="keep" checked="checked" />
							</div>
						</li>
						<li class="user-edit-information-row kbox-hover kbox-hover_list-row clear">
							<div class="form-label">
								<label for="kavatar-delete"><?php echo JText::_('COM_KUNENA_PROFILE_AVATAR_DELETE') ?></label>
							</div>
							<div class="form-field">
								<input id="kavatar-delete" type="radio" name="avatar" value="delete" />
							</div>
						</li>
						<?php endif; ?>
						<?php if ($this->config->allowavatarupload) : ?>
						<li class="user-edit-information-row kbox-hover kbox-hover_list-row clear">
							<div class="form-label">
								<label for="kavatar-upload"><?php echo JText::_('COM_KUNENA_PROFILE_AVATAR_GALLERY') ?></label>
							</div>
							<div class="form-field">
								<input type="file" name="avatarfile" class="button" id="kavatar-upload" />
							</div>
						</li>
						<?php endif ?>
						<?php if ($this->config->allowavatargallery) : ?>
						<li class="user-edit-information-row kbox-hover kbox-hover_list-row clear">
							<div class="form-label">
								<label for="avatar_category_select"><?php echo JText::_('COM_KUNENA_PROFILE_AVATAR_GALLERY') ?></label>
							</div>
							<div class="form-field">
								<?php echo $this->galleries ?>
								<div class="clr"></div>
								<script type="text/javascript">
									function switch_avatar_category(gallery) {
										if (gallery == "")
											return;
										var url = "<?php echo CKunenaLink::GetMyProfileUrl ( intval($this->user->id), 'edit', false, '&gallery=_GALLERY_' ) ?>";
										var urlreg = new RegExp("_GALLERY_","g");
										location.href=url.replace(urlreg,gallery);
									}
								</script>

								<?php foreach ($this->galleryimg as $id=>$avatarimg) : ?>
								<span>
									<label for="kavatar<?php echo $id ?>"><img src="<?php echo $this->galleryurl .'/'. ($this->gallery ? $this->gallery.'/':'') . $avatarimg ?>" alt="" /></label>
									<input id="kavatar<?php echo $id ?>" type="radio" name="avatar" value="<?php echo 'gallery/' . ($this->gallery ? $this->gallery.'/':'') . $avatarimg ?>"/>
								</span>
								<?php endforeach ?>
							</div>
						</li>
						<?php endif ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
