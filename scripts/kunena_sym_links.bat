@echo OFF

Set GitSource=
Set GitTarget=
Set OPT_DELETE=0
Set OPT_DELETE_ALL=0

echo:
echo Link Kunena development tree into your web site.
echo You need to define two variables before to launch the script
echo This script needs administrator to run correctly
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

set /p choice=What do-you want to do ? :
(
if %choice%==1 goto MKLINK
if %choice%==2 goto DELETESYM
)
goto:eof  

:DELETESYM
IF exist %GitTarget%\administrator\components\com_kunena ( rmdir /S %GitTarget%\administrator\components\com_kunena )
IF exist %GitTarget%\components\com_kunena ( rmdir /S %GitTarget%\components\com_kunena )
IF exist %GitTarget%\libraries\kunena ( rmdir /S %GitTarget%\libraries\kunena  )
IF exist %GitTarget%\media\kunena ( rmdir /S %GitTarget%\media\kunena )
IF exist %GitTarget%\plugins\system\kunena ( rmdir /S %GitTarget%\plugins\system\kunena )
IF exist %GitTarget%\plugins\content\kunena ( rmdir /S %GitTarget%\plugins\content\kunena )
IF exist%GitTarget%\plugins\quickicon\kunena ( rmdir /S %GitTarget%\plugins\quickicon\kunena )
echo Done!
pause
goto:eof 

:MKLINK
echo Delete existing directories
rmdir /S %GitTarget%\administrator\components\com_kunena
rmdir /S %GitTarget%\components\com_kunena
rmdir /S %GitTarget%\libraries\kunena %GitSource%\libraries\kunena
rmdir /S %GitTarget%\media\kunena
rmdir /S %GitTarget%\plugins\system\kunena
rmdir /S %GitTarget%\plugins\content\kunena
rmdir/S %GitTarget%\plugins\quickicon\kunena
echo Make symbolic links
mklink /d %GitTarget%\administrator\components\com_kunena %GitSource%\components\com_kunena\admin
mklink /d %GitTarget%\components\com_kunena %GitSource%\components\com_kunena\site
mklink /d %GitTarget%\libraries\kunena %GitSource%\libraries\kunena
mklink /d %GitTarget%\media\kunena %GitSource%\media\kunena
mklink /d %GitTarget%\plugins\system\kunena %GitSource%\plugins\plg_content_kunena
mklink /d %GitTarget%\plugins\content\kunena %GitSource%\plugins\plg_quickicon_kunena
mklink /d %GitTarget%\plugins\quickicon\kunena %GitSource%\Kunena-forum\plugins\plg_system_kunena
pause
goto:eof 






