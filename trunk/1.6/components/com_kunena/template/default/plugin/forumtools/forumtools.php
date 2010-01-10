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
$kunena_config =& CKunenaConfig::getInstance();

$func = JString::strtolower ( JRequest::getCmd ( 'func', 'listcat' ) );
$catid = JRequest::getInt('catid', 0);
$id = JRequest::getInt('id', 0);
?>

<script type = "text/javascript">
    jQuery(document).ready(function()
    {
        jQuery("#jrftsw").click(function()
        {
            jQuery(".forumtools_contentBox").slideToggle("fast");
            return false;
        });
    });
</script>

<div id = "fb_ft-cover">
    <div id = "forumtools_control">
        <a href = "#" id = "jrftsw" class = "forumtools"><?php echo _KUNENA_FORUMTOOLS;?></a>
    </div>

    <div class = "forumtools_contentBox" id = "box1">
        <div class = "forumtools_content" id = "subBox1">
            <ul>
                <li>
                <?php
                echo CKunenaLink::getSefHrefLink(KUNENA_LIVEURLREL . '&amp;func=post&amp;do=reply&amp;catid=' . $catid, _GEN_POST_NEW_TOPIC, _GEN_POST_NEW_TOPIC , 'follow');
                ?>

                </li>

                <?php
                if ($func == "view")
                {
                    if ($kunena_config->enablepdf)
                    {
                ?>

                        <li>
                        <?php
                        echo CKUnenaLink::getSefHrefLink(KUNENA_LIVEURLREL . '&amp;id=' . $id . '&amp;catid=' . $catid . '&amp;func=fb_pdf' , _GEN_PDF , _GEN_PDF , 'nofollow');
                        ?>

                        </li>

                <?php
                    }
                }
                ?>

                <li>
                <?php
                if ($kunena_my->id != 0) {
                    echo CKUnenaLink::getSefHrefLink(KUNENA_LIVEURLREL . '&amp;func=markThisRead&amp;catid=' . $catid , _GEN_MARK_THIS_FORUM_READ , _GEN_MARK_THIS_FORUM_READ, 'nofollow' );
                }
                ?>

                </li>
                <li>
                <?php
                echo CKUnenaLink::getSefHrefLink(KUNENA_LIVEURLREL . '&amp;func=latest', _GEN_LATEST_POSTS, _GEN_LATEST_POSTS, 'follow' );
                ?>

                </li>

                <?php
                if ($kunena_config->enablerulespage)
                {
				 if ($kunena_config->rules_infb) {
                    echo '<li>';
                    echo CKUnenaLink::getSefHrefLink(KUNENA_LIVEURLREL . '&amp;func=rules', _GEN_RULES , _GEN_RULES, 'follow' );
                    echo '</li>';
					} else {
					echo '<li>';
					echo CKUnenaLink::getSefHrefLink($kunena_config->rules_link , _GEN_RULES , _GEN_RULES, 'follow' );
                    echo '</li>';
					}
                }
                if ($kunena_config->enablehelppage)
                {
				 if ($kunena_config->help_infb) {
					echo '<li>';
					echo CKUnenaLink::getSefHrefLink(KUNENA_LIVEURLREL . '&amp;func=help' , _GEN_HELP , _GEN_HELP, 'follow' );
					echo '</li>';
					} else {
					echo '<li>';
					echo CKUnenaLink::getSefHrefLink( $kunena_config->help_link , _GEN_HELP , _GEN_HELP , 'follow');
					echo '</li>';
					}
				}
                ?>
            </ul>
        </div>
    </div>
</div>