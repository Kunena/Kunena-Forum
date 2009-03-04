<?php
/**
* @version $Id: fb_credits.php 1026 2008-08-25 02:48:14Z fxstein $
* Kunena Component
* @package Kunena
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
**/

defined( '_JEXEC' ) or die('Restricted access');

global $fbConfig;
global $is_Moderator;

// Team credits page is not translated

?>

<div class="<?php echo $boardclass; ?>_bt_cvr1">
  <div class="<?php echo $boardclass; ?>_bt_cvr2">
    <div class="<?php echo $boardclass; ?>_bt_cvr3">
      <div class="<?php echo $boardclass; ?>_bt_cvr4">
        <div class="<?php echo $boardclass; ?>_bt_cvr5">
          <table class = "fb_blocktable" id ="fb_forumcredits" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
            <thead>
              <tr>
                <th> <div class = "fb_title_cover"> <span class="fb_title" >Team credits</span> </div>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class = "<?php echo $boardclass; ?>creditsdesc"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr valign="top">
                      <td width="170"><img src="<?php echo KUNENA_DIRECTURL . '/template/default/images/kunena.logo.png';?>" alt="Kunena"  align="left" hspace="5" vspace="5"/></td>
                      <td><div  class="fb_credits_intro"> An open source project like Kunena requires the dedication and investment of personal time from various contributors.
                          This version of Kunena Forum has been made possible by the following contributors (in alphabetical ordering):</div></td>
                    </tr>
                    <tr valign="top">
                      <td colspan="2" style="padding-left:20px;padding-right:20px;"><ul  class="fb_team">
                          <li class="fb_teammember"><a href="http://www.starVmax.com" target='_blank' rel='follow'>fxstein</a> Kunena developer and admin of the world largest Yamaha Star VMax community at <a href="http://www.starVmax.com/Forum/" target='_blank' rel='follow'>www.starVmax.com/Forum/</a></li>
                          <li class="fb_teammember"><a href="http://www.racoonpages.de/" target='_blank' rel='follow'>racoon</a> Kunena developer</li>
                          <li class="fb_teammember"><a href="http://www.kunena.com/index.php?option=com_community&view=profile&userid=63" target='_blank' rel='follow'>Matias</a> Kunena developer</li>

                          <li class="fb_teammember"><a href="http://www.bestofjoomla.com" target='_blank' rel='follow'>Best Of Joomla</a> The Home of FireBoard the basis of <a href="www.kunena.com" target='_blank' rel='follow'>Kunena</a>.</li>
                        </ul></td>
                    </tr>
                    <tr valign="top">
                      <td colspan="2"><div  class="fb_credits_more">In addition many members of <a href="http://www.kunena.com" target='_blank' rel='follow'>www.Kunena.com</a> have contributed and helped make this a more stable and bugfree version.
                          Our Thanks go out to all contributors of Kunena! Greetings from the global Kunena forum team! <br />
                          <br />
                          <?php
                $catid = (int)$catid;

                // Add a link to go back to the latest category we where viewing...
                echo '<div>To return to the forum ' . CKunenaLink::GetCategoryLink('showcat', $catid, _USER_RETURN_B, $rel='nofollow') . '<div>';
                ?>
                        </div></td>
                    </tr>
                  </table></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Begin: Forum Jump -->
<div class="<?php echo $boardclass; ?>_bt_cvr1">
  <div class="<?php echo $boardclass; ?>_bt_cvr2">
    <div class="<?php echo $boardclass; ?>_bt_cvr3">
      <div class="<?php echo $boardclass; ?>_bt_cvr4">
        <div class="<?php echo $boardclass; ?>_bt_cvr5">
          <table  class = "fb_blocktable" id="fb_bottomarea"  border = "0" cellspacing = "0" cellpadding = "0">
            <thead>
              <tr>
                <th class = "th-right"> <?php
                //(JJ) FINISH: CAT LIST BOTTOM
                if ($fbConfig->enableforumjump)
                    require_once (KUNENA_PATH_LIB .DS. 'kunena.forumjump.php');
                ?>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Finish: Forum Jump -->
