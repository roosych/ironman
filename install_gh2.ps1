[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12

$url = "https://github.com/cli/cli/releases/download/v2.63.2/gh_2.63.2_windows_amd64.msi"
$output = "$env:USERPROFILE\Downloads\gh_installer.msi"

Write-Host "Downloading GitHub CLI to $output..."
try {
    Invoke-WebRequest -Uri $url -OutFile $output -UseBasicParsing
    Write-Host "Download complete!"
    Write-Host "Installing..."
    Start-Process msiexec.exe -ArgumentList "/i", "`"$output`"", "/passive" -Wait
    Write-Host "Installation complete!"
} catch {
    Write-Host "Error: $_"
}
