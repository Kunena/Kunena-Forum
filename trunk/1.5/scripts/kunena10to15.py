import os,sys,re

dictionary = {

# Kunena 1.5 Defines
'JPATH_ROOT\s*\.\s*(["\']/)':'KUNENA_ROOT_PATH .DS. \\1',
'JPATH_ADMINISTRATOR\s*\.\s*(["\']/)':'KUNENA_ROOT_PATH_ADMIN .DS. \\1',
'KUNENA_ROOT_([A-Z]+)\s*\.\s*(["\'])/(\w+)':'KUNENA_ROOT_\\1 .DS. \\2\\3',
'KUNENA_PATH\s*\.\s*(["\'])/(\w+)':'KUNENA_PATH .DS. \\1\\2',
'KUNENA_PATH_([A-Z]+)\s*\.\s*(["\'])/(\w+)':'KUNENA_PATH_\\1 .DS. \\2\\3',
'KUNENA_([A-Z]+)_PATH\s*\.\s*(["\'])/(\w+)':'KUNENA_\\1_PATH .DS. \\2\\3',
'KUNENA_([A-Z]+)_PATH_([A-Z_]+)\s*\.\s*(["\'])/(\w+)':'KUNENA_\\1_PATH_\\2 .DS. \\3\\4',
'KUNENA_ROOT_PATH\s*\.DS\.\s*(["\'])components/com_kunena':'KUNENA_PATH .DS. \\1',
'KUNENA_PATH\s*\.DS\.\s*(["\'])lib':'KUNENA_PATH_LIB .DS. \\1',
'KUNENA_PATH\s*\.DS\.\s*(["\'])template':'KUNENA_PATH_TEMPLATE .DS. \\1',
'KUNENA_PATH\s*\.DS\.\s*(["\'])template/default':'KUNENA_PATH_TEMPLATE_DEFAULT .DS. \\1',
'KUNENA_PATH_TEMPLATE\s*\.DS\.\s*(["\'])default':'KUNENA_PATH_TEMPLATE_DEFAULT .DS. \\1',
'KUNENA_ROOT_PATH\s*\.DS\.\s*(["\'])administrator':'KUNENA_ROOT_PATH_ADMIN .DS. \\1',
'KUNENA_ROOT_PATH_ADMIN\s*\.DS\.\s*(["\'])components/com_kunena':'KUNENA_PATH_ADMIN .DS. \\1',
'KUNENA_PATH_ADMIN\s*\.DS\.\s*(["\'])lib':'KUNENA_PATH_ADMIN_LIB .DS. \\1',
'KUNENA_PATH_ADMIN\s*\.DS\.\s*(["\'])language':'KUNENA_PATH_ADMIN_LANGUAGE .DS. \\1',
'KUNENA_PATH_ADMIN\s*\.DS\.\s*(["\'])install':'KUNENA_PATH_ADMIN_INSTALL .DS. \\1',
'KUNENA_PATH_ADMIN\s*\.DS\.\s*(["\'])images':'KUNENA_PATH_ADMIN_IMAGES .DS. \\1',
'KUNENA_ROOT_PATH\s*\.DS\.\s*(["\'])images/fbfiles':'KUNENA_PATH_UPLOADED .DS. \\1',
'KUNENA_([A-Z_]+)\s*\.\s*""':'KUNENA_\\1',
'KUNENA_([A-Z_]+)\s*\.\s*\'\'':'KUNENA_\\1',
'KUNENA_([A-Z_]+)\s*\.DS.\s*""\s*':'KUNENA_\\1 .DS',
'KUNENA_([A-Z_]+)\s*\.DS.\s*\'\'\s*':'KUNENA_\\1 .DS',
'KUNENA_PATH_([A-Z_]+)\s*\.\s*(["\']\w)':'KUNENA_PATH_\\1 .DS. \\2',
'KUNENA_JABSPATH\s*':'KUNENA_ROOT_PATH .DS',
'KUNENA_ABSPATH\s*':'KUNENA_PATH .DS',
'KUNENA_ABSSOURCESPATH\s*':'KUNENA_PATH_LIB .DS',
'KUNENA_LANG(\W)\s*':'KUNENA_LANGUAGE\\1 .DS',
'KUNENA_ABSADMPATH\s*':'KUNENA_PATH_ADMIN .DS',
'KUNENA_ABSUPLOADEDPATH\s*':'KUNENA_PATH_UPLOADED .DS',

# Kunena 1.5 Template Header Files
'jb-header':'kunena-header',
'fb-footer':'kunena-footer',

# Kunena 1.5 Deprecated Functions
'CKunenaTools::isJoomla15\(\)':'true ',

# Kunena 1.5 Configuration
'global\s*\$fbConfig;':'$fbConfig =& CKunenaConfig::getInstance();',
#'(\s*)(global )\s*\$fbConfig,\s*(.*?;)':'\\1\\2\\3\n\\1$fbConfig =& CKunenaConfig::getInstance();',
'(\s*)(global .*?),\s*\$fbConfig;':'\\1\\2;\n\\1$fbConfig =& CKunenaConfig::getInstance();',
'\$GLOBALS\[\"fbConfig\"\]':'$fbConfig',

# Kunena 1.5 Variables
'\$mostables':'$profileitems',
'\$aro_group->group_id':'$aro_group->id',

# Joomla! 1.5 Database Access
'\$database':'$kunena_db',
'global\s*\$kunena_db;':'$kunena_db = &JFactory::getDBO();',
'(\s*)(global )\s*\$kunena_db,\s*(.*?;)':'\\1\\2\\3\n\\1$kunena_db = &JFactory::getDBO();',
'(\s*)(global .*?),\s*\$kunena_db;':'\\1\\2;\n\\1$kunena_db = &JFactory::getDBO();',
'\$kunena_db->loadObject\(\$(\w*)\);':'$\\1 = $kunena_db->loadObject();',

# Joomla! 1.5 ACL
'\$acl':'$kunena_acl',
'global \$kunena_acl;':'$kunena_acl = &JFactory::getACL();',
'(\s*)(global )\s*\$kunena_acl,\s*(.*?;)':'\\1\\2\\3\n\\1$kunena_acl = &JFactory::getACL();',
'(\s*)(global .*?),\s*\$kunena_acl;':'\\1\\2;\n\\1$kunena_acl = &JFactory::getACL();',

# Joomla! 1.5 Configuration
'\$GLOBALS\[\'mosConfig_absolute_path\'\] \. \'\/administrator\/':'JPATH_COMPONENT_ADMINISTRATOR .DS. \'',
'\$GLOBALS\[\'mosConfig_sitename\'\]':'$mainframe->getCfg(\'sitename\')',
'\$mainframe->getCfg\(\'live_site\'\)':'JURI::root()',
'\$mainframe->getCfg\(.absolute_path.\)\s*\.\s*(.)\/components\/com_fireboard':'JPATH_COMPONENT . \\1',
'\$mainframe->getCfg\(.absolute_path.\)\s*\.\s*(.)\/administrator\/components\/com_fireboard':'JPATH_COMPONENT_ADMINISTRATOR . \\1',
'\$mainframe->getCfg\(.absolute_path.\)':'JPATH_ROOT',
'\$mainframe->getCfg\(\'lang\'\)':'$lang',
'\$mosConfig_live_site':'JURI::root()',
'\$mosConfig_lang':'$lang',
'\$mosConfig_absolute_path':'JPATH_ROOT',
'\$mosConfig_live_site':'JURI::root()',
'\$mosConfig_sitename':'$mainframe->getCfg(\'sitename\')',
'\$mosConfig_locale':'$mainframe->getCfg(\'locale\')',

# Joomla! 1.5 Application
'\$mainframe':'$app',
'global\s*\$app;':'$app =& JFactory::getApplication();',
'(\s*)(global )\s*\$app,\s*(.*?;)':'\\1\\2\\3\n\\1$app =& JFactory::getApplication();',
'(\s*)(global .*?),\s*\$app\s*;':'\\1\\2;\n\\1$app =& JFactory::getApplication();',
'\$app->addCustomHeadTag':'$document->addCustomTag',
'(\s*)\$app->setPageTitle\((.*?)\);':'\n\\1$document=& JFactory::getDocument();\n\\1$document->setTitle(\\2);',

# Joomla! 1.5 User
'\$my(\W)':'$kunena_my\\1',
#'\$my_id\s*=\s*\$kunena_my->id;':'$kunena_my = &JFactory::getUser();\n$my_id = $kunena_my->id;',
'global \$kunena_my;':'$kunena_my = &JFactory::getUser();',
'(\s*)(global )\s*\$kunena_my,\s*(.*?;)':'\\1\\2\\3\n\\1$kunena_my = &JFactory::getUser();',
'(\s*)(global .*?),\s*\$kunena_my;':'\\1\\2;\n\\1$kunena_my = &JFactory::getUser();',
##'\$my_id':'$my->id',

# Joomla! 1.5 Page Navigation
'require.*pageNavigation.php.*;':'jimport(\'joomla.html.pagination\');',
'\$pageNav(\w*)\s*=\s*new mosPageNav':'$pageNav\\1 = new JPagination',

# Joomla 1.5 Misc Legacy Functions
'writeLimitBox':'getLimitBox',
'writePagesLinks':'getPagesLinks',
'writePagesCounter':'getPagesCounter',
'mosNotAuth\(\)':'JError::raiseError( 403, JText::_("ALERTNOTAUTH") );',
'mosRedirect\s*\(':'$mainframe->redirect(',
'mosMenuBar':'JToolBarHelper',
'mosUser\(':'JUser(',
'mosDBTable':'JTable',
'mos[Mm]ail':'JUtility::sendMail',
'mosCountModules\(':'JDocumentHTML::countModules(',
'mosParameters':'JParameter',
'mosMakeHtmlSafe':'JFilterOutput::objectHTMLSafe',
'mosHTML::makeOption\(':'JHTML::_(\'select.option\', ',
'mosHTML::selectList\(':'JHTML::_(\'select.genericlist\', ',
'mosHTML::integerSelectList\(':'JHTML::_(\'select.integerList\', ',
'sef[Rr]el[Tt]o[Aa]bs':'JRoute::_', 
'josSpoofValue':'JUtility::getToken',
'JUserParameters':'mosUserParameters',
'die\(\);':'$mainframe->close();',
'exit\(\);':'$mainframe->close();',
#'\$app->redirect\( JURI::base\(\) \.':'$app->redirect(',

# Joomla! 1.5 getVar()
'mosGetParam\(\s*\$_[A-Z]*, ':'JRequest::getVar(',
#', REQUEST':', \'REQUEST\'',
#', COOKIE':', \'COOKIE\'',
#', _MOS_ALLOWRAW':'',

'(global .*),\sJPATH_ROOT(.*;)':'\\1\\2',
'(global .*),\sJURI::root\(\)(.*;)':'\\1\\2',

'$defined.*_VALID_MOS.*or die.*;':'defined( \'_JEXEC\' ) or die(\'Restricted access\');',

#'\s*\.\s*\'/\'':' .DS',
'DS\s*\.\s*(["\'])/':'DS. \\1',
#'DS\s*\.\s*DS\s*\.\s*':'DS. ',
##'\s*\.\s*DS\s*\.':' .DS.',
}

def string_replace(filename, text, dic):
    go = 1;
    while (go == 1):
        go = 0;
        for i, j in dic.iteritems():
            (text, count) = re.subn(i, j, text)
	    if count:
                go = 1
		print "%s: replaced %dx '%s' with '%s'" % (filename, count, i.replace('\n','\\n').replace('\r','\\r'), j.replace('\n','\\n'.replace('\r','\\r')))
    return text

def file_replace(filename, dic):
    f = open(filename, 'rb')
    file = f.read()
    f.close()
  
    txt = string_replace(filename, file, dic)

    f = open(filename, 'wb')
    f.write(txt)
    f.close()


if len(sys.argv)<2:
	print "Usage: python %s directory" % (sys.argv[0])
	sys.exit()

dir = sys.argv[1]

for root, dirs, files in os.walk(dir):
	for name in files:
		if name[-4:] != '.php' and name[-5:] != '.html':
			continue
		if name == 'CHANGELOG.php':
			continue
		if name == 'kunena.defines.php':
			continue
		file_replace(root+'/'+name, dictionary)

