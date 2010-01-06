<?php
/**
* @version $Id: kunena.special.upgrade.poll.php 1426 2010-01-02 09:13:33Z xillibit $
* Kunena Component
* @package Kunena
* @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*
* Kunena Upgrade poll
* component: com_kunena
**/

defined( '_JEXEC' ) or die('Restricted access');

// Add custom upgrade code here
// Most or all sql statements should be covered within comupgrade.xml

$kunena_db =& JFactory::getDBO();

//get the database prefixe and set the table to search
$prefixfinal = $kunena_db->getPrefix().'_fb_polls_datas';

//get database name
$jconfig = new JConfig();
$jdbname = 'Tables_in_'.$jconfig->db;

$kunena_db->setQuery("SHOW TABLES");
$kunena_db->query() or check_dberror("Unable to get all the tables");
$databasetables = $kunena_db->loadObjectList();

$pollupgrade = 0;
for ($i=0;$i < sizeof($databasetables);$i++)
{
  if ($databasetables[$i]->$jdbname == $prefixfinal)
  {
    $pollupgrade = 1;
  }
}

if ($pollupgrade)
{
	$kunena_db->setQuery("ALTER TABLE #__fb_messages MODIFY COLUMN poll_exist SMALLINT(3)");
	$kunena_db->query() or check_dberror("Unable to alter table fb_messages");
	$kunena_db->setQuery("ALTER TABLE #__fb_polls CHANGE topicid threadid INT(11), ADD KEY (threadid), ADD COLUMN catid INT(11), ADD Column polltimetolive TIMESTAMP NULL DEFAULT '0000-00-00 00:00:00', DROP COLUMN voters,DROP COLUMN options");
	$kunena_db->query() or check_dberror("Unable to alter table fb_polls");
	$kunena_db->setQuery("SELECT * FROM #__fb_polls");
	$kunena_db->query();
	$pollsdatas = $kunena_db->loadObjectList();
    if (isset($pollsdatas))
    {
		  for ($i=0;$i < sizeof($pollsdatas);$i++)
		  {
		  	$kunena_db->setQuery("SELECT catid FROM #__fb_messages WHERE thread={$pollsdatas[$i]->threadid}");
			$kunena_db->query();
			$pollcatid = $kunena_db->loadResult();
			$kunena_db->setQuery("UPDATE #__fb_polls SET catid={$pollcatid}");
			$kunena_db->query();
		  }
	}
	$kunena_db->setQuery("CREATE TABLE IF NOT EXISTS #__fb_polls_options (
			id int(11) NOT NULL auto_increment,
			pollid int(11) default NULL,
			text varchar(50) default NULL,
			votes int(11) default NULL,
			PRIMARY KEY  (id),
			KEY pollid (pollid)
		) DEFAULT CHARSET=utf8");
	$kunena_db->query() or check_dberror("Unable to create table fb_polls_options");
	$kunena_db->setQuery("SELECT * FROM #__fb_polls_datas");
	$kunena_db->query();
	$datapolls = $kunena_db->loadObjectList();
	if (isset($datapolls))
	{
	  	for ($i=0;$i < sizeof($datapolls);$i++)
		{
			$kunena_db->setQuery("INSERT INTO #__fb_polls_options (pollid,text,votes) VALUES('{$datapolls[$i]->pollid}','{$datapolls[$i]->text}','{$datapolls[$i]->hits}')");
		 	$kunena_db->query();
	  	}
	}
	$kunena_db->setQuery("DROP TABLE IF EXISTS #__fb_polls_datas");
	$kunena_db->query() or check_dberror("Unable to alter table fb_polls_datas");
	$kunena_db->setQuery("ALTER TABLE #__fb_polls_users ADD COLUMN votes SMALLINT(3),ADD COLUMN lasttime TIMESTAMP, ADD UNIQUE KEY (pollid,userid)");
	$kunena_db->query() or check_dberror("Unable to alter table fb_polls_users");
}
?>
