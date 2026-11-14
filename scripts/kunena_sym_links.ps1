<#
.SYNOPSIS
    Creates or deletes the symbolic links between a Joomla! site and the git clones of Kunena, Kunena-Addons and Blue Eagle.

.PARAMETER JoomlaPath
    The Joomla! installation directory. Asked for when it is not given.

.PARAMETER Action
    Menu (default) shows the menu. The other values run without a menu:
    Kunena, Addons, BlueEagle (make those links), All (Kunena, and the addons when -AddonsPath is given), Delete (delete all links).

.PARAMETER AddonsPath
    The Kunena-Addons git directory, for -Action Addons and All.

.PARAMETER BlueEaglePath
    The Blue Eagle git directory, for -Action BlueEagle.

.PARAMETER Yes
    Answer yes to the questions (replace real folders by links, delete the links). Needed to run without a person.

.PARAMETER DryRun
    Only show what would be done, change nothing.

.EXAMPLE
    .\kunena_sym_links.ps1 -JoomlaPath E:\www\site -Action Kunena -Yes

.EXAMPLE
    .\kunena_sym_links.ps1 -JoomlaPath E:\www\site -Action Delete -DryRun

.NOTES
    Exit code: 0 when all went well, 1 on a failure or a bad path.
    Creating symbolic links needs an administrator shell, or Windows Developer Mode.
    Deleting a link never touches the files it points to.
#>
param(
    [string]$JoomlaPath,
    [ValidateSet('Menu', 'Kunena', 'Addons', 'BlueEagle', 'All', 'Delete')]
    [string]$Action = 'Menu',
    [string]$AddonsPath,
    [string]$BlueEaglePath,
    [switch]$Yes,
    [switch]$DryRun
)

$ErrorActionPreference = 'Stop'
$script:failures = 0

# The git clone of Kunena is the parent of the directory of this script
$kunenaGitDir = Split-Path -Parent $PSScriptRoot
$script:addonsGitDir = $null
$script:blueEagleGitDir = $null
$script:joomlaInstallDir = $null

Write-Host "==================================================================="
Write-Host "============================ KUNENA ==============================="
Write-Host "==================================================================="
Write-Host " "
Write-Host "This script helps you to create symbolic links between your Joomla! directory and the git clone of Kunena and/or Kunena-Addons"
Write-Host "You need to have Kunena installed on your Joomla! installation before doing the symbolic links"
Write-Host "You need to launch this script in administrator (or with Developer Mode on) else it will fail to create symbolic links"
Write-Host "Kunena git clone: $kunenaGitDir"
if ($DryRun) { Write-Host "DRY RUN: nothing will be changed" -ForegroundColor Yellow }
Write-Host " "
Write-Host "==================================================================="

# True when this shell is allowed to create symbolic links
function Test-CanCreateSymlinks {
    $link = Join-Path ([System.IO.Path]::GetTempPath()) ("kunena-link-test-" + [guid]::NewGuid())
    $target = $link + "-target"
    try {
        New-Item -ItemType Directory -Path $target | Out-Null
        New-Item -ItemType SymbolicLink -Path $link -Target $target | Out-Null
        return $true
    }
    catch {
        return $false
    }
    finally {
        if (Test-Path -LiteralPath $link) { (Get-Item -LiteralPath $link -Force).Delete() }
        if (Test-Path -LiteralPath $target) { [System.IO.Directory]::Delete($target) }
    }
}

# Ask for the Joomla! directory until it looks like one
if (-not $JoomlaPath) {
    $JoomlaPath = Read-Host -Prompt "Please enter the path of your Joomla! installation directory where the symbolic links will be created"
}
$JoomlaPath = "$JoomlaPath".Trim().Trim('"')
if (-not $JoomlaPath) {
    Write-Host 'You have entered an empty path for the Joomla! dir'
    exit 1
}
if (-not (Test-Path -LiteralPath (Join-Path $JoomlaPath 'configuration.php'))) {
    Write-Host "No configuration.php in ${JoomlaPath}: this is not a Joomla! installation directory" -ForegroundColor Red
    exit 1
}
$script:joomlaInstallDir = (Resolve-Path -LiteralPath $JoomlaPath).Path

Write-Host "==================================================================="

# True for a symbolic link or a junction, false for a real file or folder
function Test-IsLink {
    param($Item)
    return [bool]($Item.Attributes -band [System.IO.FileAttributes]::ReparsePoint)
}

# Ask a yes/no question, -Yes answers it
function Confirm-Action {
    param([string]$Question)
    if ($Yes) { return $true }
    return ((Read-Host $Question) -eq 'y')
}

function Get-LinkTarget {
    param($Item)
    return "$(@($Item.Target)[0])"
}

function Format-FullPath {
    param([string]$Path)
    return [System.IO.Path]::GetFullPath($Path).TrimEnd('\', '/')
}

# One definition of a link: the group it belongs to, its path in Joomla! and what it points at
function New-LinkDef {
    param([string]$Group, [string]$RelativePath, [string]$Target)
    [pscustomobject]@{
        Group  = $Group
        Path   = Join-Path $script:joomlaInstallDir $RelativePath
        Target = $Target
    }
}

# First of the names that exists in the folder, so that both layouts of a repository work
function Find-FirstExisting {
    param([string]$Folder, [string[]]$Names)
    foreach ($name in $Names) {
        $candidate = Join-Path $Folder $name
        if (Test-Path -LiteralPath $candidate) { return $candidate }
    }
    return $null
}

# The one list of links. Create and delete both read it, in the order of $Groups.
function Get-LinkDefs {
    param([string[]]$Groups)

    $defs = @()
    foreach ($group in $Groups) {
        switch ($group) {
            'BlueEagle' {
                $defs += New-LinkDef 'BlueEagle' 'components\com_kunena\template\blue_eagle5' $script:blueEagleGitDir
            }
            'Kunena' {
                $src = Join-Path $kunenaGitDir 'src'
                $defs += New-LinkDef 'Kunena' 'administrator\components\com_kunena' (Join-Path $src 'admin')
                $defs += New-LinkDef 'Kunena' 'components\com_kunena' (Join-Path $src 'site')
                $defs += New-LinkDef 'Kunena' 'libraries\kunena' (Join-Path $src 'libraries\kunena')
                $defs += New-LinkDef 'Kunena' 'media\kunena' (Join-Path $src 'media\kunena')

                # plg_<group>_<name> is installed in plugins\<group>\<name>
                $pluginsDir = Join-Path $src 'plugins'
                if (Test-Path -LiteralPath $pluginsDir) {
                    foreach ($plugin in (Get-ChildItem -LiteralPath $pluginsDir -Directory -Filter 'plg_*')) {
                        if ($plugin.Name -match '^plg_([^_]+)_(.+)$') {
                            $defs += New-LinkDef 'Kunena' ("plugins\{0}\{1}" -f $Matches[1], $Matches[2]) $plugin.FullName
                        }
                    }
                }
            }
            'Addons' {
                $addons = $script:addonsGitDir
                foreach ($name in 'kunenalatest', 'kunenalogin', 'kunenasearch', 'kunenastats') {
                    $target = $null
                    if ($addons) { $target = Find-FirstExisting (Join-Path $addons 'modules') @("mod_$name", $name) }
                    $defs += New-LinkDef 'Addons' "modules\mod_$name" $target
                }
                $target = $null
                if ($addons) { $target = Find-FirstExisting (Join-Path $addons 'plugins\search') @('kunena') }
                $defs += New-LinkDef 'Addons' 'plugins\search\kunena' $target
                $target = $null
                if ($addons) { $target = Find-FirstExisting (Join-Path $addons 'plugins\content') @('kunenadiscuss') }
                $defs += New-LinkDef 'Addons' 'plugins\content\kunenadiscuss' $target
            }
        }
    }
    return $defs
}

# Links which older versions of this script made under another name
function Get-LegacyLinkPaths {
    $names = 'kunenalatest', 'kunenalogin', 'kunenasearch', 'kunenastats'
    return $names | ForEach-Object { Join-Path $script:joomlaInstallDir "modules\$_" }
}

# Remove a link, a folder or a file. A link is removed without ever following it.
function Remove-PathSafely {
    param($Item)
    if (Test-IsLink $Item) {
        $Item.Delete()
    }
    elseif ($Item.PSIsContainer) {
        # A file can be held for a moment by an editor, a virus scanner or the web server: Windows then stops the delete halfway
        # ("The directory is not empty"). Wait and try again, so that the folder is not left half deleted.
        # .NET deletes a link found inside the folder as a link, it does not follow it
        for ($attempt = 1; $attempt -le 5; $attempt++) {
            try {
                [System.IO.Directory]::Delete($Item.FullName, $true)
                return
            }
            catch {
                if (-not (Test-Path -LiteralPath $Item.FullName)) { return }
                Start-Sleep -Milliseconds 700
            }
        }

        # Last resort: the delete of the command prompt, which also removes read-only files
        cmd /c rmdir /s /q "$($Item.FullName)" 2>$null
        if (Test-Path -LiteralPath $Item.FullName) {
            throw "$($Item.FullName) could not be deleted, a file in it is in use. Stop Apache, close the editor which has the folder open and run this again."
        }
    }
    else {
        $Item.Delete()
    }
}

function Install-Links {
    param([string[]]$Groups)

    if (-not $DryRun -and -not (Test-CanCreateSymlinks)) {
        Write-Host "This shell cannot create symbolic links. Start PowerShell as administrator, or turn on Developer Mode." -ForegroundColor Red
        $script:failures++
        return
    }

    $toCreate = @()
    $toReplace = @()
    $stats = @{ Created = 0; Unchanged = 0; Skipped = 0; Failed = 0 }

    foreach ($def in (Get-LinkDefs $Groups)) {
        if (-not $def.Target -or -not (Test-Path -LiteralPath $def.Target)) {
            Write-Host "Skipped (nothing to link to): $($def.Path)" -ForegroundColor Yellow
            $stats.Skipped++
            continue
        }

        $item = Get-Item -LiteralPath $def.Path -Force -ErrorAction SilentlyContinue
        if ($item -and (Test-IsLink $item)) {
            if ((Format-FullPath (Get-LinkTarget $item)) -ieq (Format-FullPath $def.Target)) {
                $stats.Unchanged++
                continue
            }
        }
        elseif ($item) {
            $toReplace += $item
        }
        $toCreate += $def
    }

    # The installed copies of Kunena are real folders: they are replaced by links, so ask first
    $keep = @()
    if ($toReplace.Count -gt 0) {
        Write-Host "These real folders will be DELETED and replaced by a link:" -ForegroundColor Yellow
        $toReplace | ForEach-Object { Write-Host "  $($_.FullName)" }
        if (-not $DryRun -and -not (Confirm-Action "Replace these $($toReplace.Count) folders? (y/N)")) {
            $keep = $toReplace | ForEach-Object { $_.FullName }
        }
    }

    foreach ($def in $toCreate) {
        if ($keep -contains $def.Path) {
            Write-Host "Kept: $($def.Path)"
            $stats.Skipped++
            continue
        }
        try {
            if ($DryRun) {
                Write-Host "Would link $($def.Path) -> $($def.Target)"
                continue
            }
            $existing = Get-Item -LiteralPath $def.Path -Force -ErrorAction SilentlyContinue
            if ($existing) { Remove-PathSafely $existing }
            $parent = Split-Path -Parent $def.Path
            if (-not (Test-Path -LiteralPath $parent)) { New-Item -ItemType Directory -Path $parent | Out-Null }
            New-Item -ItemType SymbolicLink -Path $def.Path -Target $def.Target | Out-Null
            Write-Host "Linked $($def.Path) -> $($def.Target)"
            $stats.Created++
        }
        catch {
            Write-Host "Failed: $($def.Path): $($_.Exception.Message)" -ForegroundColor Red
            $stats.Failed++
            $script:failures++
        }
    }

    Write-Host ("{0} created, {1} already right, {2} skipped, {3} failed" -f $stats.Created, $stats.Unchanged, $stats.Skipped, $stats.Failed)
}

# Delete the symbolic links only, never the files they point to
function Remove-Links {
    Write-Host "Delete symbolic links of Kunena, Kunena-Addons and Blue Eagle"

    # Blue Eagle first: its link lives inside the com_kunena link
    $paths = @(Get-LinkDefs @('BlueEagle', 'Kunena', 'Addons') | ForEach-Object { $_.Path })
    $paths += Get-LegacyLinkPaths

    $links = @()
    foreach ($path in $paths) {
        $item = Get-Item -LiteralPath $path -Force -ErrorAction SilentlyContinue
        if ($item -and (Test-IsLink $item)) {
            $links += $item
        }
        elseif ($item) {
            Write-Host "Skipped (not a symbolic link): $($item.FullName)" -ForegroundColor Yellow
        }
    }

    if ($links.Count -eq 0) {
        Write-Host "No symbolic links found in $script:joomlaInstallDir"
        return
    }

    Write-Host "The following symbolic links will be deleted (the files they point to are kept):"
    $links | ForEach-Object { Write-Host "  $($_.FullName) -> $(Get-LinkTarget $_)" }

    if ($DryRun) { return }
    if (-not (Confirm-Action "Delete these $($links.Count) symbolic links? (y/N)")) {
        Write-Host "Nothing deleted."
        return
    }

    foreach ($link in $links) {
        try {
            # Delete() without recursion removes the link itself and never follows it
            $link.Delete()
            Write-Host "Deleted $($link.FullName)"
        }
        catch {
            Write-Host "Failed: $($link.FullName): $($_.Exception.Message)" -ForegroundColor Red
            $script:failures++
        }
    }
}

# Set versionname to GIT in the manifest of the package, as a UTF-8 file without BOM
function Set-GitVersionName {
    $pkgKunenaPath = Join-Path $script:joomlaInstallDir 'administrator\manifests\packages\pkg_kunena.xml'
    if (-not (Test-Path -LiteralPath $pkgKunenaPath)) { return }
    if ($DryRun) { Write-Host "Would set versionname to GIT in pkg_kunena.xml"; return }

    $xmlContent = [System.IO.File]::ReadAllText($pkgKunenaPath)
    $xmlContent = $xmlContent -replace '<versionname>.*?</versionname>', '<versionname>GIT</versionname>'
    [System.IO.File]::WriteAllText($pkgKunenaPath, $xmlContent, (New-Object System.Text.UTF8Encoding($false)))
    Write-Host "Updated versionname to GIT in pkg_kunena.xml"
}

# The full path of a git clone, or $null (with a message) when it is empty or does not exist
function Resolve-GitDir {
    param([string]$Path)
    $Path = "$Path".Trim().Trim('"')
    if (-not $Path) { return $null }
    if (-not (Test-Path -LiteralPath $Path)) {
        Write-Host "$Path does not exist" -ForegroundColor Red
        return $null
    }
    return (Resolve-Path -LiteralPath $Path).Path
}

# Ask for a git clone
function Read-GitDir {
    param([string]$Prompt)
    return Resolve-GitDir (Read-Host -Prompt $Prompt)
}

# Run one action without the menu
function Invoke-Action {
    param([string]$Name)

    switch ($Name) {
        'Kunena' {
            Install-Links @('Kunena')
            Set-GitVersionName
        }
        'Addons' {
            $script:addonsGitDir = Resolve-GitDir $AddonsPath
            if (-not $script:addonsGitDir) { Write-Host "-AddonsPath is needed for Addons" -ForegroundColor Red; $script:failures++; return }
            Install-Links @('Addons')
        }
        'BlueEagle' {
            $script:blueEagleGitDir = Resolve-GitDir $BlueEaglePath
            if (-not $script:blueEagleGitDir) { Write-Host "-BlueEaglePath is needed for BlueEagle" -ForegroundColor Red; $script:failures++; return }
            Install-Links @('BlueEagle')
        }
        'All' {
            Invoke-Action 'Kunena'
            if ($AddonsPath) { Invoke-Action 'Addons' }
        }
        'Delete' { Remove-Links }
    }
}

function Show-ChoicesMenu {
    Write-Host "=== Menu to choose what to do ==="
    Write-Host "1. Make the symbolic links for Kunena and Kunena-Addons"
    Write-Host "2. Make the symbolic links for Kunena"
    Write-Host "3. Make the symbolic links for Kunena-Addons"
    Write-Host "4. Make the symbolic links for blue eagle"
    Write-Host "5. Delete the symbolic links (Kunena, Kunena-Addons and blue eagle)"
    Write-Host "6. Exit"
}

# Without a menu: do the one action and give an exit code
if ($Action -ne 'Menu') {
    Invoke-Action $Action
    if ($script:failures -gt 0) { exit 1 }
    exit 0
}

Show-ChoicesMenu

# Start the menu loop
while ($true) {
    $choice = Read-Host "Select an operation (1-6):"

    # Validate user input
    if ($choice -match '^[1-6]$') {
        switch ($choice) {
            1 {
                Install-Links @('Kunena')
                Set-GitVersionName
                $script:addonsGitDir = Read-GitDir "Please enter the path of your Kunena-Addons git directory (empty to skip the addons)"
                if ($script:addonsGitDir) { Install-Links @('Addons') }
                Read-Host "Press Enter to continue..."
            }
            2 {
                Install-Links @('Kunena')
                Set-GitVersionName
                Read-Host "Press Enter to continue..."
            }
            3 {
                $script:addonsGitDir = Read-GitDir "Please enter the path of your Kunena-Addons git directory"
                if ($script:addonsGitDir) { Install-Links @('Addons') }
                Read-Host "Press Enter to continue..."
            }
            4 {
                $script:blueEagleGitDir = Read-GitDir "Please enter the path of your Blue Eagle git directory"
                if ($script:blueEagleGitDir) { Install-Links @('BlueEagle') }
                Read-Host "Press Enter to continue..."
            }
            5 {
                Remove-Links
                Read-Host "Press Enter to continue..."
            }
            6 { exit }  # Exit the loop when 'Exit' is selected
        }
    }
    else {
        Write-Host "Invalid input. Please select a valid option (1-6)."
        Start-Sleep -Seconds 2  # Pause for 2 seconds to display the message
    }

    # Redisplay the system operations menu
    Show-ChoicesMenu
}
