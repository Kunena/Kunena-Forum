<?php
/**
* @version $Id: myprofile_avatar_upload.php 855 2008-07-16 15:35:10Z fxstein $
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

// Dont allow direct linking
defined('_VALID_MOS') or die('Direct Access to this location is not allowed.');

global $my;
global $fbConfig;
$do = '';
$do = mosGetParam($_REQUEST, 'do', 'init');
$gallery  = mosGetParam($_REQUEST, 'gallery', '');

if ($do == 'init')
{
    if ($fbConfig->allowavatarupload)
    {
?>

<!-- B:My Profile -->
<table width = "100%" border = "0" cellspacing = "0" cellpadding = "0">
    <tr>
        <td class = "fb_myprofile_left" valign = "top">
        <!-- B:My Profile Left -->
            <?php
            if (file_exists(JB_ABSTMPLTPATH . '/plugin/myprofile/myprofile_menu.php'))
            {
                include (JB_ABSTMPLTPATH . '/plugin/myprofile/myprofile_menu.php');
            }
            else
            {
                include (JB_ABSPATH . '/template/default/plugin/myprofile/myprofile_menu.php');
            }
            ?>

        <!-- F:My Profile Left -->
        </td>

        <td class = "fb_myprofile_mid" valign = "top">&nbsp;

        </td>

        <td class = "fb_myprofile_right" valign = "top">
            <!-- B:My Profile Right -->
            <!-- B: My AVATAR -->
<div class="<?php echo $boardclass; ?>_bt_cvr1">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">
            <form action = "<?php echo sefRelToAbs(JB_LIVEURLREL.'&amp;func=myprofile&amp;do=updateavatar'); ?>" method = "post" name = "postform">
    <input type = "hidden" name = "do" value = "updateavatar"/>
    <table class = "fb_blocktable" id = "fb_forumprofile_sub" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
        <thead>
            <tr>
                <th colspan = "2">
                    <div class = "fb_title_cover">
                        <span class = "fb_title"><?php echo _YOUR_AVATAR; ?></span>
                    </div>
                </th>
            </tr>
        </thead>

        <tbody  class = "fb_myprofile_general">
            <tr>
                <td >
                    <?php
                    if ($fbConfig->allowavatar)
                    {
                    ?>

                    <?php
                        echo _YOUR_AVATAR . "</td><td >";

                        if ($fbConfig->avatar_src == "clexuspm")
                        {
                    ?>

                            <img src = "<?php echo MyPMSTools::getAvatarLinkWithID($my->id)?>" alt="" />

                            <br/> <a href = "<?php echo sefRelToAbs('index.php?option=com_mypms&amp;task=upload&amp;Itemid='._CLEXUSPM_ITEMID);?>"><?php echo _SET_NEW_AVATAR; ?></a>

                    <?php
                        }
                        elseif ($fbConfig->avatar_src == "cb")
                        {
                            if ($avatar != "")
                            {
                    ?>

                                <img src = "components/com_comprofiler/images/<?php echo $avatar;?>" alt="" />

                                <br/> <a href = "<?php echo sefRelToAbs('index.php?option=com_comprofiler&amp;Itemid=117&amp;task=userAvatar');?>"><?php echo _SET_NEW_AVATAR; ?></a>

                    <?php
                            }
                            else
                            {
                                echo _NON_SELECTED;
                    ?>

                            <a href = "<?php echo sefRelToAbs('index.php?option=com_comprofiler&amp;Itemid=117&amp;task=userAvatar');?>"><?php echo _SET_NEW_AVATAR; ?></a>

                    <?php
                            }
                        }
                        else
                        {
                            if ($avatar != "")
                            {
                    ?>

                                <img src = "<?php echo FB_LIVEUPLOADEDPATH ;?>/avatars/<?php echo $avatar;?>" alt="" />

                                <br/>

                                <a href = "<?php echo sefRelToAbs(JB_LIVEURLREL.'&amp;func=uploadavatar');?>"> <?php echo _SET_NEW_AVATAR; ?></a>

                                <br/>

                                <input type = "checkbox" value = "1" name = "deleteAvatar"/><i> <?php echo _USER_DELETEAV; ?></i>

                    <?php
                            }
                            else
                            {
                                echo _NON_SELECTED;
                    ?>

                            <a href = "<?php echo sefRelToAbs(JB_LIVEURLREL.'&amp;func=uploadavatar');?>"> <?php echo _SET_NEW_AVATAR; ?></a>

                    <?php
                            }
                    ?>

                        <input type = "hidden" value = "<?php echo $avatar;?>" name = "avatar"/>
                    <?php
                        }
                    ?>
                </td>

                    <?php
                    }
                    else
                    {
                        echo "<td>&nbsp;";
                        echo '<input type="hidden" value="" name="avatar"/></td>';
                    }
                    ?>
            </tr>
<?php if ($avatar != ""){ ?>
            <tr><td colspan = "2" align="center">
            <input type = "submit" class = "button" value = "<?php echo _GEN_DELETE;?>"/></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</form>
</div>
</div>
</div>
</div>
</div>
            <!-- F: My AVATAR -->
            <!-- B: Upload -->
<div class="<?php echo $boardclass; ?>_bt_cvr1">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">

<table class = "fb_blocktable" id ="fb_forumua" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
            <thead>
                <tr>
                    <th>
                        <div class = "fb_title_cover">
                        <span class="fb_title" ><?php echo _UPLOAD_SUBMIT; ?></span>
                        <?php // echo _UPLOAD_DIMENSIONS . ": " . $fbConfig->avatarwidth . "x" . $fbConfig->avatarheight . " - " . $fbConfig->avatarsize . " KB"; ?>
                        </div>
					</th>
                </tr>
            </thead>
            <tbody>
            <tr>
            <td class="fb_uadesc">
<?php

        echo '<form action="' . sefRelToAbs(JB_LIVEURLREL . '&amp;func=uploadavatar&amp;do=validate') . '" method="post" name="adminForm" enctype="multipart/form-data">';
        echo "<input type='hidden' value='do' name='validate'/>";
        echo "<table width='100%' border='0' cellpadding='4' cellspacing='2'>";
        echo "<tr align='center' valign='middle'><td align='center' valign='top'>";
        $uplabel = _UPLOAD_UPLOAD;
        //echo " <input type='hidden' name='MAX_FILE_SIZE' value='".$maxAllowed."' />";
        echo _UPLOAD_SELECT_FILE . " <input type='file' class='button' name='avatar' />";
        echo "<input type='submit' class='button' value='" . _UPLOAD_UPLOAD . "' />";
        echo "</td></tr></table><br />";
        echo "</form>";
?>
            </td>
        </tr>
    </tbody>
</table>
</div>
</div>
</div>
</div>
</div>
            <!-- B: Upload -->
<?PHP
    }


    if ($fbConfig->allowavatargallery)
    {
?>
            <!-- B: Gallery -->
<div class="<?php echo $boardclass; ?>_bt_cvr1">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">
<table class = "fb_blocktable" id ="fb_forumua_gal" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
            <thead>
                <tr>
                    <th>
                        <div class = "fb_title_cover">
                        <span class="fb_title" ><?php echo _UPLOAD_GALLERY; ?></span>
                        </div>
					</th>
                </tr>
            </thead>
            <tbody>
            <tr>
            <td class="fb_uadesc">

        <script type = "text/javascript">
            <!--
            function switch_avatar_category(gallery)
            {
                if (gallery == "")
                    return;

                location.href = "<?php echo JB_LIVEURLREL . '&func=uploadavatar&gallery=';?>"+gallery;
            }
                    //-->
        </script>

<?php
        echo "<p align=\"center\">";
        get_dirs(FB_ABSUPLOADEDPATH . '/avatars/gallery', "categoryid", $gallery);
        echo "<input type=\"button\" value=". _FB_GO ." class=\"button\" onclick=\"switch_avatar_category(this.options[this.selectedIndex].value)\" />\n";
        echo "</p>";
        echo "<br />\n";
        echo '<form action="' . sefRelToAbs(JB_LIVEURLREL . '&amp;func=uploadavatar&amp;do=fromgallery') . '" method="post" name="adminForm">';
        echo "<table width='100%' border='0' cellpadding='4' cellspacing='2'>";
        echo "<tr align='center' valign='middle'>";

        if ($gallery == "default")
            unset($gallery);

        if ($gallery)
        {
            $gallery1 = "/" . str_replace("%20", " ", $gallery);
            $gallery2 = str_replace("%20", " ", $gallery) . "/";
        }

        $avatar_gallery_path = FB_ABSUPLOADEDPATH . '/avatars/gallery' . $gallery1;
        $avatar_images = array ();
        $avatar_images = display_avatar_gallery($avatar_gallery_path);

        for ($i = 0; $i < count($avatar_images); $i++)
        {
            $j = $i + 1;
            echo '<td>';
            //echo '<img src="'.$avatar_gallery_path .'/'. $avatar_images[$i].'">';
            echo '<img src="' . FB_LIVEUPLOADEDPATH . '/avatars/gallery/' . $gallery2 . $avatar_images[$i] . '" alt="" />';
            echo '<input type="radio" name="newAvatar" value="gallery/' . $gallery2 . $avatar_images[$i] . '"/>';
            echo "</td>\n";

            if (function_exists('fmod'))
            {
                if (!fmod(($j), 5)) {
                    echo '</tr><tr align="center" valign="middle">';
                }
            }
            else
            {
                if (!FB_fmodReplace(($j), 5)) {
                    echo '</tr><tr align="center" valign="middle">';
                }
            }
        }

        echo '</tr>';
        echo '<tr><td colspan="5" align="center"><br /><br /><input type="submit" class="button" value="' . _UPLOAD_CHOOSE . '"/><br />';
        echo '</td></tr></table>';
        echo "</form>";
?>
            </td>
        </tr>
    </tbody>
</table>
</div>
</div>
</div>
</div>
</div>
            <!-- F: Gallery -->
<?php
    }
}
else if ($do == 'validate')
{
    //numExtensions= people tend to upload malicious files using mutliple extensions like: virus.txt.vbs; we'll want to have the last extension to validate against..
    $filename = split("\.", $_FILES['avatar']['name']);
    $numExtensions = (count($filename)) - 1;
    $avatarName = $filename[0];
    $avatarExt = $filename[$numExtensions];
    $newFileName = $my->id . "." . $avatarExt;
    $imageType = array( 1 => 'GIF', 2 => 'JPG', 3 => 'PNG', 4 => 'SWF', 5 => 'PSD', 6 => 'BMP', 7 => 'TIFF', 8 => 'TIFF', 9 => 'JPC', 10 => 'JP2', 11 => 'JPX', 12 => 'JB2', 13 => 'SWC', 14 => 'IFF');

    //move it to the proper location
    //if (!move_uploaded_file($_FILES['avatar']['tmp_name'], FB_ABSUPLOADEDPATH . "/avatars/$newFileName"))
    //echo _UPLOAD_ERROR_GENERAL;

    //Filename Medium + proper path
    $fileLocation = FB_ABSUPLOADEDPATH . "/avatars/$newFileName";
    //Filename Small + proper path
    $fileLocation_s = FB_ABSUPLOADEDPATH . "/avatars/s_$newFileName";
    //Filename Large + proper path
    $fileLocation_l = FB_ABSUPLOADEDPATH . "/avatars/l_$newFileName";
    echo '<table width = "100%" border = "0" cellspacing = "0" cellpadding = "0">';
    echo '<tr><td><div><div><div><div><table><tbody><tr><td>';
    //Avatar Size
    $avatarSize = $_FILES['avatar']['size'];

    //check for empty file
    if (empty($_FILES['avatar']['name']))
    {
        mosRedirect(JB_LIVEURL . '&amp;func=uploadavatar', _UPLOAD_ERROR_EMPTY);
    }

    //check for allowed file type (jpeg, gif, png)
    if (!($imgtype = FB_check_image_type($avatarExt)))
    {
        mosRedirect(JB_LIVEURL . '&amp;func=uploadavatar', _UPLOAD_ERROR_TYPE);
    }

    //check file name characteristics
    if (eregi("[^0-9a-zA-Z_]", $avatarExt))
    {
        mosRedirect(JB_LIVEURL . '&amp;func=uploadavatar', _UPLOAD_ERROR_NAME);
    }

    //check filesize
    $maxAvSize = $fbConfig->avatarsize * 1024;

    if ($avatarSize > $maxAvSize)
    {
        mosRedirect(JB_LIVEURL . '&amp;func=uploadavatar', _UPLOAD_ERROR_SIZE . " (" . $fbConfig->avatarsize . " KiloBytes)");
        return;
    }

    $imgInfo = getimagesize($_FILES['avatar']['tmp_name']);
    $imgInfo[2] = $imageType[$imgInfo[2]];
    $srcWidth = $imgInfo[0];
    $srcHeight = $imgInfo[1];
    $src_file = $_FILES['avatar']['tmp_name'];

    switch ($fbConfig->imageprocessor) {
    case 'gd1' :
      if ( !function_exists('imagecreatefromjpeg' )) {
        die(_FB_AVATAR_GDIMAGE_NOT);
      }
      if ( $imgInfo[2] == 'JPG' ) {
        $src_img = imagecreatefromjpeg($src_file);
      } elseif ( $imgInfo[2] == 'PNG' ) {
        $src_img = imagecreatefrompng($src_file);
      } else {
        $src_img = imagecreatefromgif($src_file);
      }

	  // Create Medium Image
	  if(($srcWidth > $fbConfig->avatarwidth) || ($srcHeight > $fbConfig->avatarheight)) {
      $dst_img = imagecreate($fbConfig->avatarwidth, $fbConfig->avatarheight);
      imagecopyresized($dst_img, $src_img, 0, 0, 0, 0, $fbConfig->avatarwidth, (int)$fbConfig->avatarheight, $srcWidth, $srcHeight);
      imagejpeg($dst_img, $fileLocation, $fbConfig->avatarquality);
      imagedestroy($dst_img);
	  } else {
	  		if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $fileLocation))
    		echo _UPLOAD_ERROR_GENERAL;
    		$moved = true;

	  }

      // Create Small Image
      if(($srcWidth > $fbConfig->avatarsmallwidth) || ($srcHeight > $fbConfig->avatarsmallheight)) {
      $dst_img = imagecreate($fbConfig->avatarsmallwidth, $fbConfig->avatarsmallheight);
      imagecopyresized($dst_img, $src_img, 0, 0, 0, 0, $fbConfig->avatarsmallwidth, (int)$fbConfig->avatarsmallheight, $srcWidth, $srcHeight);
      imagejpeg($dst_img, $fileLocation_s, $fbConfig->avatarquality);
      imagedestroy($dst_img);
      } else {
	  		if($moved) {
      			copy($fileLocation,$fileLocation_s);
      		} else {
	  			if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $fileLocation_s))
    			echo _UPLOAD_ERROR_GENERAL;
    			$moved = true;
      		}

	   }

      // Create Large Image
      if(($srcWidth > $fbConfig->avatarlargewidth) || ($srcHeight > $fbConfig->avatarlargeheight)) {
      $dst_img = imagecreate($fbConfig->avatarlargewidth, $fbConfig->avatarlargeheight);
      imagecopyresized($dst_img, $src_img, 0, 0, 0, 0, $fbConfig->avatarlargewidth, (int)$fbConfig->avatarlargeheight, $srcWidth, $srcHeight);
      imagejpeg($dst_img, $fileLocation_l, $fbConfig->avatarquality);
      imagedestroy($dst_img);
      } else {
      		if($moved) {
      			copy($fileLocation,$fileLocation_l);
      		} else {
	  			if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $fileLocation_l))
    			echo _UPLOAD_ERROR_GENERAL;
      		}
	  }

      // Destroy source Image
      imagedestroy($src_img);

    break;


    case 'gd2' :

      if ( !function_exists('imagecreatefromjpeg') ) {
        die(_FB_AVATAR_GDIMAGE_NOT);
      }
      if ( !function_exists('imagecreatetruecolor') ) {
        die(_FB_AVATAR_GD2IMAGE_NOT);
      }
      if ( $imgInfo[2] == 'JPG' ) {
        $src_img = imagecreatefromjpeg($src_file);
      } elseif ($imgInfo[2] == 'PNG'){
        $src_img = imagecreatefrompng($src_file);
      } else {
        $src_img = imagecreatefromgif($src_file);
      }

	  // Create Medium Image
	  if(($srcWidth > $fbConfig->avatarwidth) || ($srcHeight > $fbConfig->avatarheight)) {
     	 $dst_img = imagecreate($fbConfig->avatarwidth, $fbConfig->avatarheight);
      	$dst_img = imagecreatetruecolor($fbConfig->avatarwidth, $fbConfig->avatarheight);
     	 imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $fbConfig->avatarwidth, (int)$fbConfig->avatarheight, $srcWidth, $srcHeight);
    	  imagejpeg($dst_img, $fileLocation, $fbConfig->avatarquality);
    	  imagedestroy($dst_img);
	  } else {
	  		if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $fileLocation))
    		echo _UPLOAD_ERROR_GENERAL;
    		$moved = true;

	  }

      // Create Small Image
       if(($srcWidth > $fbConfig->avatarsmallwidth) || ($srcHeight > $fbConfig->avatarsmallheight)) {
     	 $dst_img = imagecreatetruecolor($fbConfig->avatarsmallwidth, $fbConfig->avatarsmallheight);
     	 imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $fbConfig->avatarsmallwidth, (int)$fbConfig->avatarsmallheight, $srcWidth, $srcHeight);
   		 imagejpeg($dst_img, $fileLocation_s, $fbConfig->avatarquality);;
    	 imagedestroy($dst_img);
        } else {
	  		if($moved) {
      			copy($fileLocation,$fileLocation_s);
      		} else {
	  			if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $fileLocation_s))
    			echo _UPLOAD_ERROR_GENERAL;
    			$moved = true;
      		}

	   }


      // Create Large Image
      if(($srcWidth > $fbConfig->avatarlargewidth) || ($srcHeight > $fbConfig->avatarlargeheight)) {
     	 $dst_img = imagecreatetruecolor($fbConfig->avatarlargewidth, $fbConfig->avatarlargeheight);
     	 imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $fbConfig->avatarlargewidth, (int)$fbConfig->avatarlargeheight, $srcWidth, $srcHeight);
     	 imagejpeg($dst_img, $fileLocation_l, $fbConfig->avatarquality);
     	 imagedestroy($dst_img);
      } else {
      		if($moved) {
      			copy($fileLocation,$fileLocation_l);
      		} else {
	  			if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $fileLocation_l))
    			echo _UPLOAD_ERROR_GENERAL;
      		}
	  }

       // Destroy source Image
      imagedestroy($src_img);
    break;
   }


    @chmod($fileLocation, 0777);
    @chmod($fileLocation_l, 0777);
    @chmod($fileLocation_s, 0777);

    $newFileName = FBTools::fbRemoveXSS($newFileName);
    $database->setQuery("UPDATE #__fb_users SET avatar='{$newFileName}' WHERE userid={$my->id}");
    $database->query() or trigger_dberror("Unable to update avatar.");
    echo " <strong>" . _UPLOAD_UPLOADED . "</strong>...<br/><br/>";
    echo '<a href="' . sefRelToAbs(JB_LIVEURLREL . '&amp;func=myprofile&amp;do=show') . '">' . _GEN_CONTINUE . ".</a>";
    ?>
                <script language = "javascript">
                    setTimeout("location='<?php echo sefRelToAbs(JB_LIVEURLREL . '&func=myprofile&do=show');?>'", 3500);
                </script>
	<?php
}
else if ($do == 'fromgallery')
{
    require_once(JB_ABSSOURCESPATH . 'fb_helpers.php');
    $newAvatar = mosGetParam($_POST, 'newAvatar', '');

    $newAvatar = FBTools::fbRemoveXSS($newAvatar);
    if ($newAvatar == '') {
        mosRedirect(JB_LIVEURL . '&amp;func=uploadavatar', _UPLOAD_ERROR_CHOOSE);
    }

    $database->setQuery("UPDATE #__fb_users SET avatar='{$newAvatar}' WHERE userid={$my->id}");
    $database->query() or trigger_dberror("Unable to update user avatar.");

    echo _USER_PROFILE_UPDATED . "<br /><br />";

    echo _USER_RETURN_A . ' <a href="' . sefRelToAbs(JB_LIVEURLREL . '&amp;func=myprofile&amp;do=show') . '">' . _USER_RETURN_B . '</a><br /><br />';
    fbSetTimeout(JB_LIVEURL . '&func=myprofile&do=show', 3500);
}

function check_filesize($file, $maxSize)
{
    $size = filesize($file);

    if ($size <= $maxSize) {
        return true;
    }

    return false;
}

function display_avatar_gallery($avatar_gallery_path)
{
    $dir = @opendir($avatar_gallery_path);
    $avatar_images = array ();
    $avatar_col_count = 0;

    while ($file = @readdir($dir))
    {
        if ($file != '.' && $file != '..' && is_file($avatar_gallery_path . '/' . $file) && !is_link($avatar_gallery_path . '/' . $file))
        {
            if (preg_match('/(\.gif$|\.png$|\.jpg|\.jpeg)$/is', $file))
            {
                $avatar_images[$avatar_col_count] = $file;
                $avatar_name[$avatar_col_count] = ucfirst(str_replace("_", " ", preg_replace('/^(.*)\..*$/', '\1', $file)));
                $avatar_col_count++;
            }
        }
    }

    @closedir($dir);
    @ksort($avatar_images);
    @reset($avatar_images);
    return $avatar_images;
}

//function FB_fmodReplace($x,$y)
//{ //function provided for older PHP versions which do not have an fmod function yet
//   $i = floor($x/$y);
// r = x - i * y
//   return $x - $i*$y;}
// This function was modified from the one posted to PHP.net by rockinmusicgv
// It is available under the readdir() entry in the PHP online manual
function get_dirs($directory, $select_name, $selected = "")
{
	$filelist = array();
	if ($dir = @opendir($directory))
    {
        while (($file = readdir($dir)) !== false)
        {
            if ($file != ".." && $file != ".")
            {
                if (is_dir($directory . "/" . $file))
                {
                    if (!($file[0] == '.')) {
                        $filelist[] = $file;
                    }
                }
            }
        }

        closedir($dir);
    }

    if ($selected)
        $selected = str_replace("%20", " ", $selected);

    echo "<select name=\"$select_name\" id=\"avatar_category_select\" onchange=\"switch_avatar_category(this.options[this.selectedIndex].value)\">\n";
    echo "<option value=\"default\"";

    if ($selected == "") {
        echo " selected=\"selected\"";
    }

    echo ">"._FB_DEFAULT_GALLERY."</option>\n";

    asort($filelist);

    while (list($key, $val) = each($filelist))
    {
        echo '<option value="'.$val.'"';

        if ($selected == $val) {
            echo " selected=\"selected\"";
        }

        echo ">$val</option>\n";
    }

    echo "</select>\n";
}
?>
 <!-- F:My Profile Right -->
        </td>
    </tr>
</table>
<!-- F:My Profile -->

<!-- Begin: Forum Jump -->
<div class="<?php echo $boardclass; ?>_bt_cvr1">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">
<table class = "fb_blocktable" id = "fb_bottomarea" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
    <thead>
        <tr>
            <th class = "th-right">
                <?php
                //(JJ) FINISH: CAT LIST BOTTOM
                if ($fbConfig->enableforumjump)
                {
                    require_once (JB_ABSSOURCESPATH . 'fb_forumjump.php');
                }
                ?>
            </th>
        </tr>
    </thead>
	<tbody><tr><td></td></tr></tbody>
</table>
</div>
</div>
</div>
</div>
</div>
<!-- Finish: Forum Jump -->
