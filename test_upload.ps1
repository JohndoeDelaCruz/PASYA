$uri = "http://localhost:8000/admin/crops/test-import"
$filePath = "test_crop_import.csv"

$boundary = [System.Guid]::NewGuid().ToString()
$LF = "`r`n"

$fileBytes = [System.IO.File]::ReadAllBytes($filePath)
$fileEnc = [System.Text.Encoding]::GetEncoding('iso-8859-1').GetString($fileBytes)

$bodyLines = (
    "--$boundary",
    "Content-Disposition: form-data; name=`"csv_file`"; filename=`"test_crop_import.csv`"",
    "Content-Type: text/csv$LF",
    $fileEnc,
    "--$boundary--$LF"
) -join $LF

try {
    $response = Invoke-RestMethod -Uri $uri -Method Post -ContentType "multipart/form-data; boundary=$boundary" -Body $bodyLines
    Write-Output "Response: $($response | ConvertTo-Json)"
} catch {
    Write-Output "Error: $($_.Exception.Message)"
    Write-Output "Response: $($_.Exception.Response)"
}