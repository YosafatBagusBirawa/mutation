param(
  [string]$CoverageDir = "coverage-xml"
)

# safe checks
if (-not (Test-Path $CoverageDir)) {
  Write-Error "Coverage directory '$CoverageDir' not found."
  exit 2
}

# back up original index.xml (only once)
$index = Join-Path $CoverageDir "index.xml"
if (-not (Test-Path $index)) {
  Write-Error "index.xml not found in $CoverageDir"
  exit 2
}
$backupIndex = "$index.bak"
if (-not (Test-Path $backupIndex)) {
  Copy-Item $index $backupIndex -Force
  Write-Host "Backed up index.xml -> index.xml.bak"
}

# Normalize index.xml: convert backslashes to forward slashes throughout
$indexContent = Get-Content $index -Raw
$indexContent = $indexContent -replace '\\','/'
Set-Content -Path $index -Value $indexContent -Encoding UTF8 -Force
Write-Host "Normalized index.xml (converted backslashes => /)"

# Process each per-file xml under coverage dir
Get-ChildItem -Path $CoverageDir -Recurse -Filter "*.php.xml" | ForEach-Object {
  $file = $_.FullName
  $bak = "$file.bak"
  if (-not (Test-Path $bak)) { Copy-Item $file $bak -Force }
  $content = Get-Content $file -Raw

  # convert all backslashes to forward slashes
  $content = $content -replace '\\','/'

  # remove leading slash after path= so path="/lib" -> path="lib"
  $content = $content -replace 'path="/+', 'path="'

  # write back
  Set-Content -Path $file -Value $content -Encoding UTF8
  Write-Host "Normalized $file"
}

Write-Host "Normalization finished. Backups are kept with .bak suffix."