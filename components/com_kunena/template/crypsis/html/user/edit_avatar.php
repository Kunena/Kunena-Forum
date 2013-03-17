<?php
/**
 * Kunena Component
 * @package Kunena.Template.Strapless
 * @subpackage User
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
$i=0;
?>

<div class="kheader">
  <h3 class="page-header"><span><?php echo JText::_('COM_KUNENA_PROFILE_EDIT_AVATAR_TITLE'); ?></span></h3>
</div>
<div class="kcontainer">
  <div class="kbody">
    <table class="table">
      <?php if ($this->profile->avatar): ?>
      <tr class="krow<?php echo ($i^=1)+1;?>">
        <td class="kcol-first">
          <label for="kavatar-keep"><?php echo JText::_('COM_KUNENA_PROFILE_AVATAR_KEEP');?></label>
        </td>
        <td class="kcol-mid">
          <input id="kavatar-keep" type="radio" name="avatar" value="keep" checked="checked" />
        </td>
      </tr>
      <tr class="krow<?php echo ($i^=1)+1;?>">
        <td class="kcol-first">
          <label for="kavatar-delete"><?php echo JText::_('COM_KUNENA_PROFILE_AVATAR_DELETE');?></label>
        </td>
        <td class="kcol-mid">
          <input id="kavatar-delete" type="radio" name="avatar" value="delete" />
        </td>
      </tr>
      <?php endif; ?>
      <?php if ($this->config->allowavatarupload):?>
      <tr class="krow<?php echo ($i^=1)+1;?>">
        <td class="kcol-first">
          <label for="kavatar-upload"><?php echo JText::_('COM_KUNENA_PROFILE_AVATAR_UPLOAD');?></label>
        </td>
        <td class="kcol-mid">
          <div>
            <input id="kavatar-upload" type="file" class="button" name="avatarfile" />
          </div>
        </td>
      </tr>
      <?php endif; ?>
      <?php if ($this->config->allowavatargallery):?>
      <tr class="krow<?php echo ($i^=1)+1;?>">
        <td class="kcol-first">
          <label><?php echo JText::_('COM_KUNENA_PROFILE_AVATAR_GALLERY');?></label>
          <input id="kunena_url_avatargallery" type="hidden" value="<?php echo $this->me->getUrl(true, 'edit') ?>" />
        </td>
        <td class="kcol-mid">
          <table class="kblocktable" id ="kforumua_gal">
            <tr>
              <td class="kuadesc"><?php echo $this->galleries; ?></td>
            </tr>
            <tr>
              <td class="kuadesc">
                <?php
					$kid = 0;
					foreach ($this->galleryimg as $avatarimg) : ?>
                <span>
                <label for="kavatar<?php echo $kid ?>"><img src="<?php echo $this->galleryurl .'/'. ($this->gallery ? $this->gallery.'/':'') . $avatarimg ?>" alt="" /></label>
                <input id="kavatar<?php echo $kid ?>" type="radio" name="avatar" value="<?php echo 'gallery/' . ($this->gallery ? $this->gallery.'/':'') . $avatarimg ?>"/>
                </span>
                <?php $kid++; endforeach; ?>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <?php endif; ?>
    </table>
  </div>
</div>
