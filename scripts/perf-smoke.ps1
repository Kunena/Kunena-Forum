param(
    [Parameter(Mandatory = $true)]
    [string]$Url,

    [int]$Iterations = 20,
    [int]$Warmup = 3,
    [int]$TimeoutSec = 30,
    [string]$CsvPath = '',
    [switch]$CsvAppend
)

$ErrorActionPreference = 'Stop'

function Invoke-MeasureRequest {
    param(
        [string]$TargetUrl,
        [int]$TimeoutSeconds
    )

    $stopwatch = [System.Diagnostics.Stopwatch]::StartNew()
    $response = Invoke-WebRequest -Uri $TargetUrl -Method Get -TimeoutSec $TimeoutSeconds -UseBasicParsing
    $stopwatch.Stop()

    $queryCount = $null

    # Try common debug-output patterns for SQL query counters.
    if ($response.Content -match '(?i)(\d+)\s+queries') {
        $queryCount = [int]$Matches[1]
    }

    [PSCustomObject]@{
        StatusCode = [int]$response.StatusCode
        DurationMs = [math]::Round($stopwatch.Elapsed.TotalMilliseconds, 2)
        Bytes = [int]$response.RawContentLength
        QueryCount = $queryCount
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

$durations = $results | Select-Object -ExpandProperty DurationMs
$average = [math]::Round((($durations | Measure-Object -Average).Average), 2)
$minimum = [math]::Round((($durations | Measure-Object -Minimum).Minimum), 2)
$maximum = [math]::Round((($durations | Measure-Object -Maximum).Maximum), 2)
$p95Index = [math]::Ceiling($durations.Count * 0.95) - 1
$sortedDurations = $durations | Sort-Object
$p95 = $sortedDurations[[math]::Max(0, $p95Index)]

Write-Host 'Performance results'
Write-Host '-------------------'
Write-Host ("Avg ms: {0}" -f $average)
Write-Host ("Min ms: {0}" -f $minimum)
Write-Host ("Max ms: {0}" -f $maximum)
Write-Host ("P95 ms: {0}" -f ([math]::Round($p95, 2)))

$withQueryCounts = $results | Where-Object { $_.QueryCount -ne $null }
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
    Write-Host 'No query-count marker detected in response.'
    Write-Host 'Enable Joomla debug SQL output if you want query-count tracking.'
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


