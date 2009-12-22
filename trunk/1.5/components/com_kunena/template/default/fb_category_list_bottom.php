<?php
/**
* @version $Id: fb_category_list_bottom.php 708 2009-05-11 09:09:49Z mahagr $
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
$fbConfig =& CKunenaConfig::getInstance();
?>
	<tr>
		<td class="fb_list_markallcatsread">
                <?php
                if ($kunena_my->id != 0)
                {
                ?>

                    <form action = "<?php echo KUNENA_LIVEURLREL; ?>" name = "markAllForumsRead" method = "post">
                        <input type = "hidden" name = "markaction" value = "allread"/>
                        <input type = "submit" class = "fb_button button<?php echo $boardclass ;?> fbs" value = "<?php echo _GEN_MARK_ALL_FORUMS_READ ;?>"/>
                    </form>

                <?php
                }
                ?>
		</td>
		<td class="fb_list_categories">
                <?php
                if ($fbConfig->enableforumjump)
                    require (KUNENA_PATH_LIB .DS. 'kunena.forumjump.php');
                ?>
		</td>
	</tr>

