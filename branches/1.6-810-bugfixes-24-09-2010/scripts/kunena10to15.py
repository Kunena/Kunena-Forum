import os,sys,re

dictionary = {
'sef[Rr]el[Tt]o[Aa]bs':'JRoute::_', 
'mosGetParam\(\s*\$_[A-Z]*, ':'JRequest::getVar(',
'writeLimitBox':'getLimitBox',
'writePagesLinks':'getPagesLinks',
'writePagesCounter':'getPagesCounter',
'\$mosConfig_live_site':'JURI::root()',
'\$mosConfig_lang':'$lang',
'$defined.*_VALID_MOS.*or die.*;':'defined( \'_JEXEC\' ) or die();',
'global\s*\$kunena_db;':'$kunena_db = &JFactory::getDBO();',
'(\s*)(global )\s*\$kunena_db,\s*(.*?;)':'\\1\\2\\3\n\\1$kunena_db = &JFactory::getDBO();',
'(\s*)(global .*?),\s*\$kunena_db;':'\\1\\2;\n\\1$kunena_db = &JFactory::getDBO();',
'\$mosConfig_absolute_path':'JPATH_ROOT',
'\$mosConfig_live_site':'JURI::root()',
'\$mainframe->getCfg\(\'live_site\'\)':'JURI::root()',
'\$mainframe->addCustomHeadTag':'$document->addCustomTag',
'\$kunena_db->loadObject\(\$(\w*)\);':'$\\1 = $kunena_db->loadObject();',
#'\$(\w+)\s*=\s*\$kunena_db->loadObject\(\);':'$kunena_db->loadObject($\\1);',
'\$mainframe->getCfg\(.absolute_path.\)\s*\.\s*(.)\/components\/com_fireboard':'JPATH_COMPONENT . \\1',
'\$mainframe->getCfg\(.absolute_path.\)\s*\.\s*(.)\/administrator\/components\/com_fireboard':'JPATH_COMPONENT_ADMINISTRATOR . \\1',
'\$mainframe->getCfg\(.absolute_path.\)':'JPATH_ROOT',
'mosHTML::makeOption\(':'JHTML::_(\'select.option\', ',
'mosHTML::selectList\(':'JHTML::_(\'select.genericlist\', ',
'mosRedirect\s*\(':'$mainframe->redirect(',
'mosMenuBar':'JToolBarHelper',
'mosUser\(':'JUser(',
'mosDBTable':'JTable',
#'\$my_id\s*=\s*\$my->id;':'$my = &JFactory::getUser();\n$my_id = $my->id;',
'\$mainframe->getCfg\(\'lang\'\)':'$lang',
'\$GLOBALS\[\'mosConfig_absolute_path\'\] \. \'\/administrator\/':'JPATH_COMPONENT_ADMINISTRATOR .DS. \'',
'mosHTML::integerSelectList\(':'JHTML::_(\'select.integerList\', ',
'(global .*),\sJPATH_ROOT(.*;)':'\\1\\2',
'(global .*),\sJURI::root\(\)(.*;)':'\\1\\2',
'require.*pageNavigation.php.*;':'jimport(\'joomla.html.pagination\');',
'\$pageNav(\w*)\s*=\s*new mosPageNav':'$pageNav\\1 = new JPagination',
', REQUEST':', \'REQUEST\'',
', COOKIE':', \'COOKIE\'',
#'JText::_\((\'.*\')\)':'\\1',
#'JPATH_ROOT\s*\.\s*(["\']/)':'KUNENA_ROOT_PATH .DS. \\1',
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
'\s*\.\s*\'/\'':' .DS',
'DS\s*\.\s*(["\'])/':'DS. \\1',
'DS\s*\.\s*DS\s*\.\s*':'DS. ',
#'\s*\.\s*DS\s*\.':' .DS.',
'KUNENA_JABSPATH\s*':'KUNENA_ROOT_PATH .DS',
'KUNENA_ABSPATH\s*':'KUNENA_PATH .DS',
'KUNENA_ABSSOURCESPATH\s*':'KUNENA_PATH_LIB .DS',
'KUNENA_LANG(\W)\s*':'KUNENA_LANGUAGE\\1 .DS',
'KUNENA_ABSADMPATH\s*':'KUNENA_PATH_ADMIN .DS',
'KUNENA_ABSUPLOADEDPATH\s*':'KUNENA_PATH_UPLOADED .DS',
'components/Kunena':'components/com_kunena',
'\$GLOBALS\[\'mosConfig_sitename\'\]':'$mainframe->getCfg(\'sitename\')',
'\$mosConfig_sitename':'$mainframe->getCfg(\'sitename\')',
'\$mosConfig_locale':'$mainframe->getCfg(\'locale\')',
'\$mostables':'$profileitems',
'mos[Mm]ail':'JUtility::sendMail',
'josSpoofValue':'JUtility::getToken',
'JUserParameters':'mosUserParameters',
'mosMakeHtmlSafe':'JFilterOutput::objectHTMLSafe',
'die\(\);':'$mainframe->close();',
'exit\(\);':'$mainframe->close();',
', _MOS_ALLOWRAW':'',
#'\$my_id':'$my->id',
#'global \$my;':'$my = &JFactory::getUser();',
#'(\s*)(global )\s*\$my,\s*(.*?;)':'\\1\\2\\3\n\\1$my = &JFactory::getUser();',
#'(\s*)(global .*?),\s*\$my;':'\\1\\2;\n\\1$my = &JFactory::getUser();',
'global \$acl;':'$acl = &JFactory::getACL();',
'(\s*)(global )\s*\$acl,\s*(.*?;)':'\\1\\2\\3\n\\1$acl = &JFactory::getACL();',
'(\s*)(global .*?),\s*\$acl;':'\\1\\2;\n\\1$acl = &JFactory::getACL();',
'mosNotAuth\(\)':'JError::raiseError( 403, JText::_("ALERTNOTAUTH") );',
'global\s*\$fbConfig;':'$fbConfig =& CKunenaConfig::getInstance();',
#'(\s*)(global )\s*\$fbConfig,\s*(.*?;)':'\\1\\2\\3\n\\1$fbConfig =& CKunenaConfig::getInstance();',
'(\s*)(global .*?),\s*\$fbConfig;':'\\1\\2;\n\\1$fbConfig =& CKunenaConfig::getInstance();',
'\$acl':'$kunena_acl',
'\$my(\W)':'$kunena_my\\1',
'\$database':'$kunena_db',
'mosCountModules\(':'JDocumentHTML::countModules(',
'\$GLOBALS\[\"fbConfig\"\]':'$fbConfig',
'mosParameters':'JParameter',
'\$aro_group->group_id':'$aro_group->id',
'\$kunena_myGraph':'$myGraph',
'global\s*\$mainframe;':'$app =& JFactory::getApplication();',
'(\s*)(global )\s*\$mainframe,\s*(.*?;)':'\\1\\2\\3\n\\1$app =& JFactory::getApplication();',
'(\s*)(global .*?),\s*\$mainframe\s*;':'\\1\\2;\n\\1$app =& JFactory::getApplication();',
'\$mainframe':'$app',
'(\s*)\$app->setPageTitle\((.*?)\);':'\n\\1$document=& JFactory::getDocument();\n\\1$document->setTitle(\\2);',
'\$kunena_my([\w_])':'$my\\1',
'\$app->redirect\( JURI::base\(\) \.':'$app->redirect(',

}

def string_replace(filename, text, dic):
    for i, j in dic.iteritems():
        (text, count) = re.subn(i, j, text)
	if count:
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
		if name[-4:] != '.php':
			continue
		if name == 'CHANGELOG.php':
			continue
		if name == 'kunena.defines.php':
			continue
		file_replace(root+'/'+name, dictionary)
