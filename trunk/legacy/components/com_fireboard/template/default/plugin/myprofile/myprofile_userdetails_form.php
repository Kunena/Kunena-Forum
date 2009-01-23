<?php
/**
* @version $Id: myprofile_userdetails_form.php 807 2008-07-13 04:31:27Z fxstein $
* Fireboard Component
* @package Fireboard
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Based on Joomlaboard Component
* @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author TSMF & Jan de Graaff
**/
defined ('_VALID_MOS') or die('Direct Access to this location is not allowed.');
global $fbConfig;

require_once ($mosConfig_absolute_path . '/includes/HTML_toolbar.php');

// used for spoof hardening
$validate = josSpoofValue();
?>
<script language = "javascript" type = "text/javascript">
    function submitbuttons(pressbutton)
    {
        var form = document.mosUserForm;
        var r = new RegExp("[\<|\>|\"|\'|\%|\;|\(|\)|\&|\+|\-]", "i");

        // do field validation
        if (form.name.value == "")
        {
            alert("<?php echo addslashes( _REGWARN_NAME );?>");
        }
        else if (form.username.value == "")
        {
            alert("<?php echo addslashes( _REGWARN_UNAME );?>");
        }
        else if (r.exec(form.username.value) || form.username.value.length < 3)
        {
            alert("<?php printf( addslashes( _VALID_AZ09 ), addslashes( _PROMPT_UNAME ), 4 );?>");
        }
        else if (form.email.value == "")
        {
            alert("<?php echo addslashes( _REGWARN_MAIL );?>");
        }
        else if ((form.password.value != "") && (form.password.value != form.verifyPass.value))
        {
            alert("<?php echo addslashes( _REGWARN_VPASS2 );?>");
        }
        else if (r.exec(form.password.value))
        {
            alert("<?php printf( addslashes( _VALID_AZ09 ), addslashes( _REGISTER_PASS ), 4 );?>");
        }
        else
        {
            form.submit();
        }
    }
</script>
 <div class="<?php echo $boardclass; ?>_bt_cvr1">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">
<form action = "index.php" method = "post" name = "mosUserForm">
    <div style = "float: right; display:none">
        <?php
        mosToolBar::startTable();
        mosToolBar::spacer();
        mosToolBar::save();
        mosToolBar::cancel();
        mosToolBar::endtable();
        ?>
    </div>

  <table class = "fb_blocktable" id = "fb_forumprofile_sub" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
    <thead>
      <tr>
        <th colspan = "2"> <div class = "fb_title_cover"> <span class = "fb_title"><?php echo _FB_EDIT_TITLE; ?></span> </div></th>
      </tr>
    </thead>
    <tbody  class = "fb_myprofile_general">
      <tr class="fb_sth">
        <th colspan="2" > <center>
            <?php echo _FB_MYPROFILE_PERSONAL_INFO; ?>
          </center></th>
      </tr>
      <tr>
        <td><?php echo _FB_YOUR_NAME; ?> </td>
        <td><input class = "inputbox" type = "text" name = "name" value = "<?php echo $row->name;?>" size = "40"/></td>
      </tr>
      <tr>
        <td><?php echo _FB_EMAIL; ?> </td>
        <td><input class = "inputbox" type = "text" name = "email" value = "<?php echo $row->email;?>" size = "40"/></td>
      </tr>
       <?php if ($fbConfig->usernamechange) { ?>
      <tr>
        <td><?php echo _FB_UNAME; ?> </td>
        <td><input class = "inputbox" type = "text" name = "username" value = "<?php echo $row->username;?>" size = "40"/></td>
      </tr>
      <?php } else { ?>
      <input class = "inputbox" type = "hidden" name = "username" value = "<?php echo $row->username;?>" size = "40"/>
      <?php } ?>
      <tr>
        <td><?php echo _FB_PASS; ?> </td>
        <td><input class = "inputbox" type = "password" name = "password" value = "" size = "40"/></td>
      </tr>
      <tr>
        <td><?php echo _FB_VPASS; ?> </td>
        <td><input class = "inputbox" type = "password" name = "verifyPass" size = "40"/></td>
      </tr>
      <?php
        if ($mosConfig_frontend_userparams == '1' || $mosConfig_frontend_userparams == 1 || $mosConfig_frontend_userparams == NULL)
        {
        ?>
      <tr>
        <td colspan = "2"><?php echo $params->render('params'); ?> </td>
      </tr>
      <?php
        }
        ?>
      <tr>
        <td colspan = "2" align="center"><input type="button" name="save" value="<?php echo _FB_SAVE;?>" onclick="submitbuttons('');"/>
          <input type="reset" value="<?php echo _FB_RESET;?>"/>
        </td>
      </tr>
    </tbody>
  </table>
  <input type = "hidden" name = "id" value = "<?php echo $row->id;?>"/>
  <input type = "hidden" name = "option" value = "com_fireboard"/>
  <input type = "hidden" name = "func" value = "myprofile"/>
  <input type = "hidden" name = "do" value = "usersave"/>
  <input type = "hidden" name = "task" value = ""/>
  <input type = "hidden" name = "<?php echo $validate; ?>" value = "1"/>
</form>
</div>
</div>
</div>
</div>
</div>