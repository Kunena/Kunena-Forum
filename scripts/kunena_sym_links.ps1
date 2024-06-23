Write-Host "==================================================================="
Write-Host "============================ KUNENA ==============================="
Write-Host "==================================================================="
Write-Host " "
Write-Host "This script helps you to create symbolic links between your Joomla! directory and the git clone of Kunena and/or Kunena-Addons"
Write-Host "You need to have Kunena installed on your Joomla! installation before doing the symbolic links"
Write-Host "You need to launch this script in administrator else it will fails to create symbolic links"
Write-Host " "
Write-Host "==================================================================="

# Prompt for to let user enter the path of his Joomla! install directory
$joomlaInstallDir = Read-Host -Prompt "Please enter the path of your Joomla! installation directory where the symbolic links will be created"

if (!$joomlaInstallDir) {
    Write-Host 'You have entered an empty path for the Joomla! dir'
    exit
}

Write-Host "==================================================================="

# Read the path of Kunena git from where this script is launched and remove scripts dir in the path
$kGitDir = Get-Location
$kunenaGitDir = $kGitDir.ToString()
$kunenaGitDir = $kunenaGitDir.Replace('scripts','')

# Define a function to display the system operations menu
function Show-ChoicesMenu {
    Write-Host "=== Menu to choose what to do ==="
    Write-Host "1. Make the symbolic links for Kunena and Kunena-Addons"
    Write-Host "2. Make the symbolic links for Kunena"
    Write-Host "3. Make the symbolic links for Kunena-Addons"
    Write-Host "4. Make the symbolic links for blue eagle"
    Write-Host "5. Exit"
}

# Display the menu initially
Show-ChoicesMenu

# Delete existing Kunena-Addons directories before to proceed
function delete-ExistingKunenaAddonsDir {
    Write-Host "Delete existing directories for Kunena-Addons before to proceed"
    
    $pathKunenalatest = $joomlaInstallDir + "\modules\mod_kunenalatest"
    if (Test-Path -Path ) {
        Remove-Item -LiteralPath $pathKunenalatest -Force -Recurse
    }

    $pathKunenalogin = $joomlaInstallDir + "\modules\mod_kunenalogin"
    if (Test-Path -Path ) {
        Remove-Item -LiteralPath $pathKunenalogin -Force -Recurse
    }

    $pathKunenasearch = $joomlaInstallDir + "\modules\mod_kunenasearch"
    if (Test-Path -Path $pathKunenasearch) {
        Remove-Item -LiteralPath $pathKunenasearch -Force -Recurse
    }

    $pathKunenastats = $joomlaInstallDir + "\modules\mod_kunenastats"
    if (Test-Path -Path ) {
        Remove-Item -LiteralPath $pathKunenastats -Force -Recurse
    }
    
    $pathPlgKunenasearch = $joomlaInstallDir + "\plugins\search\kunena"
    if (Test-Path -Path $pathPlgKunenasearch) {
        Remove-Item -LiteralPath $pathPlgKunenasearch -Force -Recurse
    }
    
    $pathKunenadiscuss = $joomlaInstallDir + "\plugins\content\kunenadiscuss"
    if (Test-Path -Path $pathKunenadiscuss) {
        Remove-Item -LiteralPath $pathKunenadiscuss -Force -Recurse
    }
}

# Create the symlinks only for Kunena-Addons
function make-SymLinksKunenaAddons {
    Write-Host "Make symbolic links for Kunena-Addons"
    
    $pathLatest = $joomlaInstallDir + "\modules\kunenalatest"
    $targetForLatest = $kunenaGitDir + "\modules\kunenalatest"
    if (Test-Path -Path $pathLatest) {
        New-Item -ItemType SymbolicLink -Path $pathLatest -Target $targetForLatest
    }
    
    $pathLogin = $joomlaInstallDir + "\modules\kunenalogin"
    $targetForLogin = $kunenaGitDir + "\modules\kunenalogin"
    if (Test-Path -Path $pathLogin) {
        New-Item -ItemType SymbolicLink -Path $pathLogin -Target $targetForLogin
    }
    
    $pathSearch = $joomlaInstallDir + "\modules\kunenasearch"
    $targetForSearch = $kunenaGitDir + "\modules\kunenasearch"
    if (Test-Path -Path $pathSearch) {
        New-Item -ItemType SymbolicLink -Path $pathSearch -Target $targetForSearch
    }
    
    $pathStats = $joomlaInstallDir + "\modules\kunenastats"
    $targetForStats = $kunenaGitDir + "\plugins\search\kunena"
    if (Test-Path -Path $pathStats) {
        New-Item -ItemType SymbolicLink -Path $pathStats -Target $targetForStats
    }
    
    $pathPlgSearch = $joomlaInstallDir + "\plugins\search\kunena"
    $targetForSearch = $kunenaGitDir + "\src\admin"
    if (Test-Path -Path $pathPlgSearch) {
        New-Item -ItemType SymbolicLink -Path $pathPlgSearch -Target $targetForSearch
    }
    
    $pathDiscuss = $joomlaInstallDir + "\plugins\content\kunenadiscuss"
    $targetForDiscuss = $kunenaGitDir + "\plugins\content\kunenadiscuss"
    if (Test-Path -Path $pathDiscuss) {
        New-Item -ItemType SymbolicLink -Path $pathDiscuss -Target $targetForDiscuss 
    }
}

# Delete existing Kunena directories before to proceed
function delete-ExistingKunenaDir {
    Write-Host "Delete existing directories for Kunena before to proceed"
    $path = $joomlaInstallDir + "\administrator\components\com_kunena"
    if (Test-Path -Path $path) {
        Remove-Item -LiteralPath $path -Force -Recurse
    }
    
    $path2 = $joomlaInstallDir + "\components\com_kunena"
    if (Test-Path -Path $path2) {
        Remove-Item -LiteralPath $path2 -Force -Recurse
    }
    
    $path3 = $joomlaInstallDir + "\libraries\kunena"
    if (Test-Path -Path $path3) {
        Remove-Item -LiteralPath $path3 -Force -Recurse
    }
    
    $path4 = $joomlaInstallDir + "\media\kunena"
    if (Test-Path -Path $path4) {
        Remove-Item -LiteralPath $path4 -Force -Recurse
    }
    
    $path5 = $joomlaInstallDir + "\plugins\system\kunena"
    if (Test-Path -Path $path5) {
        Remove-Item -LiteralPath $path5 -Force -Recurse
    }
    
    $path6 = $joomlaInstallDir + "\plugins\quickicon\kunena"
    if (Test-Path -Path $path6) {
        Remove-Item -LiteralPath $path6 -Force -Recurse
    }
    
    $path7 = $joomlaInstallDir + "\plugins\kunena\altauserpoints"
    if (Test-Path -Path $path7) {
        Remove-Item -LiteralPath $path7 -Force -Recurse
    }
    
    $path8 = $joomlaInstallDir + "\plugins\kunena\community"
    if (Test-Path -Path $path8) {
        Remove-Item -LiteralPath $path8 -Force -Recurse
    }
    
    $path9 = $joomlaInstallDir + "\plugins\kunena\comprofiler"
    if (Test-Path -Path $path9) {
        Remove-Item -LiteralPath $path9 -Force -Recurse
    }
    
    $path10 = $joomlaInstallDir + "\plugins\kunena\easyprofile"
    if (Test-Path -Path $path10) {
        Remove-Item -LiteralPath $path10 -Force -Recurse
    }
    
    $path11 = $joomlaInstallDir + "\plugins\kunena\easysocial"
    if (Test-Path -Path $path11) {
        Remove-Item -LiteralPath $path11 -Force -Recurse
    }
    
    $path12 = $joomlaInstallDir + "\plugins\kunena\finder"
    if (Test-Path -Path $path12) {
        Remove-Item -LiteralPath $path12 -Force -Recurse
    }
    
    $path13 = $joomlaInstallDir + "\plugins\finder\kunena"
    if (Test-Path -Path $path13) {
        Remove-Item -LiteralPath $path13 -Force -Recurse
    }
    
    $path14 = $joomlaInstallDir + "\plugins\kunena\gravatar"
    if (Test-Path -Path $path14) {
        Remove-Item -LiteralPath $path14 -Force -Recurse
    }
    
    $path15 = $joomlaInstallDir + "\plugins\kunena\uddeim"
    if (Test-Path -Path $path15) {
        Remove-Item -LiteralPath $path15 -Force -Recurse
    }
    
    $path16 = $joomlaInstallDir + "\plugins\privacy\kunena"
    if (Test-Path -Path $path16) {
        Remove-Item -LiteralPath $path16 -Force -Recurse
    }
    
    $path17 = $joomlaInstallDir + "\plugins\kunena\joomla"
    if (Test-Path -Path $path17) {
        Remove-Item -LiteralPath $path17 -Force -Recurse
    }
    
    $path18 = $joomlaInstallDir + "\plugins\kunena\kunena"
    if (Test-Path -Path $path18) {
        Remove-Item -LiteralPath $path18 -Force -Recurse
    }
    
    $path19 = $joomlaInstallDir + "\plugins\sampledata\kunena"
    if (Test-Path -Path $path19) {
        Remove-Item -LiteralPath $path19 -Force -Recurse
    }
}

# Create the symlinks only for Kunena component
function make-SymLinksKunena {
    Write-Host "Make symbolic links for Kunena component"
    $path1 = $joomlaInstallDir + "\administrator\components\com_kunena"
    $target = $kunenaGitDir + "\src\admin"  
    New-Item -ItemType SymbolicLink -Path $path1 -Target $target
    $path2 = $joomlaInstallDir + "\components\com_kunena"
    $target2 = $kunenaGitDir + "\src\site"
    New-Item -ItemType SymbolicLink -Path $path2 -Target $target2
    $path3 = $joomlaInstallDir + "\libraries\kunena"
    $target3 = $kunenaGitDir + "\src\libraries\kunena"
    New-Item -ItemType SymbolicLink -Path $path3 -Target $target3
    $path4 = $joomlaInstallDir + "\plugins\system\kunena"
    $target4 = $kunenaGitDir + "\src\plugins\plg_system_kunena"
    New-Item -ItemType SymbolicLink -Path $path4 -Target $target4
    $path5 = $joomlaInstallDir + "\plugins\quickicon\kunena"
    $target5 = $kunenaGitDir + "\src\plugins\plg_quickicon_kunena"
    New-Item -ItemType SymbolicLink -Path $path5 -Target $target5 
    $path6 = $joomlaInstallDir + "\plugins\kunena\altauserpoints"
    $target6 = $kunenaGitDir + "\src\plugins\plg_kunena_altauserpoints"
    New-Item -ItemType SymbolicLink -Path $path6 -Target $target6
    $path7 = $joomlaInstallDir + "\plugins\kunena\community"
    $target7 = $kunenaGitDir + "\src\plugins\plg_kunena_community"
    New-Item -ItemType SymbolicLink -Path $path7 -Target $target7
    $path8 = $joomlaInstallDir + "\plugins\kunena\comprofiler"
    $target8 = $kunenaGitDir + "\src\plugins\plg_kunena_comprofiler"
    New-Item -ItemType SymbolicLink -Path $path8 -Target $target8
    $path9 = $joomlaInstallDir + "\plugins\kunena\easyprofile"
    $target9 = $kunenaGitDir + "\src\plugins\plg_kunena_easyprofile"
    New-Item -ItemType SymbolicLink -Path $path9 -Target $target9
    $path10 = $joomlaInstallDir + "\plugins\kunena\easysocial"
    $target10 = $kunenaGitDir + "\src\plugins\plg_kunena_easysocial"
    New-Item -ItemType SymbolicLink -Path $path10 -Target $target10 
    $path11 = $joomlaInstallDir + "\plugins\kunena\finder"
    $target11 = $kunenaGitDir + "\src\plugins\plg_kunena_finder"
    New-Item -ItemType SymbolicLink -Path $path11 -Target $target11
    $path12 = $joomlaInstallDir + "\plugins\finder\kunena"
    $target12 = $kunenaGitDir + "\src\plugins\plg_finder_kunena"
    New-Item -ItemType SymbolicLink -Path $path12 -Target $target12
    $path13 = $joomlaInstallDir + "\plugins\kunena\gravatar"
    $target13 = $kunenaGitDir + "\src\plugins\plg_kunena_gravatar"
    New-Item -ItemType SymbolicLink -Path $path13 -Target $target13
    $path14 = $joomlaInstallDir + "\plugins\kunena\uddeim"
    $target14 = $kunenaGitDir + "\src\plugins\plg_kunena_uddeim"
    New-Item -ItemType SymbolicLink -Path $path14 -Target $target14
    $path15 = $joomlaInstallDir + "\plugins\kunena\joomla"
    $target15 = $kunenaGitDir + "\src\plugins\plg_kunena_joomla"
    New-Item -ItemType SymbolicLink -Path $path15 -Target $target15
    $path16 = $joomlaInstallDir + "\plugins\kunena\kunena"
    $target16 = $kunenaGitDir + "\src\plugins\plg_kunena_kunena"
    New-Item -ItemType SymbolicLink -Path $path16 -Target $target16
    $path17 = $joomlaInstallDir + "\plugins\sampledata\kunena"
    $target17 = $kunenaGitDir + "\src\plugins\plg_sampledata_kunena"
    New-Item -ItemType SymbolicLink -Path $path17 -Target $target17
    $path18 = $joomlaInstallDir + "\plugins\privacy\kunena"
    $target18 = $kunenaGitDir + "\src\plugins\plg_privacy_kunena"
    New-Item -ItemType SymbolicLink -Path $path18 -Target $target18
    $path19 = $joomlaInstallDir + "\media\kunena"
    $target19 = $kunenaGitDir + "\src\media\kunena"
    New-Item -ItemType SymbolicLink -Path $path19 -Target $target19
}

# Make synbolic links for Blue Eagle template
function make-SymLinksBlueEagle {
    Write-Host "Make symbolic links for Blue Eagle template"
    
    $pathJoomlaBlueEagle = $joomlaInstallDir + "\components\com_kunena\template\blue_eagle5"
    
    if (Test-Path -Path $pathJoomlaBlueEagle) {
        Remove-Item -LiteralPath $pathJoomlaBlueEagle -Force -Recurse
    }
    
    New-Item -ItemType SymbolicLink -Path $pathJoomlaBlueEagle -Target $blueEagleGitDir
}

# Start the menu loop
while ($true) {
    $choice = Read-Host "Select an operation (1-5):"
     
    # Validate user input
    if ($choice -match '^[1-5]$') {
        switch ($choice) {
            1 {
                # Make the symbolic links for Kunena and Kunena-Addons
                delete-ExistingKunenaDir
                make-SymLinksKunena
                delete-ExistingKunenaAddonsDir
                make-SymLinksKunenaAddons
                Read-Host "Press any key to continue..."                
            }
            2 {
                # Make the symbolic links for Kunena
                delete-ExistingKunenaDir
                make-SymLinksKunena
                Read-Host "Press any key to continue..."
            }
            3 {
                # Make the symbolic links for Kunena-Addons
                delete-ExistingKunenaAddonsDir
                make-SymLinksKunenaAddons
                Read-Host "Press any key to continue..."
            }
            4 {
                # Make the symbolic links for blue eagle                
                # Prompt for to let user enter the path of his Joomla! install directory
                $blueEagleGitDir = Read-Host -Prompt "Please enter the path of your Blue Eagle git directory"
                make-SymLinksBlueEagle
                Read-Host "Press any key to continue..."
            }
            5 { exit }  # Exit the loop when 'Exit' is selected
        }
    }
    else {
        Write-Host "Invalid input. Please select a valid option (1-5)."
        Start-Sleep -Seconds 2  # Pause for 2 seconds to display the message
    }
 
    # Redisplay the system operations menu
    Show-ChoicesMenu
}
