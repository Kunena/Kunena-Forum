<?php
/**
 * @version		$Id: default.php 1024 2009-08-19 06:18:15Z fxstein $
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;
JHtml::stylesheet('default.css', 'components/com_kunena/media/css/');
?>
<div id="kunena">
<?php echo $this->loadCommonTemplate('header'); ?>
<?php echo $this->loadCommonTemplate('pathway'); ?>

<table class="forum_body">
	<thead>
		<tr>
			<th>
				<h1><?php echo $this->escape($this->title); ?></h1>
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
                <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr valign="top">
                      <td width="170"><img src="<?php echo JURI::Base(); ?>components/com_kunena/media/images/logo.png" alt="<?php echo JText::_('K_LOGO'); ?>" title="<?php echo JText::_('K_LOGO'); ?>" /></td>
                      <td><div> An open source project like Kunena requires the dedication and investment of personal time from various contributors.
                          This version of Kunena Forum has been made possible by the following contributors (in alphabetical ordering):</div></td>
                    </tr>
                    <tr valign="top">
                      <td colspan="2" style="padding-left:20px;padding-right:20px;"><ul>
                          <li><a href="http://www.starVmax.com" target='_blank' rel='follow'>fxstein</a> Kunena developer and admin of the world largest Yamaha Star VMax community at <a href="http://www.starVmax.com/forum/" target='_blank' rel='follow'>www.starVmax.com/forum/</a></li>
                          <li><a href="http://www.kunena.com/community/profile?userid=66" target='_blank' rel='follow'>johnnydement</a> Kunena moderator</li>
                          <li><a href="http://www.kunena.com/community/profile?userid=2171" target='_blank' rel='follow'>LDA</a> Kunena moderator</li>
                          <li><a href="http://www.herppi.net" target='_blank' rel='follow'>Matias</a> Kunena developer</li>
                          <li><a href="http://www.camelcity.com" target='_blank' rel='follow'>Noel Hunter</a> Kunena developer and admin of <a href="http://www.housecalls.com/view-qaa?func=listcat" target='_blank' rel='follow'>House Calls Q&A Forum/</a></li>
                          <li><a href="http://www.kunena.com/community/profile?userid=122" target='_blank' rel='follow'>Roland76</a> Kunena developer</li>
                          <li><a href="http://www.kunena.com/community/profile?userid=114" target='_blank' rel='follow'>severdia</a> Kunena developer</li>
                          <li><a href="http://www.kunena.com/community/profile?userid=314" target='_blank' rel='follow'>Spock</a> Kunena moderator</li>
                          <li><a href="http://www.kunena.com/community/profile?userid=148" target='_blank' rel='follow'>whouse</a> Kunena developer</li>
                          <li><a href="http://www.kunena.com/community/profile?userid=1288" target='_blank' rel='follow'>xillibit</a> Kunena moderator</li>
                          <li><a href="http://www.kunena.com/community/profile?userid=447" target='_blank' rel='follow'>@quila</a> Kunena moderator</li>
                        </ul></td>
                    </tr>
                    <tr valign="top">
                      <td colspan="2"><div>Special thanks go to Beat and the CB Testing team, Ida and JoniJnm for significant contributions to Kunena. In addition many members of <a href="http://www.kunena.com" target='_blank' rel='follow'>www.Kunena.com</a> have contributed and helped make this a more stable and bugfree version.
                          Our Thanks go out to all contributors of Kunena! Greetings from the global Kunena forum team! <br />
                          <br />
                          <?php
                
                // Add a link to go back to the latest category we where viewing...
                //echo '<div>To return to the forum ' . CKunenaLink::GetCategoryLink('showcat', $catid, _USER_RETURN_B, $rel='nofollow') . '<div>';
                ?>
                        </div></td>
                    </tr>
                  </table></td>
              </tr>
	</tbody>
</table>
<?php echo JText::_('K_INSTALLED_VERSION'); ?>: <?php echo $this->credits['version']->version; ?> | <?php echo $this->credits['version']->versiondate; ?> | <?php echo $this->credits['version']->build; ?> [<?php echo $this->credits['version']->versionname; ?>] | Copyright: © 2008-2009 <a href="http://www.kunena.com" target='_blank' rel='follow'>www.Kunena.com</a> | <?php echo JText::_('K_LICENSE'); ?>: <a href = "http://www.gnu.org/copyleft/gpl.html" target = "_blank">GNU GPL</a> 
<!-- B: Category List Bottom -->
<div class="bottom_info_box">
	<?php echo $this->loadCommonTemplate('forumcat'); ?>
</div>
<!-- F: Category List Bottom -->

<?php
echo $this->loadCommonTemplate ( 'footer' );
?>
	</div>
?>