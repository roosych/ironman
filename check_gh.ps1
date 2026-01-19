# Refresh PATH
$env:Path = [System.Environment]::GetEnvironmentVariable("Path","Machine") + ";" + [System.Environment]::GetEnvironmentVariable("Path","User")

# Try to find gh.exe
$ghPath = Get-Command gh -ErrorAction SilentlyContinue
if ($ghPath) {
    Write-Host "Found: $($ghPath.Source)"
    gh --version
} else {
    # Search common locations
    $locations = @(
        "C:\Program Files\GitHub CLI\gh.exe",
        "C:\Program Files (x86)\GitHub CLI\gh.exe",
        "$env:LOCALAPPDATA\Programs\GitHub CLI\gh.exe"
    )

    foreach ($loc in $locations) {
        if (Test-Path $loc) {
            Write-Host "Found at: $loc"
            & $loc --version
            exit
        }
    }

    Write-Host "gh CLI not found. Please install manually from https://cli.github.com/"
}
