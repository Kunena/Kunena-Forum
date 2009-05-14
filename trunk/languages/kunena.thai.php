<?php
/**
* @version $Id: kunena.english.php 602 2009-04-03 20:13:16Z fxstein $
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
*
* ไฟล์เดิมแปลงมาจาก http://naywan.icspace.net
* ภาษไทยโดย** http://www.noartclub.com edit by pie & slotpro Last edit 23/2/2009 ******
**/

defined( '_JEXEC' ) or defined ('_VALID_MOS') or die('Direct Access to this location is not allowed.');

DEFINE ('_KUNENA_FILEATTACH','แนบไฟล์');
DEFINE ('_KUNENA_FILENAME','ชื่อไฟล์');
DEFINE ('_KUNENA_FILESIZE','ขนาดไฟล์');
DEFINE('_KUNENA_QMESSAGE_NOTE', 'กรุณาใส่ข้อความ,คุณสามารถใช้ แท็ก BBCODE ได้.');
DEFINE ('_KUNENA_FORUMTOOLS','เครื่องมือ');
DEFINE ('_KUNENA_MSG_CODE','CODE');
DEFINE ('_KUNENA_IN_FORUM','ในหมวด');
//userlist
DEFINE ('_KUNENA_USRL_USERLIST','รายการสมาชิกทั้งหมดในรายการ');
DEFINE ('_KUNENA_USRL_REGISTERED_USERS','%s มีจำนวน <b>%d</b> สมาชิกที่ลงทะเบียน');
DEFINE ('_KUNENA_USRL_SEARCH_ALERT','กรุณาใส่ชื่อสมาชิกที่ต้องการค้นหา!');
DEFINE ('_KUNENA_USRL_SEARCH','ค้นหาสมาชิก');
DEFINE ('_KUNENA_USRL_SEARCH_BUTTON','ค้นหา');
DEFINE ('_KUNENA_USRL_LIST_ALL','รายการสมาชิกทั้งหมด');
DEFINE ('_KUNENA_USRL_NAME','ชื่อ');
DEFINE ('_KUNENA_USRL_USERNAME','ยูซเซอร์เนม');
DEFINE ('_KUNENA_USRL_GROUP','กลุ่ม');
DEFINE ('_KUNENA_USRL_POSTS','จำนวนตอบ');
DEFINE ('_KUNENA_USRL_KARMA','จิตพิสัย');
DEFINE ('_KUNENA_USRL_HITS','ผู้ชม');
DEFINE ('_KUNENA_USRL_EMAIL','อีเมล์');
DEFINE ('_KUNENA_USRL_USERTYPE','กลุ่ม');
DEFINE ('_KUNENA_USRL_JOIN_DATE','สมัครสมาชิกวันที่');
DEFINE ('_KUNENA_USRL_LAST_LOGIN','เข้าใช้ล่าสุด');
DEFINE ('_KUNENA_USRL_NEVER','Never');
DEFINE ('_KUNENA_USRL_ONLINE','สถานะ');
DEFINE ('_KUNENA_USRL_AVATAR','รูปส่วนตัว');
DEFINE ('_KUNENA_USRL_ASC','Ascending');
DEFINE ('_KUNENA_USRL_DESC','Descending');
DEFINE ('_KUNENA_USRL_DISPLAY_NR','รูปแบบ');
DEFINE ('_KUNENA_USRL_DATE_FORMAT','%d.%m.%Y');

DEFINE('_KUNENA_ADMIN_CONFIG_PLUGINS','ปลั๊กอิน');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST','รายชื่อสมาชิก');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_ROWS_DESC','จำนวนแถวรายชื่อสมาชิก');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_ROWS','หนึ่งแถวต่อหนึ่งสมาชิก');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE','สถานะออนไลน์');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERONLINE_DESC','แสดงสถานะของสมาชิกแต่ละท่านว่าออนไลน์หรือไม่');

DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_AVATAR','แสดงรูปส่วนตัว');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERLIST_AVATAR_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_NAME','แสดงชื่อจริง');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_name_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERNAME','แสดงยูซเซอร์เนม');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERNAME_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_GROUP','แสดงกลุ่มของสมาชิก');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_GROUP_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_POSTS','แสดงคำตอบ');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_POSTS_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_KARMA','แสดงจิตพิสัย');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_KARMA_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL','แสดงอีเมล์');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_EMAIL_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERTYPE','Show User Type');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_USERTYPE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE','แสดงวันที่สมัคร');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_JOINDATE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE','แสดงการเข้าใชล่าสุด');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_LASTVISITDATE_DESC','');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_HITS','แสดงจำนวนผู้ที่ชมข้อมูล');
DEFINE('_KUNENA_ADMIN_CONFIG_USERLIST_HITS_DESC','');

// added by Ivo Apostolov: Cleaning up of admin.fireboard.php from hardcoded strings
DEFINE('_KUNENA_DBWIZ', 'Database Wizard');
DEFINE('_KUNENA_DBMETHOD', 'Please select which method you want to complete your installation:');
DEFINE('_KUNENA_DBCLEAN', 'Clean installation');
DEFINE('_KUNENA_DBUPGRADE', 'Upgrade From Joomlaboard');
DEFINE('_KUNENA_TOPLEVEL', 'Top Level Category');
DEFINE('_KUNENA_REGISTERED', 'ลงทะเบียน');
DEFINE('_KUNENA_PUBLICBACKEND', 'Public Backend');
DEFINE('_KUNENA_SELECTANITEMTO', 'Select an item to');
DEFINE('_KUNENA_ERRORSUBS', 'Something went wrong deleting the messages and subscriptions');
DEFINE('_KUNENA_WARNING', 'Warning...');
DEFINE('_KUNENA_CHMOD1', 'You need to chmod this to 766 in order for the file to be updated.');
DEFINE('_KUNENA_YOURCONFIGFILEIS', 'Your config file is');
DEFINE('_KUNENA_FIREBOARD', 'เว็บบอร์ด');
DEFINE('_KUNENA_CLEXUS', 'Clexus PM');
DEFINE('_KUNENA_CB', 'Community Builder');
DEFINE('_KUNENA_MYPMS', 'myPMS II Open Source');
DEFINE('_KUNENA_UDDEIM', 'Uddeim');
DEFINE('_KUNENA_JIM', 'JIM');
DEFINE('_KUNENA_MISSUS', 'Missus');
DEFINE('_KUNENA_SELECTTEMPLATE', 'Select Template');
DEFINE('_KUNENA_CFNW', 'เกิดข้อผิดพลาด: ไม่สามารถเขียนไฟล์ได้');
DEFINE('_KUNENA_CFS', 'การตั้งค่าคอนฟิคได้ถูกบันทึกเรียบร้อยแล้ว');
DEFINE('_KUNENA_CFCNBO', 'เกิดข้อผิดพลาด: ไม่สามารถเปิดไฟล์นี้ได้.');
DEFINE('_KUNENA_TFINW', 'ไม่สามารถเขียนไฟล์ได้.');
DEFINE('_KUNENA_FBCFS', 'Fireboard CSS file saved.');
DEFINE('_KUNENA_SELECTMODTO', 'Select an moderator to');
DEFINE('_KUNENA_CHOOSEFORUMTOPRUNE', 'เลือกรายการฟอรั่มที่ต้องการล้าง!');
DEFINE('_KUNENA_DELMSGERROR', 'ไม่สามารถลบข้อความได้:');
DEFINE('_KUNENA_DELMSGERROR1', 'Deleting messages texts failed:');
DEFINE('_KUNENA_CLEARSUBSFAIL', 'การล้างข้อมูลบันทึกผิดพลาด:');
DEFINE('_KUNENA_FORUMPRUNEDFOR', 'Forum pruned for');
DEFINE('_KUNENA_PRUNEDAYS', 'วัน');
DEFINE('_KUNENA_PRUNEDELETED', 'ลบ:');
DEFINE('_KUNENA_PRUNETHREADS', 'หัวข้อ ');
DEFINE('_KUNENA_ERRORPRUNEUSERS', 'Error pruning users:');
DEFINE('_KUNENA_USERSPRUNEDDELETED', 'Users pruned; Deleted:');
DEFINE('_KUNENA_PRUNEUSERPROFILES', 'รายละเอียดสมาชิก');
DEFINE('_KUNENA_NOPROFILESFORPRUNNING', 'ไม่มีรายการสมาชิกที่เลือก.');
DEFINE('_KUNENA_TABLESUPGRADED', 'Fireboard Tables are upgraded to version');

// Default_EX template
DEFINE('_KUNENA_ALL_DISCUSSIONS', 'ตอบกลับล่าสุด');
DEFINE('_KUNENA_MY_DISCUSSIONS', 'คำตอบของฉัน');
DEFINE('_KUNENA_MY_DISCUSSIONS_DETAIL', 'Discussions I have started or replied to');
DEFINE('_KUNENA_CATEGORY', 'หมวดหมู่:');
DEFINE('_KUNENA_CATEGORIES', 'หมวดหมู่');
DEFINE('_KUNENA_POSTED_AT', 'Posted');
DEFINE('_KUNENA_AGO', 'ที่ผ่านมา');
DEFINE('_KUNENA_DISCUSSIONS', 'Discussions');
DEFINE('_KUNENA_TOTAL_THREADS', 'Total Threads:');
DEFINE('_SHOW_DEFAULT', 'Default');
DEFINE('_SHOW_MONTH', 'เดือน');
DEFINE('_SHOW_YEAR', 'ปี');
DEFINE('_KUNENA_QMESSAGE_NOTE', 'กล่องตอบด่วน');

// added by Ivo Apostolov: Cleaning up of admin.fireboard.php - SAMPLE DATA
DEFINE('_KUNENA_FORUMCATEGORY', 'Forum Category');
DEFINE('_KUNENA_SAMPLWARN1', '-- Make absolutely sure that you load the sample data on completely empty fireboard tables. If anything is in them, it will not work!');
DEFINE('_KUNENA_FORUM1', 'Forum 1');
DEFINE('_KUNENA_SAMPLEPOST1', 'Sample Post 1');
DEFINE('_KUNENA_SAMPLEFORUM11', 'Sample Forum 1\r\n');
DEFINE('_KUNENA_SAMPLEPOST11', '[b][size=4][color=#FF6600]Sample Post[/color][/size][/b]\nCongratulations with your new Forum!\n\n[url=http://bestofjoomla.com]- Best of Joomla[/url]');
DEFINE('_KUNENA_SAMPLESUCCESS', 'การโหลดข้อมูลตัวอย่างเรียบร้อยแล้ว');
// added by Ivo Apostolov: Cleaning up of admin.fireboard.php - Other adjustments to the file
DEFINE('_KUNENA_CBADDED', 'Community Builder profile added');
DEFINE('_KUNENA_IMGDELETED', 'รูปภาพได้ถูกลบโดยผู้ใช้เรียบร้อยแล้ว');
DEFINE('_KUNENA_FILEDELETED', 'ไฟล์ได้ถูกลบโดยผู้ใช้เรียบร้อยแล้ว');
DEFINE('_KUNENA_NOPARENT', 'ไม่มีฟอรั่มย่อยที่เลือก');
DEFINE('_KUNENA_DIRCOPERR', 'เกิดข้อผิดพลาด: ไฟล์ข้อมูล');
DEFINE('_KUNENA_DIRCOPERR1', 'ไม่สามารถคัดลอกได้!\n');
DEFINE('_KUNENA_INSTALL1', '<strong>Fireboard Laithai Forum</strong> Component <em>for Joomla Laithai! CMS</em> <br />&copy; 2006 - 2007 by JoomlaCorner&Laithai Team.<br>All rights reserved.');
DEFINE('_KUNENA_INSTALL2', 'Transfer/Installation :</code></strong><br /><br /><font color="red"><b>succesfull');
// added by aliyar 
DEFINE('_KUNENA_FORUMPRF_TITLE', 'Profile Settings');
DEFINE('_KUNENA_FORUMPRF', 'Profile');
DEFINE('_KUNENA_FORUMPRRDESC', 'If you have Clexus PM or Community Builder component installed, you can configure fireboard to use the user profile page.');
DEFINE('_KUNENA_USERPROFILE_PROFILE', 'ข้อมูลส่วนตัว');
DEFINE('_KUNENA_USERPROFILE_PROFILEHITS', '<b>จำนวนผู้ชมข้อมูล</b>');
DEFINE('_KUNENA_USERPROFILE_MESSAGES', 'ข้อความทั้งหมดสำหรับสมาชิกท่านนี้');
DEFINE('_KUNENA_USERPROFILE_TOPICS', 'หัวข้อ ');
DEFINE('_KUNENA_USERPROFILE_STARTBY', 'เริ่ม โดย');
DEFINE('_KUNENA_USERPROFILE_CATEGORIES', 'ประเภท');
DEFINE('_KUNENA_USERPROFILE_DATE', 'วันที่');
DEFINE('_KUNENA_USERPROFILE_HITS', 'ผู้ชม');
DEFINE('_KUNENA_USERPROFILE_NOFORUMPOSTS', 'ไม่มีข้อความสำหรับสมาชิกท่านนี้');
DEFINE('_KUNENA_TOTALFAVORITE', 'รายการถูกบันทึก:  ');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_COLON', 'จำนวนฟอรั่มย่อยในหนึ่งประเภท  ');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_COLONDESC', 'จำนวนฟอรั่มย่อยที่ถูกแสดงในหน้าแรก ');
DEFINE('_KUNENA_SUBSCRIPTIONSCHECKED', 'เปิดให้มีการตอบรับการติดตามหัวข้อ ');
DEFINE('_KUNENA_SUBSCRIPTIONSCHECKED_DESC', 'เซ็ทค่า &quot;ใช่&quot; เพื่อให้มีกล่องยืนยันก่อนบันทึกหัวข้อและส่งไปยังอีเมลล์');
// Added by Ivo Apostolov
// Errors (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_ERROR1', 'กรุณาตั้งชื่อฟอรั่มและเลือกรายการประเภท');
// Forum Configuration (New in Fireboard)
DEFINE('_KUNENA_SHOWSTATS', 'แสดงสถานะ');
DEFINE('_KUNENA_SHOWSTATSDESC', 'หากคุณต้องการให้แสดงสถานะ, ให้เลือก "ใช่"');
DEFINE('_KUNENA_SHOWWHOIS', 'แสดงผู้ชมในขณะนี้');
DEFINE('_KUNENA_SHOWWHOISDESC', 'หากคุณต้องการให้แสดงผู้ชมในขณะนี้, ให้เลือก "ใช่"');
DEFINE('_KUNENA_STATSGENERAL', 'แสดงสถานะทั่วไป');
DEFINE('_KUNENA_STATSGENERALDESC', '');
DEFINE('_KUNENA_USERSTATS', 'แสดงสถานะ user');
DEFINE('_KUNENA_USERSTATSDESC', '');
DEFINE('_KUNENA_USERNUM', 'จำนวน user');
DEFINE('_KUNENA_USERPOPULAR', 'แสดงหัวข้อที่ถูกอ่านบ่อยที่สุด');
DEFINE('_KUNENA_USERPOPULARDESC', '');
DEFINE('_KUNENA_NUMPOP', 'จำนวนหัวข้อที่ถูกอ่านบ่อยที่สุด');
DEFINE('_KUNENA_INFORMATION',
    'Best of Joomla team is proud to announce the release of Fireboard 1.0.0. It is a powerful and stylish forum component for a well deserved content management system, Joomla!. It is initially based on the hard work of Joomlaboard team and most of our praises goes to their team.Some of the main features of Fireboard can be listed as below (in addition to JB&#39;s current features):<br /><br /><ul><li>A much more designer friendly forum system. It is close to SMF templating system having a simpler structue. With very few steps you can modify the total look of the forum. Thanks goes to the great designers in our team.</li><li>Unlimited subcategory system with better administration backend.</li><li>Faster system and better coding experience for 3rd parties.</li><li>The same<br /></li><li>Profilebox at the top of the forum</li><li>Support for popular PM systems, such as ClexusPM and Uddeim</li><li>Basic plugin system (practical rather than perfec)</li><li>Language-defined icon system.<br /></li><li>Sharing image system of other templates. So, choice between templates and image series is possible</li><li>You can add joomla modules inside the forum template itself. Wanted to have banner inside your forum?</li><li>Favourite threads selection and management</li><li>Forum spotlights and highlights</li><li>Forum announcements and its panel</li><li>Latest messages (Tabbed)</li><li>Statistics at bottom</li><li>Who&#39;s online, on what page?</li><li>Category specific image system</li><li>Enhanced pathway</li><li><strong>Joomlaboard import, SMF in plan to be releaed pretty soon</strong></li><li>RSS, PDF output</li><li>Advanced search (under developement)</li><li>Community builder and Clexus PM profile options</li><li>Avatar management : CB and Clexus PM options<br /></li></ul><br />Please keep in mind that this release is not meant to be used on production sites, even though we have tested it through. We are planning to continue to work on this project, as it is used on our several projects, and we would be pleased if you could join us to bring a bridge-free solution to Joomla! forums.<br /><br />This is a collaborative work of several developers and designers that have kindly participated and made this release come true. Here we thank all of them and wish that you enjoy this release!<br /><br />Best of Joomla! team<br /></td></tr></table>');
DEFINE('_KUNENA_INSTRUCTIONS', 'Instructions');
DEFINE('_KUNENA_FINFO', 'Fireboard Forum Information');
DEFINE('_KUNENA_CSSEDITOR', 'Fireboard Template CSS Editor');
DEFINE('_KUNENA_PATH', 'Path:');
DEFINE('_KUNENA_CSSERROR', 'Please Note:CSS Template file must be Writable to Save Changes.');
// User Management
DEFINE('_KUNENA_FUM', 'จัดการรายละเอียดสมาชิก');
DEFINE('_KUNENA_SORTID', 'เรียงโดย ID');
DEFINE('_KUNENA_SORTMOD', 'เรียงโดยรายชื่อผู้ดูแลบอร์ด');
DEFINE('_KUNENA_SORTNAME', 'เรียงโดยชื่อ');
DEFINE('_KUNENA_VIEW', 'มุมมอง');
DEFINE('_KUNENA_NOUSERSFOUND', 'ไม่มีข้อมูลสำหรับสมาชิกท่านนี้.');
DEFINE('_KUNENA_ADDMOD', 'เพิ่มผู้ดูแลสำหรับ');
DEFINE('_KUNENA_NOMODSAV', 'There are no possible moderators found. Read the \'note\' below.');
DEFINE('_KUNENA_NOTEUS',
    'ข้อแนะนำ (ภาษาอังกฤษ): Only users which have the moderator flag set in their fireboard Profile are shown here. In order to be able to add a moderator give a user a moderator flag, go to <a href="index2.php?option=com_fireboard&task=profiles">User Administration</a> and search for the user you want to make a moderator. Then select his or her profile and update it. The moderator flag can only be set by an site administrator. ');
DEFINE('_KUNENA_PROFFOR', 'รายละเอียดของ');
DEFINE('_KUNENA_GENPROF', 'รายละเอียดโดยทั่วไป');
DEFINE('_KUNENA_PREFVIEW', 'รูปแบบส่วนตัว:');
DEFINE('_KUNENA_PREFOR', 'รูปแบบการเรียงลำดับ:');
DEFINE('_KUNENA_ISMOD', 'ให้สิทธิ์เป็นผู้ดูแล:');
DEFINE('_KUNENA_ISADM', '<strong>ใช่</strong> (กรุณาอย่าแก้ไข, เนื่องจากท่านผู้นี้เป็น (super)administrator)');
DEFINE('_KUNENA_COLOR', 'สี');
DEFINE('_KUNENA_UAVATAR', 'รูปส่วนตัวของสมาชิก:');
DEFINE('_KUNENA_NS', 'ไม่มีรายการที่เลือก');
DEFINE('_KUNENA_DELSIG', ' ฉัน ต้องการลบลายเซ็นต์ทั้งหมดของฉัน');
DEFINE('_KUNENA_DELAV', ' ฉัน ต้องการลบรูปส่วนตัวของฉัน');
DEFINE('_KUNENA_SUBFOR', 'รายละเอียดการบันทึกของ');
DEFINE('_KUNENA_NOSUBS', 'ไม่มีรายการติดตามของผู้ใช้นี้');
// Forum Administration (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_BASICS', 'พื้นฐาน');
DEFINE('_KUNENA_BASICSFORUM', 'แก้ไขรายละเอียดฟอรั่ม');
DEFINE('_KUNENA_PARENT', 'ฟอรั่มย่อย:');
DEFINE('_KUNENA_PARENTDESC',
    'คำแนะนำ: ฟอรั่มย่อยนั้นจะสามารถสร้างขึ้นได้ก็ต่อเมื่อคุณได้สร้างประเภทของฟอรั่มหลักก่อน หลังจากนั้นคุณถึงสามารถเพิ่มฟอรั่มย่อยลงไปได้.');
DEFINE('_KUNENA_BASICSFORUMINFO', 'ตั้งชื่อและรายละเอียดฟอรั่ม');
DEFINE('_KUNENA_NAMEADD', 'ชื่อ:');
DEFINE('_KUNENA_DESCRIPTIONADD', 'รายละเอียด:');
DEFINE('_KUNENA_ADVANCEDDESC', 'ตั้งค่าฟอรั่มขั้นสูง');
DEFINE('_KUNENA_ADVANCEDDESCINFO', 'ระบบป้องกันและสิทธิ์การเข้าใช้');
DEFINE('_KUNENA_LOCKEDDESC', 'เซ็ทค่า &quot;ใช่&quot; เพื่อกำหนดให้ทุกหัวข้อนั้นถูกล็อค โดยสมาชิกไม่สามารถเขียนแสดงความคิดเห็นใหม่ได้ นอกจากผู้ดูแลบอร์ดและผู้ดูแลเวปไซต์เท่านั้น.');
DEFINE('_KUNENA_LOCKED1', 'ล็อคหัวข้อ:');
DEFINE('_KUNENA_PUBACC', 'ระดับการเข้าถึง:');
DEFINE('_KUNENA_PUBACCDESC',
    'To create a Non-Public Forum you can specify the minimum userlevel that can see/enter the forum here.By default the minumum userlevel is set to &quot;Everybody&quot;.<br /><b>Please note</b>: if you restrict access on a whole Category to one or more certain groups, it will hide all Forums it contains to anybody not having proper privileges on the Category <b>even</b> if one or more of these Forums have a lower access level set! This holds for Moderators too; you will have to add a Moderator to the moderator list of the Category if (s)he does not have the proper group level to see the Category.<br /> This is irrespective of the fact that Categories can not be Moderated; Moderators can still be added to the moderator list.');
DEFINE('_KUNENA_CGROUPS', 'เปิดการรวมกลุ่มรอง:');
DEFINE('_KUNENA_CGROUPSDESC', 'Should child groups be allowed access as well? If set to &quot;No&quot; access to this forum is restricted to the selected group <b>only</b>');
DEFINE('_KUNENA_ADMINLEVEL', 'ระดับผู้ดูแล:');
DEFINE('_KUNENA_ADMINLEVELDESC',
    'กำหนดระดับผู้ดูแลบอร์ด สิทธิต่างๆในการจัดการ ระดับการแก้ไข และเข้าถึง ของผู้ดูแลบอร์ดทั้งหมด ยกเว้นผู้ดูแลเวปไซต์.');
DEFINE('_KUNENA_ADVANCED', 'ขั้นสูง');
DEFINE('_KUNENA_CGROUPS1', 'เปิดการรวมกลุ่มรอง:');
DEFINE('_KUNENA_CGROUPS1DESC', 'Should child groups be allowed access as well? If set to &quot;No &quot; access to this forum is restricted to the selected group <b>only</b>');
DEFINE('_KUNENA_REV', 'ตรวจสอบข้อความ::');
DEFINE('_KUNENA_REVDESC',
    'เซ็ทค่า &quot;ใช่&quot; เพื่อให้ทุกข้อความต้องผ่านการกรอง และพิจารณาก่อนถูกบันทึก/เผยแพร่ ก่อนทุกครั้ง');
DEFINE('_KUNENA_MOD_NEW', 'ผู้ดูแลบอร์ด');
DEFINE('_KUNENA_MODNEWDESC', 'ตั้งค่าและจัดการผู้ดูแลบอร์ดของฟอรั่มนี้');
DEFINE('_KUNENA_MOD', 'ผู้ดูแลบอร์ด');
DEFINE('_KUNENA_MODDESC',
    'เซ็ทค่า &quot;ใช่&quot; เพื่อเพิ่มผู้ดูแลบอร์ดสำหรับฟอรั่มนี้.<br /><strong>Note:</strong> This doesn\'t mean that new post must be reviewed prior to publishing them to the forum!<br /> You will need to set the &quot;Review&quot; option for that on the advanced tab.<br /><br /> <strong>ข้อแนะนำ:</strong> หากต้องการแก้ไขหรือเพิ่มผู้ดูแล สามารถแก้ไขได้ที่ ปุ่ม Moderator.');
DEFINE('_KUNENA_MODHEADER', 'ตั้งค่าผู้ดูแลบอร์ดสำหรับฟอรั่มนี้');
DEFINE('_KUNENA_MODSASSIGNED', 'Moderators assigned to this forum:');
DEFINE('_KUNENA_NOMODS', 'There are no Moderators assigned to this forum');
// Some General Strings (Improvement in Fireboard)
DEFINE('_KUNENA_EDIT', 'แก้ไข');
DEFINE('_KUNENA_ADD', 'เพิ่ม');
// Reorder (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_MOVEUP', 'เลื่อนขึ้น');
DEFINE('_KUNENA_MOVEDOWN', 'เลื่อนลง');
// Groups - Integration in Fireboard
DEFINE('_KUNENA_ALLREGISTERED', 'สมาชิกที่ลงทะเบียนทั้งหมด');
DEFINE('_KUNENA_EVERYBODY', 'ทุกๆคน');
// Removal of hardcoded strings in admin panel (Re-integration from Joomlaboard 1.2)
DEFINE('_KUNENA_REORDER', 'นำเข้า');
DEFINE('_KUNENA_CHECKEDOUT', 'นำออก');
DEFINE('_KUNENA_ADMINACCESS', 'การเข้าถึง ผู้ดูแล');
DEFINE('_KUNENA_PUBLICACCESS', 'ระดับการเข้าถึง');
DEFINE('_KUNENA_PUBLISHED', 'เผยแพร่');
DEFINE('_KUNENA_REVIEW', 'พิจารณา');
DEFINE('_KUNENA_MODERATED', 'ผู้ดูแล');
DEFINE('_KUNENA_LOCKED', 'ล็อค');
DEFINE('_KUNENA_CATFOR', 'ประเภท / ฟอรั่ม');
DEFINE('_KUNENA_ADMIN', '&nbsp;ตั้งค่าประเภทฟอรั่ม');
DEFINE('_KUNENA_CP', '&nbsp;(ไฟร์บอร์ด) ตั้งค่าคอนฟิคหลัก');
// Configuration page - Headings (Re-integrated from Joomlaboard 1.2)
DEFINE('_COM_A_AVATAR_INTEGRATION', 'Avatar Integration');
DEFINE('_COM_A_RANKS_SETTINGS', 'ระดับตำแหน่ง');
DEFINE('_COM_A_RANKING_SETTINGS', 'ตั้งค่าระดับ');
DEFINE('_COM_A_AVATAR_SETTINGS', 'ตั้งค่ารูปส่วนตัว');
DEFINE('_COM_A_SECURITY_SETTINGS', 'ตั้งค่าความปลอดภัย');
DEFINE('_COM_A_BASIC_SETTINGS', 'ตั้งค่าพื้นฐาน');
// FIREBOARD 1.0.0
//
DEFINE('_COM_A_FAVORITES', 'เปิดการบันทึกรายการโปรด');
DEFINE('_COM_A_FAVORITES_DESC', 'เซ็ทค่า &quot;ใช่&quot; เพื่อเปิดฟังก์ชั่นบันทึกรายการโปรด ');
DEFINE('_USER_UNFAVORITE_ALL', 'ฉันต้องการ <b><u>ยกเลิกรายการโปรด</u></b> ทั้งหมดออกจากรายการบันทึก');
DEFINE('_VIEW_FAVORITETXT', 'บันทึกหัวข้อนี้');
DEFINE('_USER_UNFAVORITE_YES', 'คุณได้ยกเลิกรายการโปรด สำหรับหัวข้อนี้เรียบร้อยแล้วครับ');
DEFINE('_POST_FAVORITED_TOPIC', 'สถานะการบันทึกรายการของคุณ');
DEFINE('_VIEW_UNFAVORITETXT', 'ยกเลิกรายการโปรด');
DEFINE('_VIEW_UNSUBSCRIBETXT', 'ติดตามหัวข้อนี้');
DEFINE('_USER_NOFAVORITES', 'ยังไม่มีรายการโปรด');
DEFINE('_POST_SUCCESS_FAVORITE', 'คุณได้บันทึกรายการโปรด สำหรับหัวข้อนี้เรียบร้อยแล้วครับ.');
DEFINE('_COM_A_MESSAGES_SEARCH', 'ผลการค้นหาทั้งหมด');
DEFINE('_COM_A_MESSAGES_DESC_SEARCH', 'แสดงรายการผลการค้นหาที่ได้');
DEFINE('_KUNENA_USE_JOOMLA_STYLE', 'ใช้รูปแบบของ"จูมล่า ลายไทย"');
DEFINE('_KUNENA_USE_JOOMLA_STYLE_DESC', 'เลือกใช้จูมล่า ลายไทยเป็นรูปแบบ เลือก ใช่. (class: like sectionheader, sectionentry1 ...) ');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST', 'ใช้รูปภาพแสดงฟอรั่มย่อย');
DEFINE('_KUNENA_SHOW_CHILD_CATEGORY_ON_LIST_DESC', 'ใช้ไอคอนเล็กๆแสดงหน้าฟอรั่มย่อยแต่ละชนิด, เซ็ทค่าเท่ากับ "ใช่". ');
DEFINE('_KUNENA_SHOW_ANNOUNCEMENT', 'แสดงข้อความประกาศ');
DEFINE('_KUNENA_SHOW_ANNOUNCEMENT_DESC', 'เซ็ทค่า "ใช่" , เพื่อแสดงข้อความประกาศจากผู้ดูแลบอร์ด.');
DEFINE('_KUNENA_SHOW_AVATAR_ON_CAT', 'แสดงรูปแสดงแทนประเภทนั้น?');
DEFINE('_KUNENA_SHOW_AVATAR_ON_CAT_DESC', 'ตั้งค่า "ใช่" , หากต้องการใช้รูปแสดงแทนประเภท.');
DEFINE('_KUNENA_RECENT_POSTS', 'ตั้งค่าข้อความเร็วๆนี้');
DEFINE('_KUNENA_SHOW_LATEST_MESSAGES', 'แสดงหัวข้อเร็วๆนี้');
DEFINE('_KUNENA_SHOW_LATEST_MESSAGES_DESC', 'แสดงรายการหัวข้อล่าสุดเร็วๆนี้');
DEFINE('_KUNENA_NUMBER_OF_LATEST_MESSAGES', 'จำนวนหัวข้อล่าสุด');
DEFINE('_KUNENA_NUMBER_OF_LATEST_MESSAGES_DESC', 'จำนวนรายการหัวข้อล่าสุดที่นำมาแสดงในหน้าแรกเวปบอร์ด');
DEFINE('_KUNENA_COUNT_PER_PAGE_LATEST_MESSAGES', 'จำนวนที่แสดง ');
DEFINE('_KUNENA_COUNT_PER_PAGE_LATEST_MESSAGES_DESC', 'จำนวนรายการหัวข้อที่ถูกตอบ ในส่วนแสดงสถานะ');
DEFINE('_KUNENA_LATEST_CATEGORY', 'แสดงประเภท');
DEFINE('_KUNENA_LATEST_CATEGORY_DESC', 'ใส่ค่าประเภทที่ต้องการให้ข้อความล่าสุดแสดงในส่วนแสดงสถานะ. เช่น:2,3,7 ');
DEFINE('_KUNENA_SHOW_LATEST_SINGLE_SUBJECT', 'แสดงกระทู้ลำดับแรก');
DEFINE('_KUNENA_SHOW_LATEST_SINGLE_SUBJECT_DESC', 'แสดงกระทู้หัวข้อที่มาลำดับแรก');
DEFINE('_KUNENA_SHOW_LATEST_REPLY_SUBJECT', 'แสดงหัวข้อที่ถูกตอบกลับ');
DEFINE('_KUNENA_SHOW_LATEST_REPLY_SUBJECT_DESC', 'แสดงรายการหัวข้อที่ถูกตอบกลับ โดยอยู่ในรูปแบบข้อความ (ตอบกลับ:)');
DEFINE('_KUNENA_LATEST_SUBJECT_LENGTH', 'ความยาวหัวข้อ');
DEFINE('_KUNENA_LATEST_SUBJECT_LENGTH_DESC', 'ความยาวสูงสุดในการตั้งหัวข้อ');
DEFINE('_KUNENA_SHOW_LATEST_DATE', 'แสดงวันที่');
DEFINE('_KUNENA_SHOW_LATEST_DATE_DESC', 'แสดงรายการวันที่ที่ถูกตั้งหัวข้อ');
DEFINE('_KUNENA_SHOW_LATEST_HITS', 'แสดงผู้ชม');
DEFINE('_KUNENA_SHOW_LATEST_HITS_DESC', 'แสดงผู้ชมที่รับชมแต่ละหัวข้อ');
DEFINE('_KUNENA_SHOW_AUTHOR', 'แสดงชื่อผู้ตั้งหัวข้อ');
DEFINE('_KUNENA_SHOW_AUTHOR_DESC', '1=username, 2=realname, 0=none');
DEFINE('_KUNENA_STATS', 'สถานะปลั๊กอินเพิ่มเติม ');
DEFINE('_KUNENA_CATIMAGEPATH', 'พาธของรูปภาพแสดง ');
DEFINE('_KUNENA_CATIMAGEPATH_DESC', 'ตำแหน่งพาธรูปภาพ. ตั้งค่า "category_images/"หรือ"components/com_fireboard/category_images/');
DEFINE('_KUNENA_ANN_MODID', 'ลำดับ Moderator(ผู้ดูแล) IDs ');
DEFINE('_KUNENA_ANN_MODID_DESC', 'หากมีการเพิ่มผู้ดูแลให้เริ่มตั้งแต่. e.g. 62,63,73 . ผู้ดูแลสามารถ  add, edit, delete และส่วนอื่นๆได้.');
//
DEFINE('_KUNENA_GO', 'ไปที่ ');
DEFINE('_KUNENA_FORUM_TOP', 'รายการฟอรั่ม ');
DEFINE('_KUNENA_CHILD_BOARDS', 'ฟอรั่มย่อย ');
DEFINE('_KUNENA_QUICKMSG', 'ตอบกลับด่วน ');
DEFINE('_KUNENA_THREADS_IN_FORUM', 'Thread in Forum ');
DEFINE('_KUNENA_FORUM', 'ฟอรั่ม ');
DEFINE('_KUNENA_SPOTS', 'หัวข้อปักหมุด');
DEFINE('_KUNENA_CANCEL', 'ยกเลิก');
DEFINE('_KUNENA_TOPIC', 'หัวข้อ : ');
DEFINE('_KUNENA_EMOTICONS', 'รูปแสดงอารมณ์');
DEFINE('_KUNENA_EMOTICONS_SMILEY', 'Smiley');
DEFINE('_KUNENA_EMOTICONS_CODE', 'รหัส');
DEFINE('_KUNENA_EMOTICONS_URL', 'URL');
DEFINE('_KUNENA_EMOTICONS_EDIT_SMILEY', 'Edit Smiley');
DEFINE('_KUNENA_EMOTICONS_EDIT_SMILIES', 'Edit Smilies');
DEFINE('_KUNENA_EMOTICONS_EMOTICONBAR', 'EmoticonBar');
DEFINE('_KUNENA_EMOTICONS_NEW_SMILEY', 'New Smiley');
DEFINE('_KUNENA_EMOTICONS_MORE_SMILIES', 'รูปแสองอารมณ์เพิ่มเติม');
DEFINE('_KUNENA_EMOTICONS_CLOSE_WINDOW', 'Close Window');
DEFINE('_KUNENA_EMOTICONS_ADDITIONAL_EMOTICONS', 'Additional Emoticons');
DEFINE('_KUNENA_EMOTICONS_PICK_A_SMILEY', 'Pick a smiley');
DEFINE('_KUNENA_MAMBOT_SUPPORT', 'Joomla Mambot Support');
DEFINE('_KUNENA_MAMBOT_SUPPORT_DESC', 'Enable Joomla Mambot Support');
DEFINE('_KUNENA_MYPROFILE_PLUGIN_SETTINGS', 'My Profile Plugin Settings');
DEFINE('_KUNENA_USERNAMECANCHANGE', 'Allow username change');
DEFINE('_KUNENA_USERNAMECANCHANGE_DESC', 'Allow username change on myprofile plugin page');
DEFINE ('_KUNENA_RECOUNTFORUMS','Recount Categories Stats');
DEFINE ('_KUNENA_RECOUNTFORUMS_DONE','All category statistics now are recounted.');
DEFINE ('_KUNENA_EDITING_REASON','Reason for Editing');
DEFINE ('_KUNENA_EDITING_LASTEDIT','Last Edit');
DEFINE ('_KUNENA_BY','By');
DEFINE ('_KUNENA_REASON','Reason');
DEFINE('_GEN_GOTOBOTTOM', 'Go to bottom');
DEFINE('_GEN_GOTOTOP', 'Go to top');
DEFINE('_STAT_USER_INFO', 'User Info');
DEFINE('_USER_SHOWEMAIL', 'Show Email');
DEFINE('_USER_SHOWONLINE', 'Show Online');
DEFINE('_KUNENA_HIDDEN_USERS', 'สมาชิกซ่อน');
// Time Format
DEFINE('_TIME_TODAY', '<b>วันนี้</b> ');
DEFINE('_TIME_YESTERDAY', '<b>เมื่อวานนี้</b> ');
//  STARTS HERE!
DEFINE('_KUNENA_WHO_LATEST_POSTS', 'โพสล่าสุด');
DEFINE('_KUNENA_WHO_WHOISONLINE', 'ออนไลน์ขณะนี้');
DEFINE('_KUNENA_WHO_MAINPAGE', 'หน้าหลักฟอรั่ม');
DEFINE('_KUNENA_GUEST', 'ผู้เยี่ยมชม');
DEFINE('_KUNENA_PATHWAY_VIEWING', 'กำลังดูหน้านี้');
DEFINE('_KUNENA_ATTACH', 'แทรกไฟล์');
// Favorite
DEFINE('_KUNENA_FAVORITE', 'รายการโปรด');
DEFINE('_USER_FAVORITES', 'รายการโปรดของฉัน');
DEFINE('_THREAD_UNFAVORITE', 'ลบรายการโปรด');
// profilebox
DEFINE('_PROFILEBOX_WELCOME', 'ยินดีต้อนรับ');
DEFINE('_PROFILEBOX_SHOW_LATEST_POSTS', 'ดูคำตอบล่าสุด');
DEFINE('_PROFILEBOX_SET_MYAVATAR', 'รูปส่วนตัว');
DEFINE('_PROFILEBOX_MYPROFILE', 'ข้อมูลส่วนตัว');
DEFINE('_PROFILEBOX_SHOW_MYPOSTS', 'แสดงรายการข้อความของคุณ');
DEFINE('_PROFILEBOX_GUEST', 'ผู้เยี่ยมชม');
DEFINE('_PROFILEBOX_LOGIN', 'เข้าสู่ระบบ');
DEFINE('_PROFILEBOX_REGISTER', 'ลงทะเบียน');
DEFINE('_PROFILEBOX_LOGOUT', 'ออกจากระบบ');
DEFINE('_PROFILEBOX_LOST_PASSWORD', 'ลืมรหัสผ่าน?');
DEFINE('_PROFILEBOX_PLEASE', 'กรุณา');
DEFINE('_PROFILEBOX_OR', 'หรือ');
DEFINE('_ANN_ANNOUNCEMENTS', 'ประกาศ');
 
// recentposts
DEFINE('_RECENT_RECENT_POSTS', 'คำตอบล่าสุดเร็วๆนี้');
DEFINE('_RECENT_TOPICS', 'หัวข้อ ');
DEFINE('_RECENT_AUTHOR', 'โดย');
DEFINE('_RECENT_CATEGORIES', 'ประเภท');
DEFINE('_RECENT_DATE', 'วันที่');
DEFINE('_RECENT_HITS', 'ผู้ชม');
// announcement
DEFINE('_ANN_ID', 'ID');
DEFINE('_ANN_DATE', 'วัน');
DEFINE('_ANN_TITLE', 'หัวเรื่อง');
DEFINE('_ANN_SORTTEXT', 'ข้อความสั้น');
DEFINE('_ANN_LONGTEXT', 'ข้อความยาว');
DEFINE('_ANN_ORDER', 'Order');
DEFINE('_ANN_PUBLISH', 'Publish');
DEFINE('_ANN_PUBLISHED', 'Publised');
DEFINE('_ANN_UNPUBLISHED', 'Unpublished');
DEFINE('_ANN_EDIT', 'แก้ไข');
DEFINE('_ANN_DELETE', 'ลบ');
DEFINE('_ANN_SUCCESS', 'Success');
DEFINE('_ANN_SAVE', 'บันทึก');
DEFINE('_ANN_YES', 'ใช่');
DEFINE('_ANN_NO', 'ไม่');
DEFINE('_ANN_ADD', 'เพิ่มใหม่');
DEFINE('_ANN_SUCCESS_EDIT', 'Success Edit');
DEFINE('_ANN_SUCCESS_ADD', 'Success Added');
DEFINE('_ANN_DELETED', 'Success Deleted');
DEFINE('_ANN_ERROR', 'ERROR');
DEFINE('_ANN_READMORE', 'อ่านรายละเอียด...');
DEFINE('_ANN_CPANEL', 'Announcement Control Panel');
DEFINE('_ANN_SHOWDATE', 'Show Date');
// Stats
DEFINE('_STAT_FORUMSTATS', 'สถิติฟอรั่ม');
DEFINE('_STAT_GENERAL_STATS', 'ข้อมูลทั่วไป');
DEFINE('_STAT_TOTAL_USERS', 'สมาชิกทั้งหมด');
DEFINE('_STAT_LATEST_MEMBERS', 'สมาชิกล่าสุด');
DEFINE('_STAT_PROFILE_INFO', 'รายละเอียดฟอรั่ม');
DEFINE('_STAT_TOTAL_MESSAGES', 'ข้อความทั่งหมด');
DEFINE('_STAT_TOTAL_SUBJECTS', 'หัวข้อทั้งหมด');
DEFINE('_STAT_TOTAL_CATEGORIES', 'ประเภททั้งหมด');
DEFINE('_STAT_TOTAL_SECTIONS', 'หมวดทั้งหมด');
DEFINE('_STAT_TODAY_OPEN_THREAD', 'จำนวนผู้ชมวันนี้');
DEFINE('_STAT_YESTERDAY_OPEN_THREAD', 'จำนวนผู้ชมเมื่อวานนี้');
DEFINE('_STAT_TODAY_TOTAL_ANSWER', 'หัวข้อตอบกลับทั้งหมด');
DEFINE('_STAT_YESTERDAY_TOTAL_ANSWER', 'หัวข้อตอบกลับเมื่อวานนี้');
DEFINE('_STAT_VIEW_RECENT_POSTS_ON_FORUM', 'แสดงคำตอบล่าสุด');
DEFINE('_STAT_MORE_ABOUT_STATS', 'รายละเอียดฟอรั่ม');
DEFINE('_STAT_USERLIST', 'สมาชิกทั้งหมด');
DEFINE('_STAT_TEAMLIST', 'กลุ่มผู้ดูแลบอร์ด');
DEFINE('_STATS_FORUM_STATS', 'สถานะฟอรั่ม');
DEFINE('_STAT_POPULAR', 'ยอดนิยม');
DEFINE('_STAT_POPULAR_USER_TMSG', 'Users ( Total Messages) ');
DEFINE('_STAT_POPULAR_USER_KGSG', 'Threads ');
DEFINE('_STAT_POPULAR_USER_GSG', 'Users ( Total Profile Views) ');
//Team List
DEFINE('_MODLIST_STATUS', 'สถานะ');
DEFINE('_MODLIST_USERNAME', 'ยูซเซอร์เนม');
DEFINE('_MODLIST_FORUMS', 'ฟอรั่ม');
DEFINE('_MODLIST_ONLINE', 'สมาชิกที่กำลังออนไลน์');
DEFINE('_MODLIST_OFFLINE', 'สมาชิกที่ไม่ได้ออนไลน์');
// Whoisonline
DEFINE('_WHO_WHOIS_ONLINE', 'สมาชิกที่กำลังออนไลน์');
DEFINE('_WHO_ONLINE_NOW', 'ออนไลน์');
DEFINE('_WHO_ONLINE_MEMBERS', 'สมาชิก');
DEFINE('_WHO_AND', 'และ');
DEFINE('_WHO_ONLINE_GUESTS', 'ผู้เยี่ยมชม');
DEFINE('_WHO_ONLINE_USER', 'สมาชิก');
DEFINE('_WHO_ONLINE_TIME', 'เวลา');
DEFINE('_WHO_ONLINE_FUNC', 'ขณะนี้อยู่ที่');
// Userlist
DEFINE('_USRL_USERLIST', 'รายการสมาชิกทั้งหมดในฟอรั่ม');
DEFINE('_USRL_REGISTERED_USERS', '%s มีจำนวน <b>%d</b> สมาชิกที่ลงทะเบียน');
DEFINE('_USRL_SEARCH_ALERT', 'กรุณาใส่ชื่อสมาชิกที่ต้องการค้นหา!');
DEFINE('_USRL_SEARCH', 'ค้นหาสมาชิก');
DEFINE('_USRL_SEARCH_BUTTON', 'ค้นหา');
DEFINE('_USRL_LIST_ALL', 'สมาชิกทั้งหมด');
DEFINE('_USRL_NAME', 'ชื่อ');
DEFINE('_USRL_USERNAME', 'ยูซเซอร์เนม');
DEFINE('_USRL_EMAIL', 'อีเมล์');
DEFINE('_USRL_USERTYPE', 'กลุ่ม');
DEFINE('_USRL_JOIN_DATE', 'สมัครสมาชิกวันที่');
DEFINE('_USRL_LAST_LOGIN', 'เข้าใช้ล่าสุด');
DEFINE('_USRL_NEVER', 'Never');
DEFINE('_USRL_BLOCK', 'สถานะ');
DEFINE('_USRL_MYPMS2', 'MyPMS');
DEFINE('_USRL_ASC', 'Ascending');
DEFINE('_USRL_DESC', 'Descending');
DEFINE('_USRL_DATE_FORMAT', '%d.%m.%Y');
DEFINE('_USRL_TIME_FORMAT', '%H:%M');
DEFINE('_USRL_PARTYSTAFF', 'Staff');
DEFINE('_USRL_AKOSTAFF', 'AkoStaff');
DEFINE('_USRL_USEREXTENDED', 'Details');
DEFINE('_USRL_COMPROFILER', 'Profile');
DEFINE('_USRL_THUMBNAIL', 'Pic');
DEFINE('_USRL_READON', 'show');
DEFINE('_USRL_MAMBLOG', 'Mamblog');
DEFINE('_USRL_VIEWBLOG', 'View blog');
DEFINE('_USRL_NOBLOG', 'No blog');
DEFINE('_USRL_MYHOMEPAGE', 'MyHompage');
DEFINE('_USRL_MYPMSPRO', 'Clexus PM');
DEFINE('_USRL_MYPMSPRO_ADD_BUDDY', 'Add to your BuddyList');
DEFINE('_USRL_MYPMSPRO_SENDPM', 'Send PM');
DEFINE('_USRL_JIM', 'PM');
DEFINE('_USRL_UDDEIM', 'PM');
DEFINE('_USRL_SEARCHRESULT', 'Searchresult for');
DEFINE('_USRL_STATUS', 'สถานะ');
DEFINE('_USRL_LISTSETTINGS', 'Userlist Settings');
DEFINE('_USRL_ERROR', 'Error');
//removed in 1.1.4 stable
/*
_POST_HTML_ENABLED,_BACK_TO_FORUM,_RESULTS_CATEGORY,_RESULTS_FORUM_NAME,_RESULTS_CONTENT,
_RESULTS_TITLE,_SEARCH_HITS,_SEARCH_RESULTS,_DESCRIPTION_BOLD,_DESCRIPTION_ITALIC,_DESCRIPTION_U,
_DESCRIPTION_QUOTE,_DESCRIPTION_URL,_DESCRIPTION_CODE,_DESCRIPTION_IMAGE,_DESCRIPTION_LIST,_DESCRIPTION_SIZE
_DESCRIPTION_RED,_DESCRIPTION_BLUE,_DESCRIPTION_GREEN,_DESCRIPTION_YELLOW,_DESCRIPTION_ORANGE,
_DESCRIPTION_PURPLE,_DESCRIPTION_NAVY,_DESCRIPTION_DARKGREEN,_DESCRIPTION_AQUA,_DESCRIPTION_MAGENTA,
_HTML_NO,_POST_FORUM,_POST_NO_PUBACCES1_,_USAGE_BOARDCODE,_USAGE_INSTRUCTIONS,_USAGE_MYPROFILE,
_USAGE_PREVIOUS,_USAGE_TEXT,_USAGE_TEXT_BOLD,_USAGE_TEXT_ITALIC,_USAGE_TEXT_QUOTE,_USAGE_TEXT_UNDERLINE,
_USAGE_TEXT_WILL,_VIEW_LOCKED,_VIEW_EDITOR,_SEARCH_HEADER,_POST_TO_VIEW,_POST_POSTED,_POST_BOARDCODE,
_POST_BBCODE_HELP,_POST_ERROR_EXIT,_POST_EDIT_MESSAGE,_MODERATION_DELETE_REPLIES,_MODERATION_DELETE_POST,
_MODERATION_ERROR_MESSAGE,_SEARCH_ON_USER,_SEARCH_OTHER_OPTIONS,_FORUM_USERSEARCH,_RESULTS_USERNAME,
_UPLOAD_UPDATED,_GEN_TODAYS_POSTS,_GEN_UNANSWERED,_GEN_USAGE,_GEN_VIEWS,_GEN_STARTED,_GEN_POST_A_PROFILE,
_GEN_MY_PROFILE,_GEN_NEW,_GEN_NO_NEW,_GEN_NO_ACCESS,_GEN_POSTS,_GEN_POSTS_DISPLAY,_GEN_CATEGORY,
_GEN_EDIT_MESSAGE,_COM_A_HEADER,_COM_A_HEADER_DESC,_COM_A_CONFIG_DESC,_FILE_INSERT,_FILE_COPY_PASTE,
_FILE_BUTTON,_FILE_UPLOAD,_FILE_SUBMIT,_FILE_ERROR_NAME,_FILE_ERROR_EXISTS,_FILE_UPLOADED,_FILE_UPDATED,
_IMAGE_SUBMIT,_IMAGE_ERROR_NAME,_IMAGE_UPDATED,_IMAGE_COPY_PASTE,_COM_A_IMAGES,_IMAGE_INSERT,_IMAGE_BUTTON,
_IMAGE_ERROR_EXISTS,_COM_A_FILES,_GEN_SUBSCRIPTIONS,_POST_SUCCESS_THREAD_VIEW,_COM_C_BOARDSTATS
*/
//changed in 1.1.4 stable
DEFINE('_COM_A_PMS_TITLE', 'Private messaging component');
DEFINE('_COM_A_COMBUILDER_TITLE', 'Community Builder');
DEFINE('_FORUM_SEARCH', 'ผลจากการค้นหาจากคำว่า: %s');
DEFINE('_MODERATION_DELETE_MESSAGE', 'Are you sure you want to delete this message? \n\n NOTE: There is NO way to retrieve deleted messages!');
DEFINE('_MODERATION_DELETE_SUCCESS', 'The post(s) have been deleted');
DEFINE('_COM_A_KUNENA_BY', 'Joomla! Forum Component by');
DEFINE('_COM_A_RANKING', 'ตำแหน่ง,ยศ');
DEFINE('_COM_A_BOT_REFERENCE', 'Show Bot Reference Chart');
DEFINE('_COM_A_MOSBOT', 'Enable the Discuss Bot');
DEFINE('_PREVIEW', 'ดูตัวอย่าง');
DEFINE('_COM_C_UPGRADE', 'Upgrade Database To: ');
DEFINE('_COM_A_MOSBOT_TITLE', 'Discussbot');
DEFINE('_COM_A_MOSBOT_DESC', 'The Discuss Bot enables your users to discuss content items in the forums. The Content Title is used as the topic subject.'
           . '<br />If a topic does not exist yet a new one is created. If the topic already exists, the user is shown the thread and (s)he can reply.' . '<br /><strong>You will need to download and install the Bot separately.</strong>'
           . '<br />check the <a href="http://www.bestofjoomla.com">Best of Joomla Site</a> for more information.' . '<br />When Installed you will need to add the following bot lines to your Content:' . '<br />{mos_KUNENA_discuss:<em>catid</em>}'
           . '<br />The <em>catid</em> is the category in which the Content Item can be discussed. To find the proper catid, just look into the forums ' . 'and check the category id from the URLs from your browsers status bar.'
           . '<br />Example: if you want the article discussed in Forum with catid 26, the Bot should look like: {mos_KUNENA_discuss:26}'
           . '<br />This seems a bit difficult, but it does allow you to have each Content Item to be discussed in a matching forum.');
//new in 1.1.4 stable
// search.class.php
DEFINE('_FORUM_SEARCHTITLE', 'ค้นหา');
DEFINE('_FORUM_SEARCHRESULTS', 'แสดง %s จาก %s ผลลัพธ์.');
// Help, FAQ
DEFINE('_COM_FORUM_HELP', 'ช่วยเหลือ');
// rules.php
DEFINE('_COM_FORUM_RULES', 'กฎระเบียบ');
DEFINE('_COM_FORUM_RULES_DESC', '<br><ul><h4>กฏและข้อปฏิบัติในการใช้เว็บบอร์ดนี้</h4>
<p > 1. ห้ามมีข้อความและเนื้อหาที่ทำให้สถาบันชาติ ศาสนา พระมหากษัตริย์และพระบรมวงศานุวงศ์เสื่อมเสีย ไม่ว่าจะเป็นทางข้อความ ทางภาพ ทางเสียง หรือสื่อต่างๆ ที่อินเตอร์เน็ตจะพึงกระทำออกมาได้</p>
<p > 2. พูดด้วยความสุภาพ มีความเป็นปัญญาชน เป็นผู้มีการศึกษา ห้ามมีข้อความและเนื้อหาที่ทำให้ผู้อื่นนั้นเสียหาย รำคาญใจ หรือก่อเกิดความรู้สึกไม่ดีต่อผู้อื่น ไม่ว่าจะเกิดด้วยความตั้งใจหรือไม่ </p>
<p > 3. ห้ามมีข้อความที่ยุยงให้ผู้อื่นเกิดความขัดแย้งซึ่งกันและกันไม่ว่าผู้ตั้งกระทู้หรือผู้ตอบนั้นจะตั้งใจหรือไม่</p>
<p > 4. ห้ามมีข้อความ รวมถึงการโพสต์รูปภาพ ที่ส่อไปในเรื่องเพศ ลามกอนาจาร หรือขัดต่อศีลธรรมอันดีของไทย </p>
<p > 5. ห้ามมีข้อความที่ไม่ก่อให้เกิดประโยชน์แก่ผู้อื่น หรือข้อความที่ไม่ตรงประเด็น หรือข้อความที่ซ้ำๆ ในกระทู้เดียวกันหรือหลายกระทู้ ทั้งนี้ขึ้นอยู่กับความเหมาะสม เจตนาของผู้ตั้งกระทู้หรือผู้ตอบ และสถานการณ์ในกระทู้นั้น</p>
<p > 6. ห้ามมีข้อความหรือกระทู้ที่ส่อให้เห็นถึงเจตนาในการพนันต่างๆ ไม่ว่าจะวิธีใดก็ตาม</p>
<p > 7. ห้ามโฆษณาขายสินค้าอื่นๆ</p>
<p > 8. ตั้งกระทู้ข้อความให้อยู่ถูกที่ ถูกระเบียบตามหมวดที่ได้แบ่งไว้ให้ หากไม่แน่ใจสามารถสอบถามผ่านทางข้อความส่วนตัวไปยังผู้ดูแล </p><br>');
//smile.class.php
DEFINE('_COM_BOARDCODE', 'ลักษณะเพิ่มเติม');
// moderate_messages.php
DEFINE('_MODERATION_APPROVE_SUCCESS', 'ข้อความได้ถูกอนุมัติแล้ว');
DEFINE('_MODERATION_DELETE_ERROR', 'เกิดข้อผิดพลาด: ไม่สามารถลบข้อความนี้ได้');
DEFINE('_MODERATION_APPROVE_ERROR', 'เกิดข้อผิดพลาด: ไม่สามารถอนุมัติข้อความนี้ได้');
// listcat.php
DEFINE('_GEN_NOFORUMS', 'ไม่มีรายการฟอรั่มในประเภทนี้!');
//new in 1.1.3 stable
DEFINE('_POST_GHOST_FAILED', 'Failed to create ghost topic in old forum!');
DEFINE('_POST_MOVE_GHOST', 'สำรองหัวข้อไว้ประเภทเดิม');
//new in 1.1 Stable
DEFINE('_GEN_FORUM_JUMP', 'ข้ามไปยังฟอรั่มอื่น');
DEFINE('_COM_A_FORUM_JUMP', 'เปิดการข้ามไปยังฟอรั่มอื่น');
DEFINE('_COM_A_FORUM_JUMP_DESC', 'ตั้งค่า &quot;ใช่&quot;เพื่อเลือกฟังก์ชั่นการข้ามไปยังรายการฟอรั่มอื่นๆ.');
//new in 1.1 RC1
DEFINE('_GEN_RULES', 'กฎระเบียบ');
DEFINE('_COM_A_RULESPAGE', 'เปิดใช้ข้อตกลงการใช้งาน');
DEFINE('_COM_A_RULESPAGE_DESC',
    'If set to &quot;Yes&quot; a link in the header menu will be shown to your Rules page. This page can be used for things like your forum rules etcetera. You can alter the contents of this file to your liking by opening the rules.php in /joomla_root/components/com_fireboard. <em>Make sure you always have a backup of this file as it will be overwritten when upgrading!</em>');
DEFINE('_MOVED_TOPIC', 'ย้าย:');
DEFINE('_COM_A_PDF', 'ยินยอมการสร้างเอกสาร PDF');
DEFINE('_COM_A_PDF_DESC',
    'Set to &quot;Yes&quot; if you would like to enable users to download a simple PDF document with the contents of a thread.<br />It is a <u>simple</u> PDF document; no mark up, no fancy layout and such but it does contain all the text from the thread.');
DEFINE('_GEN_PDFA', 'Click this button to create a PDF document from this thread (opens in a new window).');
DEFINE('_GEN_PDF', 'Pdf');
//new in 1.0.4 stable
DEFINE('_VIEW_PROFILE', 'Click here to see the profile of this user');
DEFINE('_VIEW_ADDBUDDY', 'Click here to add this user to your buddy list');
DEFINE('_POST_SUCCESS_POSTED', 'ข้อความของคุณได้ถูกเผยแพร่เรียบร้อยแล้วครับ');
DEFINE('_POST_SUCCESS_VIEW', '[ กลับไปที่กระทู้ของคุณ ]');
DEFINE('_POST_SUCCESS_FORUM', '[ กลับไปยังฟอรั่ม ]');
DEFINE('_RANK_ADMINISTRATOR', 'ผู้ดูแลระบบ');
DEFINE('_RANK_MODERATOR', 'ผู้ดูแลบอร์ด');
DEFINE('_SHOW_LASTVISIT', 'กระทู้ล่าสุด');
DEFINE('_COM_A_BADWORDS_TITLE', 'Bad Words filtering');
DEFINE('_COM_A_BADWORDS', 'Use bad words filtering');
DEFINE('_COM_A_BADWORDS_DESC', 'Set to &quot;Yes&quot; if you want to filter posts containing the words you defined in the Badword Component config. To use this function you must have Badword Component installed!');
DEFINE('_COM_A_BADWORDS_NOTICE', '* This message has been censored because it contained one or more words set by the administrator *');
DEFINE('_COM_A_COMBUILDER_PROFILE', 'Create Community Builder forum profile');
DEFINE('_COM_A_COMBUILDER_PROFILE_DESC',
    'Click the link to create necessary Forum fields in Community Builder user profile. After they are created you are free to move them whenever you want using the Community Builder admin, just do not rename their names or options. If you delete them from the Community Builder admin, you can create them again using this link, otherwise do not click on the link multiple times!');
DEFINE('_COM_A_COMBUILDER_PROFILE_CLICK', '> คลิ๊กที่นี่ <');
DEFINE('_COM_A_COMBUILDER', 'Community Builder user profiles');
DEFINE('_COM_A_COMBUILDER_DESC',
    'Setting to &quot;Yes&quot; will activate the integration with Community Builder component (www.joomlapolis.com). All Fireboard user profile functions will be handled by the Community Builder and the profile link in the forums will take you to the Community Builder user profile. This setting will override the Clexus PM profile setting if both are set to &quot;Yes&quot;. Also, make sure you apply the required changes in the Community Builder database by using the option below.');
DEFINE('_COM_A_AVATAR_SRC', 'Use avatar picture from');
DEFINE('_COM_A_AVATAR_SRC_DESC',
    'If you have Clexus PM or Community Builder component installed, you can configure Fireboard to use the user avatar picture from Clexus PM or Community Builder user profile. NOTE: For Community Builder you need to have thumbnail option turned on because forum uses thumbnail user pictures, not the originals.');
DEFINE('_COM_A_KARMA', 'แสดง พลังน้ำใจ ของสมาชิก');
DEFINE('_COM_A_KARMA_DESC', 'ตั้งค่า &quot;ใช่&quot; แสดงพลังน้ำใจของสมาชิก และ ปุ่ม(เพิ่ม /ลด) พลังน้ำใจของสมาชิก');
DEFINE('_COM_A_DISEMOTICONS', 'ยกเลิกการใช้สัญลักษณ์แสดงอารมณ์');
DEFINE('_COM_A_DISEMOTICONS_DESC', 'เซ็ทค่า &quot;ใช่&quot; หากต้องการเปิดใช้สัญลักษณ์แสดงอารมณ์ (อีโมติคอน).');
DEFINE('_COM_C_FBCONFIG', 'ตั้งค่า<br/>คอนฟิคหลัก');
DEFINE('_COM_C_FBCONFIGDESC', 'ตั้งค่าคอนฟิคหลัก\'s ทั้งหมดของเวปบอร์ด');
DEFINE('_COM_C_FORUM', 'จัดการ<br/>รายการฟอรั่ม');
DEFINE('_COM_C_FORUMDESC', 'เพิ่มฟอรั่มและรายการประเภท');
DEFINE('_COM_C_USER', 'จัดการ<br/>สมาชิกทั้งหมด');
DEFINE('_COM_C_USERDESC', 'จัดการรายละเอียดของสมาชิกทั้งหมด');
DEFINE('_COM_C_FILES', 'จัดการ<br/>ไฟล์ทั้งหมด');
DEFINE('_COM_C_FILESDESC', 'รายการไฟล์ที่ถูกอัพโหลดเข้ามา');
DEFINE('_COM_C_IMAGES', 'จัดการ<br/>รูปภาพ');
DEFINE('_COM_C_IMAGESDESC', 'รายการรูปที่ถูกอัพโหลดเข้ามา');
DEFINE('_COM_C_CSS', 'แก้ไข<br/>CSS File');
DEFINE('_COM_C_CSSDESC', 'แก้ไข CSS');
DEFINE('_COM_C_SUPPORT', 'Support<br/>WebSite');
DEFINE('_COM_C_SUPPORTDESC', 'Connect to the Best of Joomla website (new window)');
DEFINE('_COM_C_PRUNETAB', 'จัดการ<br/>ข้อมูลฟอรั่ม');
DEFINE('_COM_C_PRUNETABDESC', 'ล้างรายการหัวข้อที่ไม่ได้ถูกใช้งาน');
DEFINE('_COM_C_PRUNEUSERS', 'จัดการ<br/>ข้อมูลสมาชิก');
DEFINE('_COM_C_PRUNEUSERSDESC', 'จัดการข้อมูลสมาชิกทั้งหมด');
DEFINE('_COM_C_LOADSAMPLE', 'โหลด<br/>ข้อมูลตัวอย่าง');
DEFINE('_COM_C_LOADSAMPLEDESC', 'โหลดข้อมูลตัวอย่างมาใช้งาน');
DEFINE('_COM_C_REMOVESAMPLE', 'ลบข้อมูลตัวอย่าง');
DEFINE('_COM_C_REMOVESAMPLEDESC', 'ลบข้อมูลตัวอย่าง');
DEFINE('_KUNENA_CONFIRM_REMOVESAMPLEDATA', 'คุณต้องการที่จะลบข้อมูลตัวอย่าง กด OK');


DEFINE('_COM_C_LOADMODPOS', 'Load Module Positions');
DEFINE('_COM_C_LOADMODPOSDESC', 'Load Module Positions for FireBoard Template');
DEFINE('_COM_C_UPGRADEDESC', 'Get your database up to the latest version after an upgrade');
DEFINE('_COM_C_BACK', 'กลับไปยังหน้าคอนฟิกหลัก');
DEFINE('_SHOW_LAST_SINCE', 'กระทู้ที่มีผู้ชมดูครั้งสุดท้าย :');
DEFINE('_POST_SUCCESS_REQUEST2', 'บันทึกรายการเรียบร้อยแล้ว');
DEFINE('_POST_NO_PUBACCESS3', 'ลงทะเบียน.');
//==================================================================================================
//Changed in 1.0.4
//please update your local language file with these changes as well
DEFINE('_POST_SUCCESS_DELETE', 'ข้อความของคุณได้ถูกลบเรียบร้อยแล้วครับ.');
DEFINE('_POST_SUCCESS_EDIT', 'ข้อความของคุณได้ถูกแก้ไขเรียบร้อยแล้วครับ.');
DEFINE('_POST_SUCCESS_MOVE', 'หัวข้อของคุณได้ถูกเลคื่อนย้ายเรียบร้อยแล้วครับ.');
DEFINE('_POST_SUCCESS_POST', 'ข้อความของคุณได้ถูกเผยแพร่เรียบร้อยแล้วครับ.');
DEFINE('_POST_SUCCESS_SUBSCRIBE', 'คุณได้บันทึกการติดตามหัวข้อของคุณเรียบร้อยแล้วครับ');
//==================================================================================================
//new in 1.0.3 stable
//Karma
DEFINE('_KARMA', 'พลังน้ำใจ');
DEFINE('_KARMA_SMITE', 'Smite');
DEFINE('_KARMA_APPLAUD', 'Applaud');
DEFINE('_KARMA_BACK', 'To get back to the topic,');
DEFINE('_KARMA_WAIT', 'You can modify only one person\'s karma every 6 hours. <br/>Please wait untill this timeout period has passed before modifying any person\'s karma again.');
DEFINE('_KARMA_SELF_DECREASE', 'Please do not attempt to decrease your own karma!');
DEFINE('_KARMA_SELF_INCREASE', 'Your karma has been decreased for attempting to increase it yourself!');
DEFINE('_KARMA_DECREASED', 'User\'s karma decreased. If you are not taken back to the topic in a few moments,');
DEFINE('_KARMA_INCREASED', 'User\'s karma increased. If you are not taken back to the topic in a few moments,');
DEFINE('_COM_A_TEMPLATE', 'เทมเพลท');
DEFINE('_COM_A_TEMPLATE_DESC', 'เลือกเทมเพลทของคุณ.');
DEFINE('_COM_A_TEMPLATE_IMAGE_PATH', 'ไอคอนเทมเพลท');
DEFINE('_COM_A_TEMPLATE_IMAGE_PATH_DESC', 'เลือกไอคอนเทมเพลทของคุณ.');
DEFINE('_PREVIEW_CLOSE', 'ออกหน้าต่างนี้');
//==========================================
//new in 1.0 Stable
DEFINE('_GEN_PATHWAY', 'คุณอยู่ที่นี่ :: ');
DEFINE('_COM_A_POSTSTATSBAR', 'แสดงสถานะรายการกระทู้ของสมาชิก');
DEFINE('_COM_A_POSTSTATSBAR_DESC', 'ตั้งค่า &quot;ใช่&quot; หากคุณต้องการให้แสดงสถานะรายการกระทู้ของสมาชิกที่แทบสถานะ.');
DEFINE('_COM_A_POSTSTATSCOLOR', 'แสดงสีสำหรับสถานะเวปบอร์ด');
DEFINE('_COM_A_POSTSTATSCOLOR_DESC', 'รายการสีแสดงสถานะของเวปบอร์ดทั้งหมด');
DEFINE('_LATEST_REDIRECT','');
DEFINE('_SMILE_COLOUR', 'สี');
DEFINE('_SMILE_SIZE', 'ขนาด');
DEFINE('_COLOUR_DEFAULT', 'มารตฐาน');
DEFINE('_COLOUR_RED', 'Red');
DEFINE('_COLOUR_PURPLE', 'Purple');
DEFINE('_COLOUR_BLUE', 'Blue');
DEFINE('_COLOUR_GREEN', 'Green');
DEFINE('_COLOUR_YELLOW', 'Yellow');
DEFINE('_COLOUR_ORANGE', 'Orange');
DEFINE('_COLOUR_DARKBLUE', 'Darkblue');
DEFINE('_COLOUR_BROWN', 'Brown');
DEFINE('_COLOUR_GOLD', 'Gold');
DEFINE('_COLOUR_SILVER', 'Silver');
DEFINE('_SIZE_NORMAL', 'ปกติ');
DEFINE('_SIZE_SMALL', 'เล็ก');
DEFINE('_SIZE_VSMALL', 'เล็กที่สุด');
DEFINE('_SIZE_BIG', 'ใหญ่');
DEFINE('_SIZE_VBIG', 'ใหญ่มาก');
DEFINE('_IMAGE_SELECT_FILE', 'แทรกรูปภาพ');
DEFINE('_FILE_SELECT_FILE', 'แทรกไฟล์');
DEFINE('_FILE_NOT_UPLOADED', 'Your file has not been uploaded. Try posting again or editing the post');
DEFINE('_IMAGE_NOT_UPLOADED', 'Your image has not been uploaded. Try posting again or editing the post');
DEFINE('_BBCODE_IMGPH', 'Insert [img] placeholder in the post for attached image');
DEFINE('_BBCODE_FILEPH', 'Insert [file] placeholder in the post for attached file');
DEFINE('_POST_ATTACH_IMAGE', '[img]');
DEFINE('_POST_ATTACH_FILE', '[file]');
DEFINE('_USER_UNSUBSCRIBE_ALL', 'ฉันต้องการ <b><u>ยกเลิกการติดตาม</u></b> กระทู้ทั้งหมดในรายการ');
DEFINE('_LINK_JS_REMOVED', '<em>Active link containing JavaScript has been removed automatically</em>');
//==========================================
//new in 1.0 RC4
DEFINE('_COM_A_LOOKS', 'ตั้งค่ารูปแบบของฟอรั่ม');
DEFINE('_COM_A_USERS', 'เกี่ยวกับสมาชิก');
DEFINE('_COM_A_LENGTHS', 'ตั้งค่าระยะ');
DEFINE('_COM_A_SUBJECTLENGTH', 'จำนวนสูงสุด.ในการตั้งหัวข้อ');
DEFINE('_COM_A_SUBJECTLENGTH_DESC',
    'จำนวนสูงสุดในการตั้งหัวข้อนั้น ผู้ดูแลสามารถตั้งได้ไม่เกิน 100 ตัวอักษรโดยเฉลี่ย ซึ่งขึ้นอยู่กับการวางรูปแบบเทมเพลทของท่าน.');
DEFINE('_LATEST_THREADFORUM', 'หัวข้อ/ฟอรั่ม');
DEFINE('_LATEST_NUMBER', 'กระทู้ใหม่');
DEFINE('_COM_A_SHOWNEW', 'แสดงรายการมาใหม่');
DEFINE('_COM_A_SHOWNEW_DESC', 'เซ็ทค่า &quot;ใช่&quot; เพื่อใช้แสดงรายการมาใหม่.');
DEFINE('_COM_A_NEWCHAR', '&quot;New&quot; การบ่งชี้');
DEFINE('_COM_A_NEWCHAR_DESC', 'ใช้ข้อความแสดงหัวข้อใหม่ (เช่น &quot;!&quot; หรือ &quot;ใหม่!&quot;)');
DEFINE('_LATEST_AUTHOR', 'กระทู้ล่าสุดโดย');
DEFINE('_GEN_FORUM_NEWPOST', 'หัวข้อใหม่');
DEFINE('_GEN_FORUM_NOTNEW', 'ไม่มีหัวข้อใหม่');
DEFINE('_GEN_UNREAD', 'หัวข้อที่ยังไม่ได้ถูกอ่าน');
DEFINE('_GEN_NOUNREAD', 'หัวข้อที่ถูกอ่านแล้ว');
DEFINE('_GEN_MARK_ALL_FORUMS_READ', 'มาร์คข้อความทั้งหมดว่าอ่านแล้ว');
DEFINE('_GEN_MARK_THIS_FORUM_READ', 'มาร์คหัวข้อนี้ว่าอ่านแล้ว');
DEFINE('_GEN_FORUM_MARKED', 'All posts in this forum have been marked as read');
DEFINE('_GEN_ALL_MARKED', 'All posts have been marked as read');
DEFINE('_IMAGE_UPLOAD', 'Image Upload');
DEFINE('_IMAGE_DIMENSIONS', 'Your image file can be maximum (width x height - size)');
DEFINE('_IMAGE_ERROR_TYPE', 'Please use only jpeg, gif or png images');
DEFINE('_IMAGE_ERROR_EMPTY', 'Please select a file before uploading');
DEFINE('_IMAGE_ERROR_SIZE', 'The image file size exceeds the maximum set by the Administrator.');
DEFINE('_IMAGE_ERROR_WIDTH', 'The image file width exceeds the maximum set by the Administrator.');
DEFINE('_IMAGE_ERROR_HEIGHT', 'The image file height exceeds the maximum set by the Administrator.');
DEFINE('_IMAGE_UPLOADED', 'Your Image has been uploaded.');
DEFINE('_COM_A_IMAGE', 'จัดการรูปภาพ');
DEFINE('_COM_A_IMGHEIGHT', 'สูงสุด. ความสูงของภาพ');
DEFINE('_COM_A_IMGWIDTH', 'สูงสุด. ความยาวของภาพ');
DEFINE('_COM_A_IMGSIZE', 'สูงสุด. ขนาดของภาพ<br/><em>หน่วยเป็นกิโลไบต์</em>');
DEFINE('_COM_A_IMAGEUPLOAD', 'ผู้เยี่ยมชมอัพโหลดภาพได้');
DEFINE('_COM_A_IMAGEUPLOAD_DESC', 'เซ็ทค่า &quot;ใช่&quot; ผู้เยี่ยมชมสามารถอัพโหลดภาพได้ เหมือนสมาชิกปกติ.');
DEFINE('_COM_A_IMAGEREGUPLOAD', 'สมาชิกสามารถอัพโหลดภาพได้');
DEFINE('_COM_A_IMAGEREGUPLOAD_DESC', 'เซ็ทค่า &quot;ใช่&quot; สมาชิกสามารถอัพโหลดไฟล์ภาพของตัวเองได้.');
//New since preRC4-II:
DEFINE('_COM_A_UPLOADS', 'อัพโหลด');
DEFINE('_FILE_TYPES', 'Your file can be of type - max. size');
DEFINE('_FILE_ERROR_TYPE', 'You are only allowed to upload files of type:\n');
DEFINE('_FILE_ERROR_EMPTY', 'Please select a file before uploading');
DEFINE('_FILE_ERROR_SIZE', 'The file size exceeds the maximum set by the Administrator.');
DEFINE('_COM_A_FILE', 'จัดการไฟล์');
DEFINE('_COM_A_FILEALLOWEDTYPES', 'นามสกุลของไฟล์');
DEFINE('_COM_A_FILEALLOWEDTYPES_DESC', 'ตังค่าการอัพโหลด โดยกรองนามสกุลของไฟล์แต่ละชนิด.<br />ตัวอย่าง: zip,txt,exe,htm,html');
DEFINE('_COM_A_FILESIZE', 'สูงสุด. ขนาดไฟล์<br/><em>หน่วยเป็นกิโลไบต์</em>');
DEFINE('_COM_A_FILEUPLOAD', 'ผู้เยี่ยมชมสามารถอัพโหลดไฟล์ได้');
DEFINE('_COM_A_FILEUPLOAD_DESC', 'เซ็ทค่า &quot;ใช่&quot; ผู้เยี่ยมชมสามารถอัพโหลดไฟล์ได้ เหมือนสมาชิกปกติ.');
DEFINE('_COM_A_FILEREGUPLOAD', 'สมาชิกสามารถอัพโหลดไฟล์ได้');
DEFINE('_COM_A_FILEREGUPLOAD_DESC', 'เซ็ทค่า &quot;ใช่&quot; สมาชิกสามารถอัพโหลดไฟล์ของตัวเองได้.');
DEFINE('_SUBMIT_CANCEL', 'Your post submission has been cancelled');
DEFINE('_HELP_SUBMIT', 'Click here to submit your message');
DEFINE('_HELP_PREVIEW', 'Click here to see what your message will look like when submitted');
DEFINE('_HELP_CANCEL', 'Click here to cancel your message');
DEFINE('_POST_DELETE_ATT', 'If this box is checked, all image and file attachments of posts you are going to delete will be deleted as well (recommended).');
//new since preRC4-III
DEFINE('_COM_A_USER_MARKUP', 'แสดงบันทึกการแก้ไข');
DEFINE('_COM_A_USER_MARKUP_DESC', 'เซ็ทค่า &quot;ใช่&quot; เพื่อแสดงข้อความบันทึกการแก้ไขของคุณ.');
DEFINE('_EDIT_BY', 'Post edited by:');
DEFINE('_EDIT_AT', 'at:');
DEFINE('_UPLOAD_ERROR_GENERAL', 'An error occured when uploading your Avatar. Please try again or notify your system administrator');
DEFINE('_COM_A_IMGB_IMG_BROWSE', 'จัดการไฟล์รูปภาพทั้งหมด');
DEFINE('_COM_A_IMGB_FILE_BROWSE', 'จัดการไฟล์ข้อมูลทั้งหมด');
DEFINE('_COM_A_IMGB_TOTAL_IMG', 'จำนวนรูปภาพทั้งหมดในตอนนี้');
DEFINE('_COM_A_IMGB_TOTAL_FILES', 'จำนวนไฟล์ทั้งหมดในตอนนี้');
DEFINE('_COM_A_IMGB_ENLARGE', 'คลิกเพื่อดูรูปขนาดจริง');
DEFINE('_COM_A_IMGB_DOWNLOAD', 'คลิกที่ไฟล์เพื่อดาว์นโหลด');
DEFINE('_COM_A_IMGB_DUMMY_DESC',
    'The option &quot;Replace with dummy&quot; will replace the selected image with a dummy image.<br /> This allows you to remove the actual file without breaking the post.<br /><small><em>Please note that sometimes an explicit browser refresh is needed to see the dummy replacement.</em></small>');
DEFINE('_COM_A_IMGB_DUMMY', 'รูปภาพปัจจุบัน');
DEFINE('_COM_A_IMGB_REPLACE', 'แก้ไขรูป');
DEFINE('_COM_A_IMGB_REMOVE', 'ลบไฟล์นี้');
DEFINE('_COM_A_IMGB_NAME', 'ชื่อ');
DEFINE('_COM_A_IMGB_SIZE', 'ขนาด');
DEFINE('_COM_A_IMGB_DIMS', 'ขนาดรูป');
DEFINE('_COM_A_IMGB_CONFIRM', 'คุณแน่ใจที่จะลบไฟล์นี้? \n หากคุณลบไฟล์จะมีผลกระทบต่อกระทู้ดังกล่าว...');
DEFINE('_COM_A_IMGB_VIEW', 'เปิดและแก้ไข');
DEFINE('_COM_A_IMGB_NO_POST', 'ไม่สามารถแก้ไขได้');
DEFINE('_USER_CHANGE_VIEW', 'การตั้งค่ารูปแบบข้อความนี้จะมีผลต่อการเรียงลำดับกระท - หลังของหัวข้อนั้นๆ.<br /> If you want to change the view type &quot;Mid-Flight&quot; you can use the options from the forum menu bar.');
DEFINE('_MOSBOT_DISCUSS_A', 'Discuss this article on the forums. (');
DEFINE('_MOSBOT_DISCUSS_B', ' posts)');
DEFINE('_POST_DISCUSS', 'This thread discusses the Content article');
DEFINE('_COM_A_RSS', 'ยินยอม RSS feed');
DEFINE('_COM_A_RSS_DESC', 'The RSS feed enables users to download the latest posts to their desktop/RSS Reader aplication (see <a href="http://www.rssreader.com" target="_blank">rssreader.com</a> for an example.');
DEFINE('_LISTCAT_RSS', 'get the latest posts directly to your desktop');

//====================
//admin.forum.html.php
DEFINE('_COM_A_CONFIG', 'ตั้งค่า คอนฟิคหลัก');
DEFINE('_COM_A_VERSION', 'Your version is ');
DEFINE('_COM_A_DISPLAY', 'รูปแบบ #');
DEFINE('_COM_A_CURRENT_SETTINGS', 'ค่าปัจจุบัน');
DEFINE('_COM_A_EXPLANATION', 'คำอธิบาย');
DEFINE('_COM_A_BOARD_TITLE', 'ชื่อบอร์ด');
DEFINE('_COM_A_BOARD_TITLE_DESC', 'ชื่อของเวปบอร์ดคุณ');
DEFINE('_COM_A_VIEW_TYPE', 'มุมมองปัจจุบัน');
DEFINE('_COM_A_VIEW_TYPE_DESC', 'รูปแบบมุมมองของเวปบอร์ด กรุณาใช้ค่าดั้งเดิมเพื่อความสะดวกของสมาชิก');
DEFINE('_COM_A_THREADS', 'จำนวนหัวข้อต่อหน้า');
DEFINE('_COM_A_THREADS_DESC', 'แสดงรายการหัวข้อในหน้าแรกของบอร์ด');
DEFINE('_COM_A_REGISTERED_ONLY', 'สมาชิกเท่านั้นที่สามารถมองเห็นได้');
DEFINE('_COM_A_REG_ONLY_DESC', 'เซ็ทค่า &quot;ใช่&quot; สมาชิกเท่านั้นที่สามารถมองเห็น กระทู้ล่าสุด');
DEFINE('_COM_A_PUBWRITE', 'เผยแพร่ข้อความ/เขียน');
DEFINE('_COM_A_PUBWRITE_DESC', 'เซ็ทค่า &quot;ใช่&quot; เพื่อยินยอมความเป็นส่วนโดยผู้เยี่ยมชมสามารถอ่านได้อย่างเดียว ไม่สามารถแก้ไขข้อความของสมาชิกท่านอื่นได้');
DEFINE('_COM_A_USER_EDIT', 'แก้ไขข้อความ');
DEFINE('_COM_A_USER_EDIT_DESC', 'เซ็ทค่า &quot;ใช่&quot; สมาชิกสามารถแก้ไขกระทู้ของตัวเองได้.');
DEFINE('_COM_A_MESSAGE', 'In order to save changes of the values above, press the &quot;Save&quot; button at the top.');
DEFINE('_COM_A_HISTORY', 'สรุปหัวข้อ');
DEFINE('_COM_A_HISTORY_DESC', 'เซ็ทค่า &quot;ใช่&quot; เพื่อแสดงหน้าต่าง สรุปหัวข้อ ทุกครั้งหากมีการ อ้างอิงข้อความ/ตอบกระทู้นี้');
DEFINE('_COM_A_SUBSCRIPTIONS', 'เปิดการติดตามหัวข้อ');
DEFINE('_COM_A_SUBSCRIPTIONS_DESC', 'ตั้งค่า &quot;ใช่&quot; เพื่อเปิดการใช้งานฟังก์ชั่นการติดตามหัวข้อของสมาชิก โดยส่งไปยังอีเมล์');
DEFINE('_COM_A_HISTLIM', 'รายการสรุปหัวข้อ');
DEFINE('_COM_A_HISTLIM_DESC', 'จำนวนรายการสรุปหัวข้อที่แสดงในหนึ่งหน้า');
DEFINE('_COM_A_FLOOD', 'ระบบกันฟลัดข้อความ');
DEFINE('_COM_A_FLOOD_DESC', 'จำนวนวินาทีที่แบ่งให้สมาชิกไม่สามารถโพสต์ติดต่อกันได้. ตั้งค่า 0 (ศูนย์) เพื่อปิดการใช้งานนี้.');
DEFINE('_COM_A_MODERATION', 'อีเมล์ของ ผู้ดูแลบอร์ด');
DEFINE('_COM_A_MODERATION_DESC',
    'ตั้งค่า &quot;ใช่&quot; หากมีกระทู้ใหม่ จะถูกส่งเข้าอีเมล์ของผู้ดูแลบอร์ดโดยตรง!');
DEFINE('_COM_A_SHOWMAIL', 'แสดงอีเมลล์');
DEFINE('_COM_A_SHOWMAIL_DESC', 'เซ็ทค่า &quot;ไม่&quot; หากคุณไม่ต้องการให้แสดงอีเมล์แก่บุคคลสาธารณะ; โดยสมาชิกสามารถเลือกได้.');
DEFINE('_COM_A_AVATAR', 'เปิดให้ใช้รูปส่วนตัว');
DEFINE('_COM_A_AVATAR_DESC', 'เซ็ทค่า &quot;ใช่&quot; เปิดให้สมาชิกสามารถเลือกรูปส่วนตัวและสามารถอัพโหลดได้)');
DEFINE('_COM_A_AVHEIGHT', 'สูงสุด.ความสูงของภาพ');
DEFINE('_COM_A_AVWIDTH', 'สูงสุด. ความยาวของภาพ');
DEFINE('_COM_A_AVSIZE', 'สูงสุด. ขนาดไฟล์ของภาพ<br/><em>หน่วยเป็น กิโลไบต์</em>');
DEFINE('_COM_A_USERSTATS', 'แสดงสถานะของสมาชิก');
DEFINE('_COM_A_USERSTATS_DESC', 'เซ็ทค่า &quot;ใช่&quot; หากคุณต้องการให้แสดงสถานะของสมาชิก, เช่น (Admin, Moderator, User, อื่นๆ.).');
DEFINE('_COM_A_AVATARUPLOAD', 'เปิดให้สมาชิกอัพโหลดรูปส่วนตัวได้');
DEFINE('_COM_A_AVATARUPLOAD_DESC', 'เซ็ทค่า &quot;ใช่&quot; สมาชิกสามารถอัพโหลดรูปจากเครื่องตัวเองมาใช้ได้.');
DEFINE('_COM_A_AVATARGALLERY', 'เลือกรูปจากคลังภาพ');
DEFINE('_COM_A_AVATARGALLERY_DESC', 'ตั้งค่า &quot;ใช่&quot; สมาชิกสามารถเลือกรูปจากคลังภาพได้ ตำแหน่งพาธของรูป: (components/com_fireboard/avatars/gallery).');
DEFINE('_COM_A_RANKING_DESC', 'เซ็ทค่า &quot;ใช่&quot; เปิดการใช้งานการจัดระดับของสมาชิก.<br/><strong>ระดับนี้จะแสดง ณ ตำแหน่งแสดงข้อมูลของสมาชิกนั้นๆ.</strong>');
DEFINE('_COM_A_RANKINGIMAGES', 'ใช้รูปภาพบอกระดับ');
DEFINE('_COM_A_RANKINGIMAGES_DESC',
    'เซ็ทค่า &quot;ใช่&quot; if you want to show the rank registered users have with an image (from components/com_fireboard/ranks). Turning this of will show the text for that rank. Check the documentation on www.bestofjoomla.com for more information on ranking images');
DEFINE('_COM_A_RANK1', 'ตำแหน่งที่ 1');
DEFINE('_COM_A_RANK1TXT', 'ชื่อตำแหน่งที่ 1');
DEFINE('_COM_A_RANK2', 'ตำแหน่งที่ 2');
DEFINE('_COM_A_RANK2TXT', 'ชื่อตำแหน่งที่ 2');
DEFINE('_COM_A_RANK3', 'ตำแหน่งที่ 3');
DEFINE('_COM_A_RANK3TXT', 'ชื่อตำแหน่งที่ 3');
DEFINE('_COM_A_RANK4', 'ตำแหน่งที่ 4');
DEFINE('_COM_A_RANK4TXT', 'ชื่อตำแหน่งที่ 4');
DEFINE('_COM_A_RANK5', 'ตำแหน่งที่ 5');
DEFINE('_COM_A_RANK5TXT', 'ชื่อตำแหน่งที่ 5');
DEFINE('_COM_A_RANK6', 'ตำแหน่งที่ 6');
DEFINE('_COM_A_RANK6TXT', 'ชื่อตำแหน่งที่ 6');
DEFINE('_COM_A_RANK', 'ตำแหน่ง');
DEFINE('_COM_A_RANK_NAME', 'ชื่อตำแหน่ง');
DEFINE('_COM_A_RANK_LIMIT', 'ลำดับสูงสุด');
//email and stuff
$_COM_A_NOTIFICATION = "New post notification from ";
$_COM_A_NOTIFICATION1 = "A new post has been made to a topic to which you have subscribed on the ";
$_COM_A_NOTIFICATION2 = "You can administer your subscriptions by following the 'my profile' link on the forum home page after you have logged in on the site. From your profile you can also unsubscribe from the topic.";
$_COM_A_NOTIFICATION3 = "Do not answer to this email notification as it is a generated email.";
$_COM_A_NOT_MOD1 = "A new post has been made to a forum to which you have assigned as moderator on the ";
$_COM_A_NOT_MOD2 = "Please have a look at it after you have logged in on the site.";
DEFINE('_COM_A_NO', 'ไม่');
DEFINE('_COM_A_YES', 'ใช่');
DEFINE('_COM_A_FLAT', 'Flat');
DEFINE('_COM_A_THREADED', 'Threaded');
DEFINE('_COM_A_MESSAGES', 'จำนวนข้อความต่อหน้า');
DEFINE('_COM_A_MESSAGES_DESC', 'แสดงรายการข้อความที่ถูกเผยแพร่ต่อหนึ่งหน้า');
//admin; changes from 0.9 to 0.9.1
DEFINE('_COM_A_USERNAME', 'ลักษณะชื่อ');
DEFINE('_COM_A_USERNAME_DESC', 'ตั้งค่า &quot;ใช่&quot; หากใช้ username (ในการล็อกอิน) และใช้ชื่อจริงที่สมัครไว้');
DEFINE('_COM_A_CHANGENAME', 'สมาชิกสมารถแก้ไขชื่อได้');
DEFINE('_COM_A_CHANGENAME_DESC', 'สมาชิกสามารถแก้ไขชื่อได้ คำเตือน!:การเปิดใช้ฟังก์ชั่นนี้อาจทำให้ผู้ดูแลระบบสับสนและจัดการสมาชิกได้ยาก');
//admin; changes 0.9.1 to 0.9.2
DEFINE('_COM_A_BOARD_OFFLINE', 'ปิดฟอรั่มชั่วคราว');
DEFINE('_COM_A_BOARD_OFFLINE_DESC', 'เซ็ทค่า &quot;ใช่&quot; เพื่อปิดเวปบอร์ดชั่วคราว. สำหรับผู้ดูแลระบบเท่านั้นที่สามารถตั้งค่านี้ได้.');
DEFINE('_COM_A_BOARD_OFFLINE_MES', 'ข้อความปิดของบอร์ด');
DEFINE('_COM_A_PRUNE', 'ตั้งค่าการล้างข้อมูลฟอรั่ม');
DEFINE('_COM_A_PRUNE_NAME', 'รายการฟอรั่ม:');
DEFINE('_COM_A_PRUNE_DESC',
    'ตั้งค่าระบบการล้างข้อมูลฟอรั่มอัตโนมัติ โดยผู้ดูแลสามารถเลือกรายการฟอรั่มที่ต้องการได้ ระบบจะล้างข้อมูลฟอรั่มเองโดยอัตโนมัติหากกระทู้นั้น ไม่มีการตอบโดยสมาชิกท่านอื่น.');
DEFINE('_COM_A_PRUNE_NOPOSTS', 'ตั้งค่าล้างข้อมูลฟอรั่มที่ไม่ได้ถูกใช้งานภายใน ');
DEFINE('_COM_A_PRUNE_DAYS', 'วัน');
DEFINE('_COM_A_PRUNE_USERS', 'ตั้งค่าการล้างข้อมูลสมาชิก');
DEFINE('_COM_A_PRUNE_USERS_DESC',
    'This function allows you to prune your Fireboard user list against the Joomla! Site user list. It will delete all profiles for Fireboard Users that have been deleted from your Joomla! Framework.<br/>When you are sure you want to continue, click &quot;Start Pruning&quot; in the menu bar above.');
//general
DEFINE('_GEN_ACTION', 'Action');
DEFINE('_GEN_AUTHOR', 'โดย');
DEFINE('_GEN_BY', 'โดย');
DEFINE('_GEN_CANCEL', 'ยกเลิก');
DEFINE('_GEN_CONTINUE', 'ยืนยัน');
DEFINE('_GEN_DATE', 'วันที่');
DEFINE('_GEN_DELETE', 'ลบ');
DEFINE('_GEN_EDIT', 'แก้ไข');
DEFINE('_GEN_EMAIL', 'อีเมล์');
DEFINE('_GEN_EMOTICONS', 'รูปแบบแสดงอารมณ์');
DEFINE('_GEN_FLAT', 'Flat');
DEFINE('_GEN_FLAT_VIEW', 'Flat View');
DEFINE('_GEN_FORUMLIST', 'Forum List');
DEFINE('_GEN_FORUM', 'ฟอรั่ม');
DEFINE('_GEN_HELP', 'ช่วยเหลือ');
DEFINE('_GEN_HITS', 'ผู้ชม');
DEFINE('_GEN_LAST_POST', 'คำตอบล่าสุด');
DEFINE('_GEN_LATEST_POSTS', 'รายละเอียดของคำตอบ');
DEFINE('_GEN_LOCK', 'ล็อค');
DEFINE('_GEN_UNLOCK', 'ปลดล็อค');
DEFINE('_GEN_LOCKED_FORUM', 'หัวข้อที่ถูกล็อค');
DEFINE('_GEN_LOCKED_TOPIC', 'หัวข้อที่ถูกล็อค');
DEFINE('_GEN_MESSAGE', 'ข้อความ');
DEFINE('_GEN_MODERATED', 'หัวข้อโดยผู้ดูแลบอร์ด; กำลังตรวจสอบ.');
DEFINE('_GEN_MODERATORS', 'ผู้ดูแลบอร์ด');
DEFINE('_GEN_MOVE', 'เคลื่อนย้าย');
DEFINE('_GEN_NAME', 'ชื่อ');
DEFINE('_GEN_POST_NEW_TOPIC', 'เริ่มหัวข้อใหม่');
DEFINE('_GEN_POST_REPLY', 'ตอบกลับ');
DEFINE('_GEN_MYPROFILE', 'ข้อมูลส่วนตัว');
DEFINE('_GEN_QUOTE', 'อ้างอิง');
DEFINE('_GEN_REPLY', 'กลับ');
DEFINE('_GEN_REPLIES', 'กระทู้');
DEFINE('_GEN_THREADED', 'Threaded');
DEFINE('_GEN_THREADED_VIEW', 'Threaded View');
DEFINE('_GEN_SIGNATURE', 'ลายเซ็นต์');
DEFINE('_GEN_ISSTICKY', 'หัวข้อนี้ปักหมุด');
DEFINE('_GEN_STICKY', 'ปักหมด');
DEFINE('_GEN_UNSTICKY', 'ยกเลิกปักหมุด');
DEFINE('_GEN_SUBJECT', 'หัวข้อ');
DEFINE('_GEN_SUBMIT', 'ยืนยัน');
DEFINE('_GEN_TOPIC', 'หัวข้อ');
DEFINE('_GEN_TOPICS', 'หัวข้อ');
DEFINE('_GEN_TOPIC_ICON', 'หัวข้อไอคอน');
DEFINE('_GEN_SEARCH_BOX', 'ค้นหากระทู้');
$_GEN_THREADED_VIEW = "Threaded View";
$_GEN_FLAT_VIEW = "Flat View";
//avatar_upload.php
DEFINE('_UPLOAD_UPLOAD', 'Upload');
DEFINE('_UPLOAD_DIMENSIONS', 'ขนาดของรูปภาพ (กว้าง x สูง - ขนาด)');
DEFINE('_UPLOAD_SUBMIT', 'เลือกรูปส่วนตัวจากเครื่องของฉัน');
DEFINE('_UPLOAD_SELECT_FILE', 'เลือกรูปภาพ');
DEFINE('_UPLOAD_ERROR_TYPE', 'กรุณาใช้รูปภาพที่มีนามสกุล jpeg, gif หรือ png เท่านั้น');
DEFINE('_UPLOAD_ERROR_EMPTY', 'กรุณาเลือกรูปภาพหลังจากอัพโหลดเสร็จ');
DEFINE('_UPLOAD_ERROR_NAME', 'The image file must contain only alphanumeric characters and no spaces please.');
DEFINE('_UPLOAD_ERROR_SIZE', 'รูปภาพของท่านมีขนาดใหญ่เกินขนาดกำหนด.');
DEFINE('_UPLOAD_ERROR_WIDTH', 'รูปภาพของท่านมีขนาดความยาวเกินขนาดกำหนด.');
DEFINE('_UPLOAD_ERROR_HEIGHT', 'รูปภาพของท่านมีขนาดความสูงเกินขนาดกำหนด.');
DEFINE('_UPLOAD_ERROR_CHOOSE', "กรุณาเลือกรูปภาพส่วนตัว...");
DEFINE('_UPLOAD_UPLOADED', 'รูปภาพของคุณได้ถูกอัพโหลดเรียบร้อย.');
DEFINE('_UPLOAD_GALLERY', 'เลือกรูปส่วนตัว:');
DEFINE('_UPLOAD_CHOOSE', 'บันทึก.');
// listcat.php
DEFINE('_LISTCAT_ADMIN', 'An administrator should create them first from the ');
DEFINE('_LISTCAT_DO', 'They will know what to do ');
DEFINE('_LISTCAT_INFORM', 'Inform them and tell them to hurry up!');
DEFINE('_LISTCAT_NO_CATS', 'There are no categories in the forum defined yet.');
DEFINE('_LISTCAT_PANEL', 'Administration Panel of the Joomla! OS CMS.');
DEFINE('_LISTCAT_PENDING', 'pending message(s)');
// moderation.php
DEFINE('_MODERATION_MESSAGES', 'There are no pending messages in this forum.');
// post.php
DEFINE('_POST_ABOUT_TO_DELETE', 'You are about to delete the message');
DEFINE('_POST_ABOUT_DELETE', '<strong>NOTES:</strong><br/>
-if you delete a Forum Topic (the first post in a thread) all children will be deleted as well!
..Consider blanking the post and posters name if only the contents should be removed..
<br/>
- All children of a deleted normal post will be moved up 1 rank in the thread hierarchy.');
DEFINE('_POST_CLICK', 'click here');
DEFINE('_POST_ERROR', 'Could not find username/email. Severe database error not listed');
DEFINE('_POST_ERROR_MESSAGE', 'An unknown SQL Error occured and your message was not posted.  If the problem persists, please contact the administrator.');
DEFINE('_POST_ERROR_MESSAGE_OCCURED', 'An error has occured and the message was not updated.  Please try again.  If this error persists please contact the administrator.');
DEFINE('_POST_ERROR_TOPIC', 'An error occured during the delete(s). Please check the error below:');
DEFINE('_POST_FORGOT_NAME', 'คุณลืมใส่ชื่อ แจ้งด้วยนะว่าใครตอบจะได้ตามถูก.  Click your browser&#146s back button to go back and try again.');
DEFINE('_POST_FORGOT_SUBJECT', 'ลืมใส่หัวข้อเรื่อง.  Click your browser&#146s back button to go back and try again.');
DEFINE('_POST_FORGOT_MESSAGE', '...ลืม พิมพ์ข้อความ รึเปล่า ???.  Click your browser&#146s back button to go back and try again.');
DEFINE('_POST_FORGOT_NAME_ALERT', 'คุณลืมใส่ชื่อ แจ้งด้วยนะว่าใครตอบจะได้ตามถูก.');
DEFINE('_POST_FORGOT_EMAIL_ALERT', 'อีเมล์ ต้องใส่ด้วยนะ.');
DEFINE('_POST_FORGOT_SUBJECT_ALERT', 'ลืมใส่หัวข้อเรื่อง.');
DEFINE('_KUNENA_EDIT_TITLE', 'แก้ไขข้อมูลเข้าระบบ');
DEFINE('_KUNENA_YOUR_NAME', 'Your Name:');
DEFINE('_KUNENA_EMAIL', 'e-mail:');
DEFINE('_KUNENA_UNAME', 'User Name:');
DEFINE('_KUNENA_PASS', 'Password:');
DEFINE('_KUNENA_VPASS', 'Verify Password:');
DEFINE('_KUNENA_USER_DETAILS_SAVE', 'User details have been saved.');
DEFINE('_KUNENA_TEAM_CREDITS', 'Credits');
DEFINE('_COM_A_BBCODE', 'BBCode');

DEFINE('_POST_INVALID', 'An invalid post id was requested.');
DEFINE('_POST_LOCK_SET', 'หัวข้อนี้ได้ถูกล็อคโดยผู้ใช้เรียบร้อยแล้ว');
DEFINE('_POST_LOCK_NOT_SET', 'The topic could not be locked.');
DEFINE('_POST_LOCK_UNSET', 'หัวข้อนี้ได้ถูกปลดล็อคโดยผู้ใช้เรียบร้อยแล้ว.');
DEFINE('_POST_LOCK_NOT_UNSET', 'The topic could not be unlocked.');
DEFINE('_POST_MESSAGE', 'เริ่มหัวข้อใหม่ใน ');
DEFINE('_POST_MOVE_TOPIC', 'เลือกรายการประเภท ');
DEFINE('_POST_NEW', 'Post a new message in: ');
DEFINE('_POST_NO_SUBSCRIBED_TOPIC', 'Your subscription to this topic could not be processed.');
DEFINE('_POST_NOTIFIED', 'บันทึกรายการหากมีกระทู้ใหม่.');
DEFINE('_POST_STICKY_SET', 'หัวข้อนี้ได้ถูกปักหมุดโดยผู้ใช้เรียบร้อยแล้ว');
DEFINE('_POST_STICKY_NOT_SET', 'The sticky bit could not be set for this topic.');
DEFINE('_POST_STICKY_UNSET', 'หัวข้อนี้ได้ถูกยกเลิกการปักหมุดโดยผู้ใช้เรียบร้อยแล้ว');
DEFINE('_POST_STICKY_NOT_UNSET', 'The sticky bit could not be unset for this topic.');
DEFINE('_POST_SUBSCRIBE', 'การติดตาม');
DEFINE('_POST_SUBSCRIBED_TOPIC', 'สถานะการติดตามหัวข้อของคุณ');
DEFINE('_POST_SUCCESS', 'Your message has been successfully');
DEFINE('_POST_SUCCES_REVIEW', 'Your message has been successfully posted.  It will be reviewed by a Moderator before it will be published on the forum.');
DEFINE('_POST_SUCCESS_REQUEST', 'Your request has been processed.  If you are not taken back to the topic in a few moments,');
DEFINE('_POST_TOPIC_HISTORY', 'สรุปหัวข้อ');
DEFINE('_POST_TOPIC_HISTORY_MAX', 'รูปแบบ. การจัดเรียงข้อความ');
DEFINE('_POST_TOPIC_HISTORY_LAST', 'posts  -  <i>(ข้อความล่าสุดอยู่ข้างบน)</i>');
DEFINE('_POST_TOPIC_NOT_MOVED', 'Your topic could not be moved. To get back to the topic:');
DEFINE('_POST_TOPIC_FLOOD1', 'The administrator of this forum has enabled Flood Protection and has decided that you must wait ');
DEFINE('_POST_TOPIC_FLOOD2', ' seconds before you can make another post.');
DEFINE('_POST_TOPIC_FLOOD3', 'Please click your browser&#146s back button to get back to the forum.');
DEFINE('_POST_EMAIL_NEVER', 'your email address will never be displayed on the site.');
DEFINE('_POST_EMAIL_REGISTERED', 'your email address will only be available to registered users.');
DEFINE('_POST_LOCKED', 'locked by the administrator.');
DEFINE('_POST_NO_NEW', 'New replies are not allowed.');
DEFINE('_POST_NO_PUBACCESS1', 'ไม่อนุญาติให้บุคคลทั่วไปโพสต์ครับ');
DEFINE('_POST_NO_PUBACCESS2', 'กรุณาเข้าสู่ระบบหรือลงทะเบียน<br /> ขอบคุณครับ.');
// showcat.php
DEFINE('_SHOWCAT_NO_TOPICS', '>> ยังไม่มีกระทู้ใหม่ <<');
DEFINE('_SHOWCAT_PENDING', 'ข้อความรอการตรวจสอบ');
// userprofile.php
DEFINE('_USER_DELETE', ' ฉัน ต้องการลบรูปส่วนตัวออก');
DEFINE('_USER_ERROR_A', 'You came to this page in error. Please inform the administrator on which links ');
DEFINE('_USER_ERROR_B', 'you clicked that got you here. She or he can then file a bug report.');
DEFINE('_USER_ERROR_C', 'Thank you!');
DEFINE('_USER_ERROR_D', 'Error number to include in your report: ');
DEFINE('_USER_GENERAL', 'ตั้งค่าข้อมูลทั่วไป');
DEFINE('_USER_MODERATOR', 'You are assigned as a Moderator to forums');
DEFINE('_USER_MODERATOR_NONE', 'No forums found assigned to you');
DEFINE('_USER_MODERATOR_ADMIN', 'Admins are moderator on all forums.');
DEFINE('_USER_NOSUBSCRIPTIONS', 'ไม่มีรายการดังกล่าว');
DEFINE('_USER_PREFERED', 'รูปแบบมุมมอง');
DEFINE('_USER_PROFILE', 'ข้อมูลของ : ');
DEFINE('_USER_PROFILE_NOT_A', 'Your profile could ');
DEFINE('_USER_PROFILE_NOT_B', 'not');
DEFINE('_USER_PROFILE_NOT_C', ' be updated.');
DEFINE('_USER_PROFILE_UPDATED', 'บันทึกรายการเรียบร้อย.');
DEFINE('_USER_RETURN_A', 'กลับไปหน้าตั้งค่าข้อมูลส่วนตัวอีกครั้ง : ');
DEFINE('_USER_RETURN_B', 'กลับ');
DEFINE('_USER_SUBSCRIPTIONS', 'รายการกระทู้ติดตามของฉัน');
DEFINE('_USER_UNSUBSCRIBE', 'Unsubscribe');
DEFINE('_USER_UNSUBSCRIBE_A', 'You could ');
DEFINE('_USER_UNSUBSCRIBE_B', 'not');
DEFINE('_USER_UNSUBSCRIBE_C', ' be unsubscribed from the topic.');
DEFINE('_USER_UNSUBSCRIBE_YES', 'สถานะการบันทึกการติดตามหัวข้อนี้ของคุณ');
DEFINE('_USER_DELETEAV', ' ฉัน ต้องการลบรูปส่วนตัวออก');
//New 0.9 to 1.0
DEFINE('_USER_ORDER', 'รูปแบบข้อความ');
DEFINE('_USER_ORDER_DESC', 'กระทู้ล่าสุดขึ้นก่อน');
DEFINE('_USER_ORDER_ASC', 'กระทู้แรกเริ่มก่อน');
// view.php
DEFINE('_VIEW_DISABLED', 'The administrator has disabled public write access.');
DEFINE('_VIEW_POSTED', 'Posted by');
DEFINE('_VIEW_SUBSCRIBE', ':: Subscribe to this topic ::');
DEFINE('_MODERATION_INVALID_ID', 'An invalid post id was requested.');
DEFINE('_VIEW_NO_POSTS', 'There are no posts in this forum.');
DEFINE('_VIEW_VISITOR', 'ผู้เยี่ยมชม');
DEFINE('_VIEW_ADMIN', 'ผู้ดูแลระบบ');
DEFINE('_VIEW_USER', 'สมาชิก');
DEFINE('_VIEW_MODERATOR', 'ผู้ดูแลบอร์ด');
DEFINE('_VIEW_REPLY', 'ตอบกระทู้นี้');
DEFINE('_VIEW_EDIT', 'แก้ไขข้อความ');
DEFINE('_VIEW_QUOTE', 'อ้างอิงข้อความ');
DEFINE('_VIEW_DELETE', 'ลบข้อความ');
DEFINE('_VIEW_STICKY', 'ปักหมุดหัวข้อนี้');
DEFINE('_VIEW_UNSTICKY', 'ยกเลิกการปักหมุดหัวข้อนี้');
DEFINE('_VIEW_LOCK', 'ล็อคหัวข้อนี้');
DEFINE('_VIEW_UNLOCK', 'ปลดล็อคหัวข้อนี้');
DEFINE('_VIEW_MOVE', 'เคลื่อนย้ายหัวข้อนี้');
DEFINE('_VIEW_SUBSCRIBETXT', 'ติดตามหัวข้อนี้');
//NEW-STRINGS-FOR-TRANSLATING-READY-FOR-SIMPLEBOARD 9.2
DEFINE('_HOME', 'หน้าแรก');
DEFINE('_POSTS', 'กระทู้:');
DEFINE('_TOPIC_NOT_ALLOWED', 'Post');
DEFINE('_FORUM_NOT_ALLOWED', 'ฟอรั่ม');
DEFINE('_FORUM_IS_OFFLINE', 'Forum is OFFLINE!');
DEFINE('_PAGE', 'หน้า: ');
DEFINE('_NO_POSTS', 'ไม่มีกระทู้ใหม่');
DEFINE('_CHARS', 'ตัวอักษร สูงสุด.');
DEFINE('_HTML_YES', 'HTML ได้ถูกปิด');
DEFINE('_YOUR_AVATAR', '<b>รูปส่วนตัว</b>');
DEFINE('_NON_SELECTED', 'ไม่มีรูปส่วนตัวในขณะนี้ <br>');
DEFINE('_SET_NEW_AVATAR', 'เลือกรูปส่วนตัว');
DEFINE('_THREAD_UNSUBSCRIBE', 'Unsubscribe');
DEFINE('_SHOW_LAST_POSTS', 'กระทู้ล่าสุดใน');
DEFINE('_SHOW_HOURS', 'ชั่วโมง');
DEFINE('_SHOW_POSTS', 'ทั้งหมด: ');
DEFINE('_DESCRIPTION_POSTS', 'แสดงกระทู้อย่างละเอียด');
DEFINE('_SHOW_4_HOURS', '4 ชั่วโมง');
DEFINE('_SHOW_8_HOURS', '8 ชั่วโมง');
DEFINE('_SHOW_12_HOURS', '12 ชั่วโมง');
DEFINE('_SHOW_24_HOURS', '24 ชั่วโมง');
DEFINE('_SHOW_48_HOURS', '48 ชั่วโมง');
DEFINE('_SHOW_WEEK', 'สัปดาห์นี้');
DEFINE('_POSTED_AT', 'โดย');
DEFINE('_DATETIME', 'Y/m/d H:i');
DEFINE('_NO_TIMEFRAME_POSTS', 'ยังไม่มีรายการบันทึก.');
DEFINE('_MESSAGE', 'ข้อความ');
DEFINE('_NO_SMILIE', 'ไม่มี');
DEFINE('_FORUM_UNAUTHORIZIED', 'This forum is open only to registered and logged in users.');
DEFINE('_FORUM_UNAUTHORIZIED2', 'If you are already registered, please log in first.');
DEFINE('_MESSAGE_ADMINISTRATION', 'Moderation');
DEFINE('_MOD_APPROVE', 'Approve');
DEFINE('_MOD_DELETE', 'Delete');
//NEW in RC1
DEFINE('_SHOW_LAST', 'แสดงกระทู้ของท่านนี้');
DEFINE('_POST_WROTE', 'อ้างอิงข้อความ');
DEFINE('_COM_A_EMAIL', 'อีเมล์บอร์ด');
DEFINE('_COM_A_EMAIL_DESC', 'อีเมล์หลักของบอร์ด. อีเมล์นี้จะถูกใช้กับสมาชิกที่ติดตามหัวข้อทุกคน');
DEFINE('_COM_A_WRAP', 'ความยาวข้อความต่อหนึ่งวรรค');
DEFINE('_COM_A_WRAP_DESC',
    'ความยาวข้อความต่อหนึ่งวรรคจะถูกขึ้นวรรคใหม่โดยอัตโนมัติ โดยผู้ดูแลระบบนั้นสามารถตั้งได้ โดยค่าเฉลี่ยจะอยู่ที่ 70-100 ตัวอักษร');
DEFINE('_COM_A_SIGNATURE', 'ความยาวสูงสุด.สำหรับลายเซ็นต์');
DEFINE('_COM_A_SIGNATURE_DESC', 'จำนวนตัวอักษรสูงสุดในการตั้งลายเซ็นต์.');
DEFINE('_SHOWCAT_NOPENDING', 'No pending message(s)');
DEFINE('_COM_A_BOARD_OFSET', 'หน่วงเวลาของบอร์ด');
DEFINE('_COM_A_BOARD_OFSET_DESC', 'การตั้งค่าหน่วงเวลาของบอร์ดอาจมีผลร้ายแรงต่อเว็บบอร์ดของท่านได้ กรุณาอ่านข้อศึกษาก่อนตั้งค่าเวลาทุกครั้ง,กรุณาตั้งค่าตาม zone ของท่านในที่นี่เท่ากับ 7');
//New in RC2
DEFINE('_COM_A_BASICS', 'พื้นฐาน');
DEFINE('_COM_A_FRONTEND', 'หน้าแรก');
DEFINE('_COM_A_SECURITY', 'ระบบป้องกัน');
DEFINE('_COM_A_AVATARS', 'รูปส่วนตัว');
DEFINE('_COM_A_INTEGRATION', 'เพิ่มเติม');
DEFINE('_COM_A_PMS', 'Enable private messaging');
DEFINE('_COM_A_PMS_DESC',
    'Select the appropriate private messaging component if you have installed any. Selecting Clexus PM will also enable ClexusPM user profile related options (like ICQ, AIM, Yahoo, MSN and profile links if supported by the Fireboard template used');
DEFINE('_VIEW_PMS', 'Click here to send a Private Message to this user');
//new in RC3
DEFINE('_POST_RE', 'Re:');
DEFINE('_BBCODE_BOLD', 'คำสั่งตัวหนา: [b]ข้อความ[/b] ');
DEFINE('_BBCODE_ITALIC', 'คำสั่งตัวเอียง: [i]ข้อความ[/i]');
DEFINE('_BBCODE_UNDERL', 'ขีดเส้นใต้: [u]ข้อความ[/u]');
DEFINE('_BBCODE_QUOTE', 'Quote text: [quote]text[/quote]');
DEFINE('_BBCODE_CODE', 'Code display: [code]code[/code]');
DEFINE('_BBCODE_ULIST', 'Unordered List: [ul] [li]text[/li] [/ul] - Hint: a list must contain List Items');
DEFINE('_BBCODE_OLIST', 'Ordered List: [ol] [li]text[/li] [/ol] - Hint: a list must contain List Items');
DEFINE('_BBCODE_IMAGE', 'รูปภาพ: [img size=(01-499)]http://www.google.com/images/web_logo_left.gif[/img]');
DEFINE('_BBCODE_LINK', 'ลิงค์: [url=http://www.zzz.com/]This is a link[/url]');
DEFINE('_BBCODE_CLOSA', 'ยกเลิกคำสั่งทั้งหมด');
DEFINE('_BBCODE_CLOSE', 'ยกเลิกคำสั่ง bbCode ทั้งหมด');
DEFINE('_BBCODE_COLOR', 'สี: [color=#FF6600]text[/color]');
DEFINE('_BBCODE_SIZE', 'ขนาด: [size=1]ขนาดตัวอักษร[/size] - Hint: sizes range from 1 to 5');
DEFINE('_BBCODE_LITEM', 'รายการไอเทม: [li] list item [/li]');
DEFINE('_BBCODE_HINT', 'bbCode Help - Hint: bbCode can be used on selected text!');
DEFINE('_BBCODE_HIDE', 'ซ่อน');

DEFINE('_COM_A_TAWIDTH', 'พื้นที่ข้อความ (Width)');
DEFINE('_COM_A_TAWIDTH_DESC', 'พื้นที่กรอกข้อความทั้งหมด ในเทมเพลท. <br/โดยถูกกำหนดไว้ที่ <= 420 pixels');
DEFINE('_COM_A_TAHEIGHT', 'พื้นที่ข้อความ (Height)');
DEFINE('_COM_A_TAHEIGHT_DESC', 'พื้นที่กรอกข้อความทั้งหมด ในเทมเพลท');
DEFINE('_COM_A_ASK_EMAIL', 'ต้องการ อีเมล์');
DEFINE('_COM_A_ASK_EMAIL_DESC', 'ต้องใช้อีเมล์ทุกครั้งในการตั้งกระทู้ รวมถึงผู้เยี่ยมชม. เซ็ทค่า &quot;ไม่&quot;เพื่อไม่ต้องการใช้อีเมล์ในการตั้งกระทู้.');
///////
DEFINE('_KUNENA_WELCOME','เว็บบอร์ด...นายแว่น');
DEFINE('_KUNENA_WELCOME_DESC','ขอบคุณคุณสำหรับการเลือก FireBoard เป็นของคุณบอร์ดการแก้ปัญหา.-การแนะนำคร่าวๆรวดเร็วของสถิติต่างๆทั้งหมดของคุณบอร์ด.เชื่อมบนด้านซ้ายมือนี้ยอมให้คุณเพื่อควบคุมรูปการทั้งหมดของของคุณบอร์ดมีประสบการณ์.หน้าแต่ละจะมีคำสั่งบนเพื่อใช้เครื่องมือเป็นอย่างไร.');
DEFINE('_KUNENA_STATISTIC','สถิติ');
DEFINE('_KUNENA_VALUE','ค่า');

DEFINE('_GEN_CATEGORY','ประเภท');
DEFINE('_GEN_STARTEDBY','เริ่มต้นโดยทั่วไป :');
DEFINE('_GEN_STATS','เริ่ม');
DEFINE('_STATS_TITLE',' เว็บบอร์ด - เริ่ม');
DEFINE('_STATS_GEN_STATS','สถิติทั่วไป');
DEFINE('_STATS_TOTAL_MEMBERS','สมาชิก:');
DEFINE('_STATS_TOTAL_REPLIES','คำตอบ:');
DEFINE('_STATS_TOTAL_TOPICS','หัวข้อ : ');
DEFINE('_STATS_TODAY_TOPICS','หัวข้อ วันนี้:');
DEFINE('_STATS_TODAY_REPLIES','คำตอบวันนี้:');
DEFINE('_STATS_TOTAL_CATEGORIES','ประเภท:');
DEFINE('_STATS_TOTAL_SECTIONS','ส่วน:');
DEFINE('_STATS_LATEST_MEMBER','สมาชิกล่าสุด:');
DEFINE('_STATS_YESTERDAY_TOPICS','หัวข้อเมื่อวาน:');
DEFINE('_STATS_YESTERDAY_REPLIES','คำตอบเมื่อวาน:');
DEFINE('_STATS_POPULAR_PROFILE','Popular 10 Members (Profile hits)');
DEFINE('_STATS_TOP_POSTERS','Top posters');
DEFINE('_STATS_POPULAR_TOPICS','Top popular topics');
//Rank Administration - Dan Syme/IGD
define('_KUNENA_RANKS_MANAGE', 'Ranks Management');
define('_KUNENA_SORTRANKS', 'Sort by Ranks');
define('_KUNENA_RANKSIMAGE', 'Rank Image');
define('_KUNENA_RANKS', 'Rank Title');
define('_KUNENA_RANKS_SPECIAL', 'Special');
define('_KUNENA_RANKSMIN', 'Minimum Post Count');
define('_KUNENA_RANKS_ACTION', 'Actions');
define('_KUNENA_NEW_RANK', 'New Rank');

// 1.0.2
DEFINE('_KUNENA_HEADERADD', 'Forumheader:');
DEFINE('_KUNENA_ADVANCEDDISPINFO', "Forum display");
DEFINE('_KUNENA_CLASS_SFX', "Forum CSS class suffix");
DEFINE('_KUNENA_CLASS_SFXDESC', "CSS suffix applied to index, showcat, view and allows different designs per forum.");
DEFINE('_COM_A_USER_EDIT_TIME', 'User Edit Time');
DEFINE('_COM_A_USER_EDIT_TIME_DESC', 'Set to 0 for unlimited time, else window
in seconds from post or last modification to allow edit.');
DEFINE('_COM_A_USER_EDIT_TIMEGRACE', 'User Edit Grace Time');
DEFINE('_COM_A_USER_EDIT_TIMEGRACE_DESC', 'Default 600 [seconds], allows
storing a modification up to 600 seconds after edit link disappears');
DEFINE('_KUNENA_HELPPAGE','Enable Help Page');
DEFINE('_KUNENA_HELPPAGE_DESC','If set to &quot;Yes&quot; a link in the header menu will be shown to your Help page.');
DEFINE('_KUNENA_HELPPAGE_IN_FB','Show help in fireboard');
DEFINE('_KUNENA_HELPPAGE_IN_KUNENA_DESC','If set to &quot;Yes&quot; help content text will be include in fireboard and Help external page link will not work. <b>Note:</b> You should add "Help Content ID" .');
DEFINE('_KUNENA_HELPPAGE_CID','Help Content ID');
DEFINE('_KUNENA_HELPPAGE_CID_DESC','You should set <b>"YES"</b> "Show help in fireboard" setting.');
DEFINE('_KUNENA_HELPPAGE_LINK',' Help external page link');
DEFINE('_KUNENA_HELPPAGE_LINK_DESC','If you show help external link, please set <b>"NO"</b> "Show help in fireboard" setting.');
DEFINE('_KUNENA_RULESPAGE','Enable Rules Page');
DEFINE('_KUNENA_RULESPAGE_DESC','If set to &quot;Yes&quot; a link in the header menu will be shown to your Rules page.');
DEFINE('_KUNENA_RULESPAGE_IN_FB','Show rules in fireboard');
DEFINE('_KUNENA_RULESPAGE_IN_KUNENA_DESC','If set to &quot;Yes&quot; rules content text will be include in fireboard and Rules external page link will not work. <b>Note:</b> You should add "Rules Content ID" .');
DEFINE('_KUNENA_RULESPAGE_CID','Rules Content ID');
DEFINE('_KUNENA_RULESPAGE_CID_DESC','You should set <b>"YES"</b> "Show rules in fireboard" setting.');
DEFINE('_KUNENA_RULESPAGE_LINK',' Rules external page link');
DEFINE('_KUNENA_RULESPAGE_LINK_DESC','If you show rules external link, please set <b>"NO"</b> "Show rules in fireboard" setting.');
DEFINE('_KUNENA_AVATAR_GDIMAGE_NOT','GD Library not found');
DEFINE('_KUNENA_AVATAR_GDIMAGE_NOT','GD2 Library not found');
DEFINE('_KUNENA_GD_INSTALLED','GD is avabile version ');
DEFINE('_KUNENA_GD_NO_VERSION','Can not detect GD version');
DEFINE('_KUNENA_GD_NOT_INSTALLED','GD isnt installed, you can get more info ');
DEFINE('_KUNENA_AVATAR_SMALL_HEIGHT','Small Image Height :');
DEFINE('_KUNENA_AVATAR_SMALL_WIDTH','Small Image Width :');
DEFINE('_KUNENA_AVATAR_MEDIUM_HEIGHT','Medium Image Height :');
DEFINE('_KUNENA_AVATAR_MEDUIM_WIDTH','Medium Image Width :');
DEFINE('_KUNENA_AVATAR_LARGE_HEIGHT','Large Image Height :');
DEFINE('_KUNENA_AVATAR_LARGE_WIDTH','Large Image Width :');
DEFINE('_KUNENA_AVATAR_QUALITY','Avatar Quality');
DEFINE('_KUNENA_COM_A_REPORT', 'หน้ารายงานข้อความถึง moderator ');
DEFINE('_KUNENA_COM_A_REPORT_DESC', 'ถ้าคุณต้องการเพื่อผู้ใช้รายงานข้อความใดๆ, เลือกใช่.');
DEFINE('_KUNENA_CAPTCHA_ON','สปามปกป้องระบบ');
DEFINE('_KUNENA_CAPTCHA_DESC','Antispam & antibot CAPTCHA system On/Off');
DEFINE('_KUNENA_CAPDESC','ใส่รหัสที่นี่');
DEFINE('_KUNENA_CAPERR','รหัสไม่ถูกต้อง!');
DEFINE('_KUNENA_MAILFULL','Include complete post content in the email sent to subscribers');
DEFINE('_KUNENA_MAILFULL_DESC','If No - subscribers will receive only titles of new messages');

DEFINE('_KUNENA_SAVE', 'บันทึก');
DEFINE('_KUNENA_RESET', 'รีเซ็ต');

DEFINE('_KUNENA_MYPROFILE_PERSONAL_INFO', 'คำแนะนำส่วนบุคคล');
DEFINE('_KUNENA_MYPROFILE_SUMMARY', 'รายละเอียดข้อมูลส่วนตัว');
DEFINE('_KUNENA_MYPROFILE_MYAVATAR', 'รูปส่วนตัว');
DEFINE('_KUNENA_MYPROFILE_FORUM_SETTINGS', 'ปรับแต่งบร์อด');
DEFINE('_KUNENA_MYPROFILE_LOOK_AND_LAYOUT', 'มุมมอง');
DEFINE('_KUNENA_MYPROFILE_MY_PROFILE_INFO', 'ข้อมูลส่วนตัว');
DEFINE('_KUNENA_MYPROFILE_MY_POSTS', 'กระทู้ที่โพส');
DEFINE('_KUNENA_MYPROFILE_MY_SUBSCRIBES', 'กณะทู้ติดตาม');
DEFINE('_KUNENA_MYPROFILE_MY_FAVORITES', 'กณะทู้ที่ชอบ');
DEFINE('_KUNENA_MYPROFILE_PRIVATE_MESSAGING', 'Private Messaging');
DEFINE('_KUNENA_MYPROFILE_INBOX', 'Inbox');
DEFINE('_KUNENA_MYPROFILE_NEW_MESSAGE', 'ข้อความใหม่');
DEFINE('_KUNENA_MYPROFILE_OUTBOX', 'Outbox');
DEFINE('_KUNENA_MYPROFILE_TRASH', 'Trash');
DEFINE('_KUNENA_MYPROFILE_SETTINGS', 'Settings');
DEFINE('_KUNENA_MYPROFILE_CONTACTS', 'Contacts');
DEFINE('_KUNENA_MYPROFILE_BLOCKEDLIST', 'กล่องรายการ');
DEFINE('_KUNENA_MYPROFILE_ADDITIONAL_INFO', 'ข้อมูลเพิ่มเติม');
DEFINE('_KUNENA_MYPROFILE_NAME', 'Name');
DEFINE('_KUNENA_MYPROFILE_USERNAME', 'Username');
DEFINE('_KUNENA_MYPROFILE_EMAIL', 'Email');
DEFINE('_KUNENA_MYPROFILE_USERTYPE', 'User Type');
DEFINE('_KUNENA_MYPROFILE_REGISTERDATE', 'วันที่เป็นสมาชิก');
DEFINE('_KUNENA_MYPROFILE_LASTVISITDATE', 'เข้าเว็บครั้งสุดท้าย');
DEFINE('_KUNENA_MYPROFILE_POSTS', 'Posts');
DEFINE('_KUNENA_MYPROFILE_PROFILEVIEW', 'ดูข้อมูล');
DEFINE('_KUNENA_MYPROFILE_PERSONALTEXT', 'ข้อความส่วนตัว');
DEFINE('_KUNENA_MYPROFILE_GENDER', 'เพศ');
DEFINE('_KUNENA_MYPROFILE_BIRTHDATE', 'วันเกิด');
DEFINE('_KUNENA_MYPROFILE_BIRTHDATE_DESC', 'วัน (DD)-เดือน -(MM)-ปี (YYYY)');
DEFINE('_KUNENA_MYPROFILE_LOCATION', 'ที่ตั้ง');
DEFINE('_KUNENA_MYPROFILE_ICQ', 'ICQ');
DEFINE('_KUNENA_MYPROFILE_ICQ_DESC', 'This is your ICQ number.');
DEFINE('_KUNENA_MYPROFILE_AIM', 'AIM');
DEFINE('_KUNENA_MYPROFILE_AIM_DESC', 'This is your AOL Instant Messenger nickname.');
DEFINE('_KUNENA_MYPROFILE_YIM', 'YIM');
DEFINE('_KUNENA_MYPROFILE_YIM_DESC', 'This is your Yahoo! Instant Messenger nickname.');
DEFINE('_KUNENA_MYPROFILE_SKYPE', 'SKYPE');
DEFINE('_KUNENA_MYPROFILE_SKYPE_DESC', 'This is your Skype handle.');
DEFINE('_KUNENA_MYPROFILE_GTALK', 'GTALK');
DEFINE('_KUNENA_MYPROFILE_GTALK_DESC', 'This is your Gtalk nickname.');
DEFINE('_KUNENA_MYPROFILE_WEBSITE', 'Website');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_NAME', 'Website Name');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_NAME_DESC', 'Example: Best of Joomla!');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_URL', 'Website URL');
DEFINE('_KUNENA_MYPROFILE_WEBSITE_URL_DESC', 'Example: www.bestofjoomla.com');
DEFINE('_KUNENA_MYPROFILE_MSN', 'MSN');
DEFINE('_KUNENA_MYPROFILE_MSN_DESC', 'Your MSN messenger email address.');
DEFINE('_KUNENA_MYPROFILE_SIGNATURE', 'Signature');
DEFINE('_KUNENA_MYPROFILE_MALE', 'ชายชาตรี');
DEFINE('_KUNENA_MYPROFILE_FEMALE', 'กุลสตรี');
DEFINE('_KUNENA_REPORT_REASON', 'เหตุผล');
DEFINE('_KUNENA_REPORT_MESSAGE', 'ข้อความ');
DEFINE('_KUNENA_REPORT_SEND', 'ส่งข้อความ');
DEFINE('_KUNENA_REPORT', 'รายงานถึง moderator');
DEFINE('_KUNENA_REPORT_LOGGED', 'การบันทึก');
DEFINE('_KUNENA_DATE_YEAR', 'ปี');
DEFINE('_KUNENA_DATE_MONTH', 'เดือน');
DEFINE('_KUNENA_DATE_WEEK','สัปดาห์');
DEFINE('_KUNENA_DATE_DAY', 'วัน');
DEFINE('_KUNENA_DATE_HOUR', 'ชั่วโมง');
DEFINE('_KUNENA_DATE_MINUTE', 'นาที');
DEFINE('_KUNENA_DEFAULT_GALLERY', 'ค่าปกติจาก Gallery');
DEFINE('_KUNENA_SAMPLEREMOVED', 'ลบข้อมูลตัวอย่างเรียบร้อยแล้ว');
DEFINE('_KUNENA_DELETE_SELECTED', 'ลบหัวข้อที่เลือก');
DEFINE('_KUNENA_MOVE_SELECTED', 'ย้ายหัวข้อที่เลือก');

/*1.0.4*/
DEFINE('_WHO_ONLINE_GUEST', 'ผู้ไม่ประสงค์ออกนาม');
DEFINE('_WHO_ONLINE_MEMBER', 'สมาชิก');
DEFINE('_KUNENA_DT_DATETIME_FMT', 'วันเวลา');
DEFINE('_KUNENA_CHILD_BOARD', 'บอร์ดย่อย');
  
/*1.0.4 admin*/
DEFINE('_KUNENA_SYNC_USERS', 'Syncronize ผู้ใช้');
DEFINE('_KUNENA_SYNC_USERS_DESC', 'สถิติผู้ใช้ บอร์ด');
DEFINE('_COM_SYNCUSERSDESC', 'ตารางสถิติผู้ใช้ บอร์ด');

DEFINE('_KUNENA_COPY_FILE', 'Copying "%src%" to "%dst%"...');
DEFINE('_KUNENA_COPY_OK', 'OK');
DEFINE('_KUNENA_CSS_SAVE', 'Saving css file should be here...file="%file%"');
DEFINE('_KUNENA_UP_ATT_10', 'Attachment table successfully upgraded to the latest 1.0.x series structure!');
DEFINE('_KUNENA_UP_ATT_10_MSG', 'Attachments in messages table successfully upgraded to the latest 1.0.x series structure!');
DEFINE('_KUNENA_TOPIC_MOVED', '---');
DEFINE('_KUNENA_TOPIC_MOVED_LONG', '------------');
DEFINE('_KUNENA_POST_DEL_ERR_CHILD', 'Could not promote children in post hierarchy. Nothing deleted.');
DEFINE('_KUNENA_POST_DEL_ERR_MSG', 'Could not delete the post(s) - nothing else deleted');
DEFINE('_KUNENA_POST_DEL_ERR_TXT', 'Could not delete the texts of the post(s). Update the database manually (mesid=%id%).');
DEFINE('_KUNENA_POST_DEL_ERR_USR', 'Everything deleted, but failed to update user post stats!');
DEFINE('_KUNENA_POST_MOV_ERR_DB', "Severe database error. Update your database manually so the replies to the topic are matched to the new forum as well");
DEFINE('_KUNENA_UNIST_SUCCESS', "FireBoard component was successfully uninstalled!");
DEFINE('_KUNENA_PDF_VERSION', 'FireBoard Forum Component version: %version%');
DEFINE('_KUNENA_PDF_DATE', 'Generated: %date%');
DEFINE('_KUNENA_SEARCH_NOFORUM', 'No forums to search in.');

DEFINE('_KUNENA_ERRORADDUSERS', 'Error adding users:');
DEFINE('_KUNENA_USERSSYNCDELETED', 'Users syncronized; Deleted:');
DEFINE('_KUNENA_USERSSYNCADD', ', add:');
DEFINE('_KUNENA_SYNCUSERPROFILES', 'user profiles.');
DEFINE('_KUNENA_NOPROFILESFORSYNC', 'No profiles found eligible for syncronizing.');
DEFINE('_KUNENA_SYNC_USERS', 'Syncronize Users');
DEFINE('_KUNENA_SYNC_USERS_DESC', 'Sync FireBoard user table with Joomla! user table');
DEFINE('_KUNENA_A_MAIL_ADMIN', 'Email Administrators');
DEFINE('_KUNENA_A_MAIL_ADMIN_DESC',
    'Set to &quot;Yes&quot; if you want email notifications on each new post sent to the enabled system administrator(s).');
DEFINE('_KUNENA_RANKS_EDIT', 'Edit Rank');
DEFINE('_KUNENA_USER_HIDEEMAIL', 'Hide Email');
DEFINE('_KUNENA_DT_DATE_FMT','%m/%d/%Y');
DEFINE('_KUNENA_DT_TIME_FMT','%H:%M');
DEFINE('_KUNENA_DT_DATETIME_FMT','%m/%d/%Y %H:%M');
DEFINE('_KUNENA_DT_LDAY_SUN', 'Sunday');
DEFINE('_KUNENA_DT_LDAY_MON', 'Monday');
DEFINE('_KUNENA_DT_LDAY_TUE', 'Tuesday');
DEFINE('_KUNENA_DT_LDAY_WED', 'Wednesday');
DEFINE('_KUNENA_DT_LDAY_THU', 'Thursday');
DEFINE('_KUNENA_DT_LDAY_FRI', 'Friday');
DEFINE('_KUNENA_DT_LDAY_SAT', 'Saturday');
DEFINE('_KUNENA_DT_DAY_SUN', 'Sun');
DEFINE('_KUNENA_DT_DAY_MON', 'Mon');
DEFINE('_KUNENA_DT_DAY_TUE', 'Tue');
DEFINE('_KUNENA_DT_DAY_WED', 'Wed');
DEFINE('_KUNENA_DT_DAY_THU', 'Thu');
DEFINE('_KUNENA_DT_DAY_FRI', 'Fri');
DEFINE('_KUNENA_DT_DAY_SAT', 'Sat');
DEFINE('_KUNENA_DT_LMON_JAN', 'January');
DEFINE('_KUNENA_DT_LMON_FEB', 'February');
DEFINE('_KUNENA_DT_LMON_MAR', 'March');
DEFINE('_KUNENA_DT_LMON_APR', 'April');
DEFINE('_KUNENA_DT_LMON_MAY', 'May');
DEFINE('_KUNENA_DT_LMON_JUN', 'June');
DEFINE('_KUNENA_DT_LMON_JUL', 'July');
DEFINE('_KUNENA_DT_LMON_AUG', 'August');
DEFINE('_KUNENA_DT_LMON_SEP', 'September');
DEFINE('_KUNENA_DT_LMON_OCT', 'October');
DEFINE('_KUNENA_DT_LMON_NOV', 'November');
DEFINE('_KUNENA_DT_LMON_DEV', 'December');
DEFINE('_KUNENA_DT_MON_JAN', 'Jan');
DEFINE('_KUNENA_DT_MON_FEB', 'Feb');
DEFINE('_KUNENA_DT_MON_MAR', 'Mar');
DEFINE('_KUNENA_DT_MON_APR', 'Apr');
DEFINE('_KUNENA_DT_MON_MAY', 'May');
DEFINE('_KUNENA_DT_MON_JUN', 'Jun');
DEFINE('_KUNENA_DT_MON_JUL', 'Jul');
DEFINE('_KUNENA_DT_MON_AUG', 'Aug');
DEFINE('_KUNENA_DT_MON_SEP', 'Sep');
DEFINE('_KUNENA_DT_MON_OCT', 'Oct');
DEFINE('_KUNENA_DT_MON_NOV', 'Nov');
DEFINE('_KUNENA_DT_MON_DEV', 'Dec');
DEFINE('_KUNENA_CHILD_BOARD', 'Child Board');
DEFINE('_WHO_ONLINE_GUEST', 'Guest');
DEFINE('_WHO_ONLINE_MEMBER', 'Member');
DEFINE('_KUNENA_IMAGE_PROCESSOR_NONE', 'none');
DEFINE('_KUNENA_IMAGE_PROCESSOR', 'Image Processor:');
DEFINE('_KUNENA_INSTALL_CLICK_TO_CONTINUE', 'Click here to continue...');
DEFINE('_KUNENA_INSTALL_APPLY', 'Apply!');
DEFINE('_KUNENA_NO_ACCESS', 'You do not have access to this Forum!');
DEFINE('_KUNENA_TIME_SINCE', '%time% ก่อน');
DEFINE('_KUNENA_DATE_YEARS', 'ปี');
DEFINE('_KUNENA_DATE_MONTHS', 'เดือน');
DEFINE('_KUNENA_DATE_WEEKS','สัปดาห์');
DEFINE('_KUNENA_DATE_DAYS', 'วัน');
DEFINE('_KUNENA_DATE_HOURS', 'ชั่วโมง');
DEFINE('_KUNENA_DATE_MINUTES', 'นาที');

// 1.0.5
DEFINE('_KUNENA_BBCODE_HIDE', 'The following is hidden from non registered users:');
DEFINE('_KUNENA_BBCODE_SPOILER', 'Warning Spoiler!');
DEFINE('_KUNENA_FORUM_SAME_ERR', 'Parent Forum must not be the same.');
DEFINE('_KUNENA_FORUM_OWNCHILD_ERR', 'Parent Forum is one of its own childs.');
DEFINE('_KUNENA_FORUM_UNKNOWN_ERR', 'Forum ID does not exist.');
DEFINE('_KUNENA_RECURSION', 'Recursion detected.');
DEFINE('_POST_FORGOT_NAME_ALERT', 'You forgot to enter your name.');
DEFINE('_POST_FORGOT_EMAIL_ALERT', 'You forgot to enter your email.');
DEFINE('_POST_FORGOT_SUBJECT_ALERT', 'You forgot to enter a subject.');
DEFINE('_KUNENA_EDIT_TITLE', 'แก้ไขข้อมูลเข้าระบบ');
DEFINE('_KUNENA_YOUR_NAME', 'Your Name:');
DEFINE('_KUNENA_EMAIL', 'e-mail:');
DEFINE('_KUNENA_UNAME', 'User Name:');
DEFINE('_KUNENA_PASS', 'Password:');
DEFINE('_KUNENA_VPASS', 'Verify Password:');
DEFINE('_KUNENA_USER_DETAILS_SAVE', 'User details have been saved.');
DEFINE('_KUNENA_TEAM_CREDITS', 'Credits');
DEFINE('_COM_A_BBCODE', 'BBCode');
DEFINE('_COM_A_BBCODE_SETTINGS', 'BBCode Settings');
DEFINE('_COM_A_SHOWSPOILERTAG', 'Show spoiler tag in editor toolbar');
DEFINE('_COM_A_SHOWSPOILERTAG_DESC', 'Set to &quot;Yes&quot; if you want the [spoiler] tag to be listed in the post editor\'s toolbar.');
DEFINE('_COM_A_SHOWVIDEOTAG', 'Show video tag in editor toolbar');
DEFINE('_COM_A_SHOWVIDEOTAG_DESC', 'Set to &quot;Yes&quot; if you want the [video] tag to be listed in the post editor\'s toolbar.');
DEFINE('_COM_A_SHOWEBAYTAG', 'Show eBay tag in editor toolbar');
DEFINE('_COM_A_SHOWEBAYTAG_DESC', 'Set to &quot;Yes&quot; if you want the [ebay] tag to be listed in the post editor\'s toolbar.');
DEFINE('_COM_A_TRIMLONGURLS', 'Trimm long URLs');
DEFINE('_COM_A_TRIMLONGURLS_DESC', 'Set to &quot;Yes&quot; if you want long URLs to be trimmed. See URL trim front and back settings.');
DEFINE('_COM_A_TRIMLONGURLSFRONT', 'Front portion of trimmed URLs');
DEFINE('_COM_A_TRIMLONGURLSFRONT_DESC', 'Number of characters for front portion of trimmed URLs. Trim long URLs must be set to &quot;Yes&quot;.');
DEFINE('_COM_A_TRIMLONGURLSBACK', 'Back portion of trimmed URLs');
DEFINE('_COM_A_TRIMLONGURLSBACK_DESC', 'Number of characters for back portion of trimmed URLs. Trim long URLs must be set to &quot;Yes&quot;.');
DEFINE('_COM_A_AUTOEMBEDYOUTUBE', 'Auto embed YouTube videos');
DEFINE('_COM_A_AUTOEMBEDYOUTUBE_DESC', 'Set to &quot;Yes&quot; if you want youtube video urls to get automatically embedded.');
DEFINE('_COM_A_AUTOEMBEDEBAY', 'Auto embed eBay items');
DEFINE('_COM_A_AUTOEMBEDEBAY_DESC', 'Set to &quot;Yes&quot; if you want eBay items and searches to get automatically embedded.');
DEFINE('_COM_A_EBAYLANGUAGECODE', 'eBay widget language code');
DEFINE('_COM_A_EBAYLANGUAGECODE_DESC', 'It is important to set the proper language code as the eBay widget derives both language and currency from it. Default is en-us for ebay.com. Examples: ebay.de: de-de, ebay.at: de-at, ebay.co.uk: en-gb');
DEFINE('_COM_A_KUNENA_SESSION_TIMEOUT', 'Session Lifetime');
DEFINE('_COM_A_KUNENA_SESSION_TIMEOUT_DESC', 'Default is 1800 [seconds]. Session lifetime (timeout) in seconds similar to Joomla session lifetime. The session lifetime is important for access rights recalculation, whoisonline display and NEW indicator. Once a session expires beyond that timeout, access rights and NEW indicator are reset.'); 

// 1.0.5RC2
DEFINE('_COM_A_HIGHLIGHTCODE', 'Enable Code Highlighting');
DEFINE('_COM_A_HIGHLIGHTCODE_DESC', 'Enables the FireBoard code tag highlighting java script. If your members post php and similar code fragments within code tags, turning this on will colorize the code. If your forum does not make use of such programing language posts, you might want to turn it off to avoid code tags from getting malformed.');
DEFINE('_COM_A_RSS_TYPE', 'Default RSS type');
DEFINE('_COM_A_RSS_TYPE_DESC', 'Choose between RSS feeds by thread or post. By thread means that only one entry per thread will be listed in the RSS feed, independet of how many posts have been made within that thread. By thread creates a shorter more compact RSS feed but will not list every reply made.');
DEFINE('_COM_A_RSS_BY_THREAD', 'By Thread');
DEFINE('_COM_A_RSS_BY_POST', 'By Post');
DEFINE('_COM_A_RSS_HISTORY', 'RSS History');
DEFINE('_COM_A_RSS_HISTORY_DESC', 'Select how much history should be included in the RSS feed. Default is 1 month but you might want to limit it to 1 week on high volume sites.');
DEFINE('_COM_A_RSS_HISTORY_WEEK', '1 Week');
DEFINE('_COM_A_RSS_HISTORY_MONTH', '1 Month');
DEFINE('_COM_A_RSS_HISTORY_YEAR', '1 Year');
DEFINE('_COM_A_FBDEFAULT_PAGE', 'Default FireBoard Page');
DEFINE('_COM_A_FBDEFAULT_PAGE_DESC', 'Select the default FireBoard page that gets displayed when a forum link is clicked or the forum is entered initially. Default is Recent Discussions. Should be set to Categories for templates other than default_ex. If My Discussions is selected, guests will default to Recent Discussions.');
DEFINE('_COM_A_FBDEFAULT_PAGE_RECENT', 'Recent Discussions');
DEFINE('_COM_A_FBDEFAULT_PAGE_MY', 'My Discussions');
DEFINE('_COM_A_FBDEFAULT_PAGE_CATEGORIES', 'Categories');
DEFINE('_KUNENA_POWEREDBY', 'ขอขอบคุณ');





?>
