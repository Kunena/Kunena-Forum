<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*
* Based on FireBoard Component
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
defined( '_JEXEC' ) or die('Restricted access');

global $kunena_db;

require_once(KUNENA_PATH_LIB .DS. 'kunena.file.class.php');

function generateAvatarGD($gdversion, $src_img, $srcWidth, $srcHeight, $dstWidth, $dstHeight, $quality, $location)
{
	if ($srcWidth>$dstWidth || $srcHeight>$dstHeight) {
		$ratio = $srcWidth/$srcHeight;
		if ($dstWidth/$dstHeight > $ratio) {
			$dstWidth = $dstHeight*$ratio;
		} else {
			$dstHeight = $dstWidth/$ratio;
		}
	} else {
		$dstWidth = $srcWidth;
		$dstHeight = $srcHeight;
	}
	if ((int)$gdversion == 1) {
		$dst_img = imagecreate($dstWidth, $dstHeight);
		imagecopyresized($dst_img, $src_img, 0, 0, 0, 0, (int)$dstWidth, (int)$dstHeight, $srcWidth, $srcHeight);
	} else {
		$dst_img = imagecreatetruecolor($dstWidth, $dstHeight);
		imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, (int)$dstWidth, (int)$dstHeight, $srcWidth, $srcHeight);
	}
	$tmpfile = tempnam(CKunenaPath::tmpdir(), "kn_");
	imagejpeg($dst_img, $tmpfile, $quality);
	CKunenaFile::copy($tmpfile, $location);
	unlink($tmpfile);
	imagedestroy($dst_img);
}

function kn_myprofile_kn_myprofile_check_filesize($file, $maxSize)
{
    $size = filesize($file);

    if ($size <= $maxSize) {
        return true;
    }

    return false;
}

function kn_myprofile_display_avatar_gallery($avatar_gallery_path)
{
    $dir = @opendir($avatar_gallery_path);
    $avatar_images = array ();
    $avatar_col_count = 0;

    $file = @readdir($dir);

    while ($file)
    {
        if ($file != '.' && $file != '..' && is_file($avatar_gallery_path .DS . $file) && !is_link($avatar_gallery_path .DS . $file))
        {
            if (preg_match('/(\.gif$|\.png$|\.jpg|\.jpeg)$/is', $file))
            {
                $avatar_images[$avatar_col_count] = $file;
                $avatar_name[$avatar_col_count] = ucfirst(str_replace("_", " ", preg_replace('/^(.*)\..*$/', '\1', $file)));
                $avatar_col_count++;
            }
        }
        $file = @readdir($dir);
    }

    @closedir($dir);
    @ksort($avatar_images);
    @reset($avatar_images);
    return $avatar_images;
}

// This function was modified from the one posted to PHP.net by rockinmusicgv
// It is available under the readdir() entry in the PHP online manual
function get_dirs($directory, $select_name, $selected = "")
{
	$filelist = array();

	$dir = @opendir($directory);
	if ($dir)
    {
    	$file = readdir($dir);

        while ($file)
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
            $file = readdir($dir);
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

    echo ">"._KUNENA_DEFAULT_GALLERY."</option>\n";

    asort($filelist);

    foreach ($filelist as $key => $val)
    {
        echo '<option value="'.$val.'"';

        if ($selected == $val) {
            echo " selected=\"selected\"";
        }

        echo ">$val</option>\n";
    }

    echo "</select>\n";
}

$kunena_my = &JFactory::getUser();

if ($kunena_my->id != "" && $kunena_my->id != 0)
{

$fbConfig =& CKunenaConfig::getInstance();
$task = JRequest::getCmd('action', 'default');
$gallery  = JRequest::getVar('gallery', '');
$app =& JFactory::getApplication();

switch ($task) {
	case "delete":
		$rowItemid = JRequest::getInt('Itemid');

		$deleteAvatar = JRequest::getInt('deleteAvatar', 0);
		$avatar = JRequest::getVar('avatar', '');

		if ($deleteAvatar == 1)
		{
			$avatar = "";
		}

		$kunena_db->setQuery("UPDATE #__fb_users SET avatar='{$avatar}' WHERE userid='{$kunena_my->id}'");

		if (!$kunena_db->query())
		{
			$app->enqueueMessage(_USER_PROFILE_NOT_A._USER_PROFILE_NOT_B._USER_PROFILE_NOT_C, 'notice');
		}
		else
		{
			$app->enqueueMessage(_USER_PROFILE_UPDATED);
		}

		$app->redirect(JRoute::_(KUNENA_LIVEURLREL . '&func=myprofile&do=avatar'));
		break;

	case 'upload':
		//numExtensions= people tend to upload malicious files using mutliple extensions like: virus.txt.vbs; we'll want to have the last extension to validate against..
		$filename = split('\.', $_FILES['avatar']['name']);
		$numExtensions = (count($filename)) - 1;
		$avatarName = $filename[0];
		$avatarExt = $filename[$numExtensions];
		$newFileName = $kunena_my->id . "." . $avatarExt;
		$imageType = array( 1 => 'GIF', 2 => 'JPG', 3 => 'PNG', 4 => 'SWF', 5 => 'PSD', 6 => 'BMP', 7 => 'TIFF', 8 => 'TIFF', 9 => 'JPC', 10 => 'JP2', 11 => 'JPX', 12 => 'JB2', 13 => 'SWC', 14 => 'IFF');

		//move it to the proper location
		//if (!move_uploaded_file($_FILES['avatar']['tmp_name'], KUNENA_PATH_UPLOADED .DS. "avatars/$newFileName"))
		//echo _UPLOAD_ERROR_GENERAL;

		//Filename Medium + proper path
		$fileLocation = KUNENA_PATH_UPLOADED .DS. "avatars/$newFileName";
		//Filename Small + proper path
		$fileLocation_s = KUNENA_PATH_UPLOADED .DS. "avatars/s_$newFileName";
		//Filename Large + proper path
		$fileLocation_l = KUNENA_PATH_UPLOADED .DS. "avatars/l_$newFileName";
		echo '<table width = "100%" border = "0" cellspacing = "0" cellpadding = "0">';
		echo '<tr><td><div><div><div><div><table><tbody><tr><td>';
		//Avatar Size
		$avatarSize = $_FILES['avatar']['size'];
		$src_file = $_FILES['avatar']['tmp_name'];

		//check for empty file
		if (!is_uploaded_file($src_file) || empty($_FILES['avatar']['name']))
		{
			$app->enqueueMessage(_UPLOAD_ERROR_EMPTY, 'notice');
			$app->redirect(JRoute::_(KUNENA_LIVEURLREL . '&func=myprofile&do=avatar'));
		}

		//check for allowed file type (jpeg, gif, png)
		if (!($imgtype = KUNENA_check_image_type($avatarExt)))
		{
			$app->enqueueMessage(_UPLOAD_ERROR_TYPE, 'notice');
			$app->redirect(JRoute::_(KUNENA_LIVEURLREL . '&func=myprofile&do=avatar'));
		}

		//check file name characteristics
		if (eregi("[^0-9a-zA-Z_]", $avatarExt))
		{
			$app->enqueueMessage(_UPLOAD_ERROR_NAME, 'notice');
			$app->redirect(JRoute::_(KUNENA_LIVEURLREL . '&func=myprofile&do=avatar'));
		}

		//check filesize
		$maxAvSize = $fbConfig->avatarsize * 1024;

		if ($avatarSize > $maxAvSize)
		{
			$app->enqueueMessage(_UPLOAD_ERROR_SIZE . " (" . $fbConfig->avatarsize . " KiloBytes)", 'notice');
			$app->redirect(JRoute::_(KUNENA_LIVEURLREL . '&func=myprofile&do=avatar'));
		}

		$imgInfo = false;
		if (function_exists('getimagesize')) {
			$imgInfo = @getimagesize($_FILES['avatar']['tmp_name']);
			if ($imgInfo !== false) {
				$imgInfo[2] = $imageType[$imgInfo[2]];
				$srcWidth = $imgInfo[0];
				$srcHeight = $imgInfo[1];
			}
		} else {
			$fbConfig->imageprocessor = 'none';
		}

		//$gdversion = ereg_replace('[[:alpha:][:space:]()]+', '', $GDArray['GD Version']); // just FYI for detection from gd_info()

		switch ($fbConfig->imageprocessor) {
		case 'gd1' :
			if ( !function_exists('imagecreatefromjpeg' )) {
				$app->enqueueMessage(_KUNENA_AVATAR_GDIMAGE_NOT, 'error');
				$app->redirect(JRoute::_(KUNENA_LIVEURLREL . '&func=myprofile&do=avatar'));
			}
			if ( $imgInfo[2] == 'JPG' ) {
				$src_img = imagecreatefromjpeg($src_file);
			} elseif ( $imgInfo[2] == 'PNG' ) {
				$src_img = imagecreatefrompng($src_file);
			} else {
				$src_img = imagecreatefromgif($src_file);
			}

			// Create Large Image
			generateAvatarGD(1, $src_img, $srcWidth, $srcHeight, $fbConfig->avatarlargewidth,
			$fbConfig->avatarlargeheight, $fbConfig->avatarquality, $fileLocation_l);
			// Create Medium Image
			generateAvatarGD(1, $src_img, $srcWidth, $srcHeight, $fbConfig->avatarwidth,
			$fbConfig->avatarheight, $fbConfig->avatarquality, $fileLocation);
			// Create Small Image
			generateAvatarGD(1, $src_img, $srcWidth, $srcHeight, $fbConfig->avatarsmallwidth,
			$fbConfig->avatarsmallheight, $fbConfig->avatarquality, $fileLocation_s);
			// Destroy source Image
			imagedestroy($src_img);
			break;

		case 'gd2' :

			if ( !function_exists('imagecreatefromjpeg') ) {
				$app->enqueueMessage(_KUNENA_AVATAR_GDIMAGE_NOT, 'error');
				$app->redirect(JRoute::_(KUNENA_LIVEURLREL . '&func=myprofile&do=avatar'));
			}
			if ( !function_exists('imagecreatetruecolor') ) {
				$app->enqueueMessage(_KUNENA_AVATAR_GD2IMAGE_NOT, 'error');
				$app->redirect(JRoute::_(KUNENA_LIVEURLREL . '&func=myprofile&do=avatar'));
			}
			if ( $imgInfo[2] == 'JPG' ) {
				$src_img = imagecreatefromjpeg($src_file);
			} elseif ($imgInfo[2] == 'PNG'){
				$src_img = imagecreatefrompng($src_file);
			} else {
				$src_img = imagecreatefromgif($src_file);
			}

			// Create Large Image
			generateAvatarGD(2, $src_img, $srcWidth, $srcHeight, $fbConfig->avatarlargewidth,
			$fbConfig->avatarlargeheight, $fbConfig->avatarquality, $fileLocation_l);
			// Create Medium Image
			generateAvatarGD(2, $src_img, $srcWidth, $srcHeight, $fbConfig->avatarwidth,
			$fbConfig->avatarheight, $fbConfig->avatarquality, $fileLocation);
			// Create Small Image
			generateAvatarGD(2, $src_img, $srcWidth, $srcHeight, $fbConfig->avatarsmallwidth,
			$fbConfig->avatarsmallheight, $fbConfig->avatarquality, $fileLocation_s);
			// Destroy source Image
			imagedestroy($src_img);
			break;

		default:
			if (isset($srcWidth) && ($srcWidth > $fbConfig->avatarlargewidth || $srcHeight > $fbConfig->avatarlargeheight)) {
				$app->enqueueMessage(_UPLOAD_ERROR_SIZE . " (" . $fbConfig->avatarlargewidth . " x ". $fbConfig->avatarlargeheight .")", 'notice');
				$app->redirect(JRoute::_(KUNENA_LIVEURLREL . '&func=myprofile&do=avatar'));
			}
			// Make sure that we do not use wrong avatar image
			if (file_exists($fileLocation_s)) CKunenaFile::delete($fileLocation_s);
			if (file_exists($fileLocation_l)) CKunenaFile::delete($fileLocation_l);
			CKunenaFile::copy($src_file, $fileLocation);
			break;
		}

		// delete original file
		unlink($src_file);

		$kunena_db->setQuery("UPDATE #__fb_users SET avatar='{$newFileName}' WHERE userid={$kunena_my->id}");
		$kunena_db->query() or trigger_dberror("Unable to update avatar.");

		$app->redirect(JRoute::_(KUNENA_LIVEURLREL . '&func=myprofile'),_UPLOAD_UPLOADED);

	case 'gallery':
		require_once(KUNENA_PATH_LIB .DS. 'kunena.helpers.php');
		$newAvatar = JRequest::getVar('newAvatar', '');

		$newAvatar = CKunenaTools::fbRemoveXSS($newAvatar);
		if ($newAvatar == '') {
			$app->enqueueMessage(_UPLOAD_ERROR_CHOOSE, 'notice');
			$app->redirect(JRoute::_(KUNENA_LIVEURLREL . '&amp;func=myprofile&do=avatar'));
		}

		$kunena_db->setQuery("UPDATE #__fb_users SET avatar='{$newAvatar}' WHERE userid={$kunena_my->id}");
		$kunena_db->query() or trigger_dberror("Unable to update user avatar.");

		$app->redirect(JRoute::_(KUNENA_LIVEURLREL . '&func=myprofile'),_UPLOAD_UPLOADED);
		break;
}


if ($task == 'default')
{
    if ($fbConfig->allowavatar)
    {
?>
        <td class = "fb_myprofile_right" valign = "top">
            <!-- B:My Profile Right -->
            <!-- B: My AVATAR -->
            <form action = "<?php echo JRoute::_(KUNENA_LIVEURLREL.'&func=myprofile&do=avatar&action=delete'); ?>" method = "post" name = "postform">
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
                        echo _YOUR_AVATAR . "</td><td >";

                        if ($fbConfig->avatar_src == "clexuspm")
                        {
                    ?>

                            <img src = "<?php echo MyPMSTools::getAvatarLinkWithID($kunena_my->id)?>" alt="" />

                            <br /> <a href = "<?php echo JRoute::_('index.php?option=com_mypms&amp;task=upload&amp;Itemid='._CLEXUSPM_ITEMID);?>"><?php echo _SET_NEW_AVATAR; ?></a>

                    <?php
                        }
                        elseif ($fbConfig->avatar_src == "cb")
                        {
                            $kunena_db->setQuery("SELECT avatar FROM #__comprofiler WHERE user_id='{$kunena_my->id}'");
                            $avatar = $kunena_db->loadResult();
                            check_dberror("Unable to load CB Avatar.");
                            if ($avatar != "")
                            {
                    ?>

                                <img src = "components/com_comprofiler/images/<?php echo $avatar;?>" alt="" />

                                <br /> <a href = "<?php echo JRoute::_('index.php?option=com_comprofiler&amp;Itemid=117&amp;task=userAvatar');?>"><?php echo _SET_NEW_AVATAR; ?></a>

                    <?php
                            }
                            else
                            {
                                echo _NON_SELECTED;
                    ?>

                            <br /> <a href = "<?php echo JRoute::_('index.php?option=com_comprofiler&amp;Itemid=117&amp;task=userAvatar');?>"><?php echo _SET_NEW_AVATAR; ?></a>

                    <?php
                            }
                        }
                        else
                        {
                            $kunena_db->setQuery("SELECT avatar FROM #__fb_users WHERE userid='{$kunena_my->id}'");
                            $avatar = $kunena_db->loadResult();
                            check_dberror("Unable to load Kunena Avatar.");
                            if ($avatar != "")
                            {
								if(!file_exists(KUNENA_PATH_UPLOADED .DS. 'avatars/l_' . $avatar)) {
									$msg_avatar = '<img src="' . KUNENA_LIVEUPLOADEDPATH . '/avatars/' . $avatar . '" alt="" style="max-width: '.$fbConfig->avatarlargewidth.'px; max-height: '.$fbConfig->avatarlargeheight.'px;" />';
								} else {
									$msg_avatar = '<img src="' . KUNENA_LIVEUPLOADEDPATH . '/avatars/l_' . $avatar . '" alt="" />';
								}
								echo $msg_avatar;
                    ?>

                                <br />

                                <input type = "checkbox" value = "1" name = "deleteAvatar"/><i> <?php echo _USER_DELETEAV; ?></i>

                    <?php
                            }
                            else
                            {
                                echo _NON_SELECTED;
                    ?>

                            <br /> <a href = "<?php echo JRoute::_(KUNENA_LIVEURLREL.'&func=myprofile&do=avatar');?>"> <?php echo _SET_NEW_AVATAR; ?></a>

                    <?php
                            }
                    ?>

                        <input type = "hidden" value = "<?php echo $avatar;?>" name = "avatar"/>
                    <?php
                        }
                    ?>
                </td>

            </tr>
<?php if ($avatar != ""){ ?>
            <tr><td colspan = "2" align="center">
            <input type = "submit" class = "button" value = "<?php echo _GEN_DELETE;?>"/></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</form>
            <!-- F: My AVATAR -->

<?php
if ($fbConfig->allowavatarupload)
{
?>

            <!-- B: Upload -->
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

        echo '<form action="' . JRoute::_(KUNENA_LIVEURLREL . '&func=myprofile&do=avatar&action=upload') . '" method="post" name="adminForm" enctype="multipart/form-data">';
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
            <!-- B: Upload -->
<?PHP
    }

	} // allow avatar upload

    if ($fbConfig->allowavatargallery)
    {
?>
            <!-- B: Gallery -->
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

                location.href = "<?php echo JRoute::_(KUNENA_LIVEURLREL . '&func=myprofile&do=avatar&gallery=');?>"+gallery;
            }
                    //-->
        </script>

<?php
        echo "<p align=\"center\">";
        get_dirs(KUNENA_PATH_UPLOADED .DS. 'avatars/gallery', "categoryid", $gallery);
        echo '<input type="button" value="'. _KUNENA_GO .'" class="button" onclick="switch_avatar_category(jQuery(\'#avatar_category_select\').val())" />'."\n";
        echo "</p>";
        echo "<br />\n";
        echo '<form action="' . JRoute::_(KUNENA_LIVEURLREL . '&func=myprofile&do=avatar&action=gallery') . '" method="post" name="adminForm">';
        echo "<table width='100%' border='0' cellpadding='4' cellspacing='2'>";
        echo "<tr align='center' valign='middle'>";

        if ($gallery == "default") $gallery='';

	$gallery1 = $gallery2 = '';
        if ($gallery)
        {
            $gallery1 = "/" . str_replace("%20", " ", $gallery);
            $gallery2 = str_replace("%20", " ", $gallery) . "/";
        }

        $avatar_gallery_path = KUNENA_PATH_UPLOADED .DS. 'avatars/gallery' . $gallery1;
        $avatar_images = array ();
        $avatar_images = kn_myprofile_display_avatar_gallery($avatar_gallery_path);

        for ($i = 0; $i < count($avatar_images); $i++)
        {
            $j = $i + 1;
            echo '<td>';
            //echo '<img src="'.$avatar_gallery_path .DS. $avatar_images[$i].'">';
            echo '<img src="' . KUNENA_LIVEUPLOADEDPATH . '/avatars/gallery/' . $gallery2 . $avatar_images[$i] . '" alt="" />';
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
                if (!KUNENA_fmodReplace(($j), 5)) {
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
            <!-- F: Gallery -->
<?php
    }
}
?>
 <!-- F:My Profile Right -->

<?php
}
else
{
 echo '<b>'. _COM_A_REGISTERED_ONLY.'</b><br />';
   echo _FORUM_UNAUTHORIZIED2 ;
}
?>