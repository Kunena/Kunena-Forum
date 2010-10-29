<?php
/**
* @version $Id: initialize.php 3728 2010-10-15 20:16:32Z fxstein $
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2010 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/
defined( '_JEXEC' ) or die();

?>
<meta name="application-name" content="Forum" />
<meta name="msapplication-tooltip" content="Welcome on a Kunena forum" />
<meta name="msapplication-navbutton-color" content="#5388b4" />
<meta name="msapplication-task" content="name=Recent Topics;action-uri=index.php?option=com_kunena&view=latest;icon-uri=http://www.newf1.nl/recent.ico"/>
<meta name="msapplication-task" content="name=New topic;action-uri=index.php?option=com_kunena&view=post&do=new;icon-uri=http://www.newf1.nl/new.ico"/>
<meta name="msapplication-task" content="name=Search;action-uri=index.php?option=com_kunena&view=search;icon-uri=http://www.newf1.nl/search.ico"/>
<meta name="msapplication-task" content="name=Subscribe to RSS;action-uri=index.php?option=com_kunena&func=rss;icon-uri=http://www.newf1.nl/rss.ico"/>
<meta name="msapplication-task" content="name=Kunena;action-uri=http://www.kunena.com;icon-uri=http://www.newf1.nl/kunena.ico"/>
<link rel="shortcut icon" type="image/vnd.microsoft.icon" href="/kunena.ico"/>
<script type='text/javascript'>
function MyJumplist() {
window.external.msSiteModeCreateJumplist('Forum');
window.external.msSiteModeAddJumpListItem('Profile', 'index.php?option=com_kunena&view=profile','http://www.newf1.nl/profile.ico');
window.external.msSiteModeAddJumpListItem('Login', 'index.php?option=com_kunena', 'http://www.newf1.nl/login.ico');
window.external.msSiteModeShowJumplist();
}
MyJumplist();
</script>
