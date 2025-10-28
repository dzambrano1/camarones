# PowerShell script to remove old chat code from inventario_camarones.php
$file = "inventario_camarones.php"
$content = Get-Content $file -Raw -Encoding UTF8

# Find the start marker
$startMarker = "<!-- Poblacion camarones -->"
$endMarker = "</script>`n`n<style>"

# Find positions
$startPos = $content.IndexOf($startMarker) + $startMarker.Length
$endPos = $content.IndexOf($endMarker, $startPos)

if ($startPos -gt $startMarker.Length -and $endPos -gt $startPos) {
    # Calculate what to keep
    $before = $content.Substring(0, $startPos)
    $after = $content.Substring($endPos)
    
    # Add newlines to separate sections
    $newContent = $before + "`n`n" + $after
    
    # Backup original
    Copy-Item $file "$file.backup_$(Get-Date -Format 'yyyyMMdd_HHmmss')" -Force
    
    # Write new content
    [System.IO.File]::WriteAllText((Resolve-Path $file), $newContent, [System.Text.Encoding]::UTF8)
    
    Write-Host "Successfully removed old chat code!" -ForegroundColor Green
    Write-Host "Backup created: $file.backup_$(Get-Date -Format 'yyyyMMdd_HHmmss')" -ForegroundColor Yellow
} else {
    Write-Host "Could not find the markers to remove the code!" -ForegroundColor Red
}

