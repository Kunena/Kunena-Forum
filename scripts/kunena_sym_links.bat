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
IF exist %GitTarget%\plugins\content\kunena ( rmdir /S/q %GitTarget%\plugins\content\kunena )
IF exist %GitTarget%\plugins\quickicon\kunena ( rmdir /S/q %GitTarget%\plugins\quickicon\kunena )
echo Put back kunena.xml file in place to allow to uninstall kunena
Md %GitTarget%\administrator\components\com_kunena
Copy %GitSource%\components\com_kunena\admin\kunena.xml %GitTarget%\administrator\components\com_kunena 
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
IF exist %GitTarget%\plugins\content\kunena ( rmdir /S/q %GitTarget%\plugins\content\kunena )
IF exist %GitTarget%\plugins\quickicon\kunena ( rmdir /S/q %GitTarget%\plugins\quickicon\kunena )

echo Make symbolic links
mklink /d %GitTarget%\administrator\components\com_kunena %GitSource%\components\com_kunena\admin
mklink /d %GitTarget%\components\com_kunena %GitSource%\components\com_kunena\site
mklink /d %GitTarget%\libraries\kunena %GitSource%\libraries\kunena
mklink /d %GitTarget%\plugins\system\kunena %GitSource%\plugins\plg_system_kunena
mklink /d %GitTarget%\plugins\content\kunena %GitSource%\plugins\plg_content_kunena
mklink /d %GitTarget%\plugins\quickicon\kunena %GitSource%\plugins\plg_quickicon_kunena
echo Copying media/kunena
xcopy /E /I %GitSource%\media\kunena\*.* %GitTarget%\media\kunena

pause
goto:eof 







