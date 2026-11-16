param(
    [Parameter(Mandatory = $true)]
    [string]$Url,

    [ValidateRange(1, 10000)]
    [int]$Iterations = 20,
    [ValidateRange(0, 1000)]
    [int]$Warmup = 3,
    [ValidateRange(1, 3600)]
    [int]$TimeoutSec = 30,
    [string]$CsvPath = '',
    [switch]$CsvAppend
)

$ErrorActionPreference = 'Stop'

# Windows PowerShell 5.1 draws a progress bar for every response, which took longer than the request itself: every measurement
# was about 0.5 s too high.
$ProgressPreference = 'SilentlyContinue'

# The number of SQL queries in the debug bar of Joomla (Debug System and the SQL queries of the debug plugin on): the debug bar
# has them as JSON, one {"sql":...} object for each query in "statements":[...].
function Get-QueryCount {
    param([string]$Content)

    $start = $Content.IndexOf('"statements":[')

    if ($start -lt 0) {
        return $null
    }

    return ([regex]::Matches($Content.Substring($start), '\{"sql":')).Count
}

function Invoke-MeasureRequest {
    param(
        [string]$TargetUrl,
        [int]$TimeoutSeconds
    )

    $statusCode = 0
    $content = ''
    $bytes = 0
    $failure = $null

    $stopwatch = [System.Diagnostics.Stopwatch]::StartNew()

    try {
        $response = Invoke-WebRequest -Uri $TargetUrl -Method Get -TimeoutSec $TimeoutSeconds -UseBasicParsing
        $statusCode = [int]$response.StatusCode
        $content = [string]$response.Content
        $bytes = [int]$response.RawContentLength
    }
    catch {
        # An error page (404, 500...) is measured too, with its status code, instead of stopping the run. Without an answer of
        # the server (timeout, refused) the status code is 0.
        $failure = $_.Exception.Message
        $errorResponse = $_.Exception.Response

        if ($errorResponse -and $errorResponse.StatusCode) {
            $statusCode = [int]$errorResponse.StatusCode
        }
    }

    $stopwatch.Stop()

    [PSCustomObject]@{
        StatusCode = $statusCode
        DurationMs = [math]::Round($stopwatch.Elapsed.TotalMilliseconds, 2)
        Bytes = $bytes
        QueryCount = Get-QueryCount $content
        Error = $failure
    }
}

Write-Host "Target URL: $Url"
Write-Host "Warmup: $Warmup request(s), Measured: $Iterations request(s)"
Write-Host ''

for ($i = 1; $i -le $Warmup; $i++) {
    [void](Invoke-MeasureRequest -TargetUrl $Url -TimeoutSeconds $TimeoutSec)
}

$results = @()
for ($i = 1; $i -le $Iterations; $i++) {
    $measurement = Invoke-MeasureRequest -TargetUrl $Url -TimeoutSeconds $TimeoutSec
    $measurement | Add-Member -NotePropertyName Iteration -NotePropertyValue $i
    $results += $measurement
}

$failed = @($results | Where-Object { $_.StatusCode -lt 200 -or $_.StatusCode -ge 400 })

if ($failed.Count -gt 0) {
    Write-Host ("{0} of {1} request(s) did not answer 2xx/3xx, status code(s): {2}" -f $failed.Count, $results.Count, (($failed | ForEach-Object { $_.StatusCode } | Sort-Object -Unique) -join ', ')) -ForegroundColor Yellow

    # Status 0: no answer could be read. Windows PowerShell 5.1 cannot read the error pages of Joomla ("The connection was closed
    # unexpectedly"); PowerShell 7 (pwsh) gives their status code.
    $noAnswer = @($failed | Where-Object { $_.StatusCode -eq 0 } | Select-Object -First 1)

    if ($noAnswer.Count -gt 0) {
        Write-Host ("No answer: {0}" -f $noAnswer[0].Error) -ForegroundColor Yellow

        if ($PSVersionTable.PSVersion.Major -lt 6) {
            Write-Host 'Run the script with pwsh (PowerShell 7) to get the status code of an error page.' -ForegroundColor Yellow
        }
    }

    Write-Host ''
}

$durations = @($results | Select-Object -ExpandProperty DurationMs)
$average = [math]::Round((($durations | Measure-Object -Average).Average), 2)
$minimum = [math]::Round((($durations | Measure-Object -Minimum).Minimum), 2)
$maximum = [math]::Round((($durations | Measure-Object -Maximum).Maximum), 2)
$p95Index = [math]::Ceiling($durations.Count * 0.95) - 1
$sortedDurations = @($durations | Sort-Object)
$p95 = $sortedDurations[[math]::Max(0, $p95Index)]

Write-Host 'Performance results'
Write-Host '-------------------'
Write-Host ("Avg ms: {0}" -f $average)
Write-Host ("Min ms: {0}" -f $minimum)
Write-Host ("Max ms: {0}" -f $maximum)
Write-Host ("P95 ms: {0}" -f ([math]::Round($p95, 2)))

$withQueryCounts = @($results | Where-Object { $null -ne $_.QueryCount })
if ($withQueryCounts.Count -gt 0) {
    $queryAvg = [math]::Round((($withQueryCounts | Measure-Object -Property QueryCount -Average).Average), 2)
    $queryMin = ($withQueryCounts | Measure-Object -Property QueryCount -Minimum).Minimum
    $queryMax = ($withQueryCounts | Measure-Object -Property QueryCount -Maximum).Maximum

    Write-Host ''
    Write-Host 'Detected query count (from response HTML)'
    Write-Host '----------------------------------------'
    Write-Host ("Avg queries: {0}" -f $queryAvg)
    Write-Host ("Min queries: {0}" -f $queryMin)
    Write-Host ("Max queries: {0}" -f $queryMax)
} else {
    Write-Host ''
    Write-Host 'No SQL queries found in the response.'
    Write-Host 'Turn on Debug System in the Joomla configuration and the SQL queries of the plugin "System - Debug" to count them.'
}

Write-Host ''
Write-Host 'Sample measurements (first 5)'
$results | Select-Object -First 5 | Format-Table -AutoSize | Out-String | Write-Host

if (-not [string]::IsNullOrWhiteSpace($CsvPath)) {
    $timestamp = (Get-Date).ToString('s')
    $csvRows = @()

    foreach ($row in $results) {
        $csvRows += [PSCustomObject]@{
            RowType = 'measurement'
            Timestamp = $timestamp
            Url = $Url
            Iteration = $row.Iteration
            StatusCode = $row.StatusCode
            DurationMs = $row.DurationMs
            Bytes = $row.Bytes
            QueryCount = $row.QueryCount
            Error = $row.Error
            AvgMs = $null
            MinMs = $null
            MaxMs = $null
            P95Ms = $null
            Iterations = $null
        }
    }

    $csvRows += [PSCustomObject]@{
        RowType = 'summary'
        Timestamp = $timestamp
        Url = $Url
        Iteration = $null
        StatusCode = $null
        DurationMs = $null
        Bytes = $null
        QueryCount = $null
        Error = $null
        AvgMs = $average
        MinMs = $minimum
        MaxMs = $maximum
        P95Ms = [math]::Round($p95, 2)
        Iterations = $Iterations
    }

    $csvDirectory = Split-Path -Parent $CsvPath

    if (-not [string]::IsNullOrWhiteSpace($csvDirectory) -and -not (Test-Path -LiteralPath $csvDirectory)) {
        New-Item -ItemType Directory -Path $csvDirectory -Force | Out-Null
    }

    $shouldAppend = $CsvAppend -and (Test-Path -LiteralPath $CsvPath)

    if ($shouldAppend) {
        $csvRows | Export-Csv -LiteralPath $CsvPath -NoTypeInformation -Append
    } else {
        $csvRows | Export-Csv -LiteralPath $CsvPath -NoTypeInformation
    }

    Write-Host ''
    Write-Host ("CSV output written: {0}" -f $CsvPath)
}


