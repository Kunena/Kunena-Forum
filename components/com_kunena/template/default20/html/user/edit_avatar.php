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
							<div class="kblock keditavatar">
								<h2 class="kheader"><a rel="kchange-avatar-image"><?php echo JText::_('COM_KUNENA_PROFILE_EDIT_AVATAR_TITLE') ?></a></h2>

								<ul class="kform kedit-user-information clearfix" id="kchange-avatar-image">
									<?php if ($this->profile->avatar): ?>
									<li class="kedit-user-information-row krow-<?php echo $this->row() ?>">
										<div class="kform-label">
											<label for="kavatar-keep"><?php echo JText::_('COM_KUNENA_PROFILE_AVATAR_KEEP') ?></label>
										</div>
										<div class="kform-field">
											<input id="kavatar-keep" type="radio" name="avatar" value="keep" checked="checked" />
										</div>
									</li>
									<li class="kedit-user-information-row krow-<?php echo $this->row() ?>">
										<div class="kform-label">
											<label for="kavatar-delete"><?php echo JText::_('COM_KUNENA_PROFILE_AVATAR_DELETE') ?></label>
										</div>
										<div class="kform-field">
											<input id="kavatar-delete" type="radio" name="avatar" value="delete" />
										</div>
									</li>
									<?php endif; ?>
									<?php if ($this->config->allowavatarupload) : ?>
									<li class="kedit-user-information-row krow-<?php echo $this->row() ?>">
										<div class="kform-label">
											<label for="kavatar-upload"><?php echo JText::_('COM_KUNENA_PROFILE_AVATAR_GALLERY') ?></label>
										</div>
										<div class="kform-field">
											<input type="file" name="avatarfile" class="button" id="kavatar-upload" />
										</div>
									</li>
									<?php endif ?>
									<?php if ($this->config->allowavatargallery) : ?>
									<li class="kedit-user-information-row krow-<?php echo $this->row() ?>">
										<div class="kform-label">
											<label for="avatar_category_select"><?php echo JText::_('COM_KUNENA_PROFILE_AVATAR_GALLERY') ?></label>
										</div>
										<div class="kform-field">
											<?php echo $this->galleries ?>
											<div class="clr"></div>
											<script type="text/javascript"><!--
												function switch_avatar_category(gallery){
													if (gallery == "")
														return;
													var url = "<?php echo CKunenaLink::GetMyProfileUrl ( intval($this->user->id), 'edit', false, '&gallery=_GALLERY_' ) ?>";
													var urlreg = new  RegExp("_GALLERY_","g");
													location.href=url.replace(urlreg,gallery);
												}
											// --></script>

											<?php
											foreach ($this->galleryimg as $id=>$avatarimg) : ?>
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