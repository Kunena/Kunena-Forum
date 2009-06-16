<?php
/**
* @version $Id:kunena_category_list_bottom.php 884 2009-06-16 03:48:56Z fxstein $
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
$kunenaConfig =& CKunenaConfig::getInstance();
?>
	<tr>
		<td class="kunena_list_markallcatsread">
                <?php
                if ($kunena_my->id != 0)
                {
                ?>

                    <form action = "<?php echo KUNENA_LIVEURLREL; ?>" name = "markAllForumsRead" method = "post">
                        <input type = "hidden" name = "markaction" value = "allread"/>
                        <input type = "submit" class = "kunena_button button<?php echo $boardclass ;?> kunenas" value = "<?php echo _GEN_MARK_ALL_FORUMS_READ ;?>"/>
                    </form>

                <?php
                }
                ?>
		</td>
		<td class="kunena_list_categories">
                <?php
                if ($kunenaConfig->enableforumjump)
                    require (KUNENA_PATH_LIB .DS. 'kunena.forumjump.php');
                ?>
		</td>
	</tr>

