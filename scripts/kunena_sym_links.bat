@echo OFF

Set GitSource=
Set GitTarget=
Set OPT_DELETE=0
Set OPT_DELETE_ALL=0

echo:
echo Link Kunena development tree into your web site.
echo You need to define two variables before to launch the script
echo This script needs administrator rights to run correctly
echo:

SET /p GitSource=GIT repository in ........:
SET /p GitTarget=Joomla installation in ...:

pause

echo:
echo You have set the GIT repository in ........:  %GitSource%
echo You have set the Joomla installation in ...:  %GitTarget%
echo:

if exist %GitTarget%\configuration.php (
goto WHATTODO
) else (
echo You need to have a Joomla! installation to run this script
)

:WHATTODO
echo 1 : Make the symbolic links for Kunena with Kunena-Addons
echo 2 : Delete the symbolic links
echo 3 : Exit

set /p choice=What do-you want to do ? :
(
if %choice%==1 goto MKLINK
if %choice%==2 goto DELETESYM
if %choice%==3 exit
)
goto:eof

:DELETESYM
IF exist %GitTarget%\administrator\components\com_kunena ( rmdir /S/q %GitTarget%\administrator\components\com_kunena )
IF exist %GitTarget%\components\com_kunena ( rmdir /S/q %GitTarget%\components\com_kunena )
IF exist %GitTarget%\libraries\kunena ( rmdir /S/q %GitTarget%\libraries\kunena  )
IF exist %GitTarget%\media\kunena ( rmdir /S/q %GitTarget%\media\kunena )
IF exist %GitTarget%\plugins\system\kunena ( rmdir /S/q %GitTarget%\plugins\system\kunena )
IF exist %GitTarget%\plugins\quickicon\kunena ( rmdir /S/q %GitTarget%\plugins\quickicon\kunena )
IF exist %GitTarget%\plugins\kunena\altauserpoints ( rmdir /S/q %GitTarget%\plugins\kunena\altauserpoints )
IF exist %GitTarget%\plugins\kunena\community ( rmdir /S/q %GitTarget%\plugins\kunena\community )
IF exist %GitTarget%\plugins\kunena\comprofiler ( rmdir /S/q %GitTarget%\plugins\kunena\comprofiler )
IF exist %GitTarget%\plugins\kunena\easyprofile ( rmdir /S/q %GitTarget%\plugins\kunena\easyprofile )
IF exist %GitTarget%\plugins\kunena\easysocial ( rmdir /S/q %GitTarget%\plugins\kunena\easysocial )
IF exist %GitTarget%\plugins\kunena\finder ( rmdir /S/q %GitTarget%\plugins\kunena\finder )
IF exist %GitTarget%\plugins\finder\kunena ( rmdir /S/q %GitTarget%\plugins\finder\kunena )
IF exist %GitTarget%\plugins\kunena\gravatar ( rmdir /S/q %GitTarget%\plugins\kunena\gravatar )
IF exist %GitTarget%\plugins\kunena\joomla ( rmdir /S/q %GitTarget%\plugins\kunena\joomla )
IF exist %GitTarget%\plugins\kunena\kunena ( rmdir /S/q %GitTarget%\plugins\kunena\kunena )
echo Put back kunena.xml file in place to allow to uninstall kunena
Md %GitTarget%\administrator\components\com_kunena
Copy %GitSource%\src\administrator\components\com_kunena\kunena.xml %GitTarget%\administrator\components\com_kunena
echo Removed Kunena development tree from your web site.
IF exist %GitTarget%\modules\mod_kunenalatest ( rmdir /S/q %GitTarget%\modules\mod_kunenalatest )
IF exist %GitTarget%\modules\mod_kunenalogin ( rmdir /S/q %GitTarget%\modules\mod_kunenalogin )
IF exist %GitTarget%\modules\mod_kunenasearch ( rmdir /S/q %GitTarget%\modules\mod_kunenasearch )
IF exist %GitTarget%\modules\mod_kunenastats ( rmdir /S/q %GitTarget%\modules\mod_kunenastats )
IF exist %GitTarget%\plugins\search\kunena ( rmdir /S/q %GitTarget%\plugins\search\kunena )
IF exist %GitTarget%\plugins\content\kunenadiscuss ( rmdir /S/q %GitTarget%\plugins\content\kunenadiscuss )
echo Removed modules and plugins from Kunena-Addons
echo Please install Kunena Package to fix your site!
echo:
echo:
pause
goto:eof

:MKLINK
echo Delete existing directories for Kunena
IF exist %GitTarget%\administrator\components\com_kunena ( rmdir /S/q %GitTarget%\administrator\components\com_kunena )
IF exist %GitTarget%\components\com_kunena ( rmdir /S/q %GitTarget%\components\com_kunena )
IF exist %GitTarget%\libraries\kunena ( rmdir /S/q %GitTarget%\libraries\kunena  )
IF exist %GitTarget%\media\kunena ( rmdir /S/q %GitTarget%\media\kunena )
IF exist %GitTarget%\plugins\system\kunena ( rmdir /S/q %GitTarget%\plugins\system\kunena )
IF exist %GitTarget%\plugins\quickicon\kunena ( rmdir /S/q %GitTarget%\plugins\quickicon\kunena )
IF exist %GitTarget%\plugins\kunena\altauserpoints ( rmdir /S/q %GitTarget%\plugins\kunena\altauserpoints )
IF exist %GitTarget%\plugins\kunena\community ( rmdir /S/q %GitTarget%\plugins\kunena\community )
IF exist %GitTarget%\plugins\kunena\comprofiler ( rmdir /S/q %GitTarget%\plugins\kunena\comprofiler )
IF exist %GitTarget%\plugins\kunena\easyprofile ( rmdir /S/q %GitTarget%\plugins\kunena\easyprofile )
IF exist %GitTarget%\plugins\kunena\easysocial ( rmdir /S/q %GitTarget%\plugins\kunena\easysocial )
IF exist %GitTarget%\plugins\kunena\finder ( rmdir /S/q %GitTarget%\plugins\kunena\finder )
IF exist %GitTarget%\plugins\finder\kunena ( rmdir /S/q %GitTarget%\plugins\finder\kunena )
IF exist %GitTarget%\plugins\kunena\gravatar ( rmdir /S/q %GitTarget%\plugins\kunena\gravatar )
IF exist %GitTarget%\plugins\kunena\joomla ( rmdir /S/q %GitTarget%\plugins\kunena\joomla )
IF exist %GitTarget%\plugins\kunena\kunena ( rmdir /S/q %GitTarget%\plugins\kunena\kunena )
echo Delete existing directories for Kunena-Addons
IF exist %GitTarget%\modules\mod_kunenalatest ( rmdir /S/q %GitTarget%\modules\mod_kunenalatest )
IF exist %GitTarget%\modules\mod_kunenalogin ( rmdir /S/q %GitTarget%\modules\mod_kunenalogin )
IF exist %GitTarget%\modules\mod_kunenasearch ( rmdir /S/q %GitTarget%\modules\mod_kunenasearch )
IF exist %GitTarget%\modules\mod_kunenastats ( rmdir /S/q %GitTarget%\modules\mod_kunenastats )
IF exist %GitTarget%\plugins\search\kunena ( rmdir /S/q %GitTarget%\plugins\search\kunena )
IF exist %GitTarget%\plugins\content\kunenadiscuss ( rmdir /S/q %GitTarget%\plugins\content\kunenadiscuss )

echo Make symbolic links for Kunena
mklink /d %GitTarget%\administrator\components\com_kunena %GitSource%\src\admin
mklink /d %GitTarget%\components\com_kunena %GitSource%\src\components
mklink /d %GitTarget%\libraries\kunena %GitSource%\src\libraries\kunena
mklink /d %GitTarget%\plugins\system\kunena %GitSource%\src\plugins\plg_system_kunena
mklink /d %GitTarget%\plugins\quickicon\kunena %GitSource%\src\plugins\plg_quickicon_kunena
mklink /d %GitTarget%\plugins\kunena\altauserpoints %GitSource%\src\plugins\plg_kunena_altauserpoints
mklink /d %GitTarget%\plugins\kunena\community %GitSource%\src\plugins\plg_kunena_community
mklink /d %GitTarget%\plugins\kunena\comprofiler %GitSource%\src\plugins\plg_kunena_comprofiler
mklink /d %GitTarget%\plugins\kunena\easyprofile %GitSource%\src\plugins\plg_kunena_easyprofile
mklink /d %GitTarget%\plugins\kunena\easysocial %GitSource%\src\plugins\plg_kunena_easysocial
mklink /d %GitTarget%\plugins\kunena\finder %GitSource%\src\plugins\plg_kunena_finder
mklink /d %GitTarget%\plugins\finder\kunena %GitSource%\src\plugins\plg_kunena_kunena
mklink /d %GitTarget%\plugins\kunena\gravatar %GitSource%\src\plugins\plg_kunena_gravatar
mklink /d %GitTarget%\plugins\kunena\joomla %GitSource%\src\plugins\plg_kunena_joomla
mklink /d %GitTarget%\plugins\kunena\kunena %GitSource%\src\plugins\plg_kunena_kunena
mklink /d %GitTarget%\media\kunena %GitSource%\src\media\kunena
echo Make symbolic links for Kunena-Addons
IF exist %GitSource%\modules\kunenalatest ( mklink /d %GitTarget%\modules\mod_kunenalatest %GitSource%\modules\kunenalatest )
IF exist %GitSource%\modules\kunenalogin ( mklink /d %GitTarget%\modules\mod_kunenalogin %GitSource%\modules\kunenalogin )
IF exist %GitSource%\modules\kunenasearch ( mklink /d %GitTarget%\modules\mod_kunenasearch %GitSource%\modules\kunenasearch )
IF exist %GitSource%\modules\kunenastats ( mklink /d %GitTarget%\modules\mod_kunenastats %GitSource%\modules\kunenastats )
IF exist %GitSource%\plugins\search\kunena ( mklink /d %GitTarget%\plugins\search\kunena %GitSource%\plugins\search\kunena )
IF exist %GitSource%\plugins\content\kunenadiscuss ( mklink /d %GitTarget%\plugins\content\kunenadiscuss %GitSource%\plugins\content\kunenadiscuss )

pause
goto:eof







