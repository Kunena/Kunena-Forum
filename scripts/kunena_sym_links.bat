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
echo 1 : Make the symbolic links
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
IF exist %GitTarget%\plugins\kunena\uddeim ( rmdir /S/q %GitTarget%\plugins\kunena\uddeim )
echo Put back kunena.xml file in place to allow to uninstall kunena
Md %GitTarget%\administrator\components\com_kunena
Copy %GitSource%\src\administrator\components\com_kunena\kunena.xml %GitTarget%\administrator\components\com_kunena
echo Removed development tree from your web site.
echo Please install Kunena Package to fix your site!
echo:
echo:
pause
goto:eof

:MKLINK
echo Delete existing directories
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
IF exist %GitTarget%\plugins\kunena\uddeim ( rmdir /S/q %GitTarget%\plugins\kunena\uddeim )

echo Make symbolic links
mklink /d %GitTarget%\administrator\components\com_kunena %GitSource%\src\administrator\components\com_kunena
mklink /d %GitTarget%\components\com_kunena %GitSource%\src\components\com_kunena
mklink /d %GitTarget%\libraries\kunena %GitSource%\src\libraries\kunena
mklink /d %GitTarget%\plugins\system\kunena %GitSource%\src\plugins\plg_system_kunena
mklink /d %GitTarget%\plugins\quickicon\kunena %GitSource%\src\plugins\plg_quickicon_kunena
mklink /d %GitTarget%\plugins\kunena\altauserpoints %GitSource%\src\administrator\components\com_kunena\install\plugins\plg_kunena_altauserpoints
mklink /d %GitTarget%\plugins\kunena\community %GitSource%\src\administrator\components\com_kunena\install\plugins\plg_kunena_community
mklink /d %GitTarget%\plugins\kunena\comprofiler %GitSource%\src\administrator\components\com_kunena\install\plugins\plg_kunena_comprofiler
mklink /d %GitTarget%\plugins\kunena\easyprofile %GitSource%\src\administrator\components\com_kunena\install\plugins\plg_kunena_easyprofile
mklink /d %GitTarget%\plugins\kunena\easysocial %GitSource%\src\administrator\components\com_kunena\install\plugins\plg_kunena_easysocial
mklink /d %GitTarget%\plugins\kunena\finder %GitSource%\src\administrator\components\com_kunena\install\plugins\plg_kunena_finder
mklink /d %GitTarget%\plugins\finder\kunena %GitSource%\src\administrator\components\com_kunena\install\plugins\plg_finder_kunena
mklink /d %GitTarget%\plugins\kunena\gravatar %GitSource%\src\administrator\components\com_kunena\install\plugins\plg_kunena_gravatar
mklink /d %GitTarget%\plugins\kunena\joomla %GitSource%\src\administrator\components\com_kunena\install\plugins\plg_kunena_joomla
mklink /d %GitTarget%\plugins\kunena\kunena %GitSource%\src\administrator\components\com_kunena\install\plugins\plg_kunena_kunena
mklink /d %GitTarget%\plugins\kunena\uddeim %GitSource%\src\administrator\components\com_kunena\install\plugins\plg_kunena_uddeim
echo Copying media/kunena
xcopy /E /I %GitSource%\src\media\kunena\*.* %GitTarget%\media\kunena

pause
goto:eof







