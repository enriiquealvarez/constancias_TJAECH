$ErrorActionPreference = "Stop"
$outDir = "c:\laragon\www\constancias.tjaech.gob.mx\stitch_export"
if (!(Test-Path $outDir)) { New-Item -ItemType Directory -Force -Path $outDir }

$urls = @{
    "login.html" = "https://contribution.usercontent.google.com/download?c=CgthaWRhX2NvZGVmeBJ8Eh1hcHBfY29tcGFuaW9uX2dlbmVyYXRlZF9maWxlcxpbCiVodG1sXzI5MTIwZDk5OWIzMjQwYjk5YzZlNzA2ZTkyYmJkY2IwEgsSBxDflJXcsQYYAZIBJAoKcHJvamVjdF9pZBIWQhQxNjExNzI2ODQxNjU5MjgzNjE2Mw&filename=&opi=96797242"
    "dashboard.html" = "https://contribution.usercontent.google.com/download?c=CgthaWRhX2NvZGVmeBJ8Eh1hcHBfY29tcGFuaW9uX2dlbmVyYXRlZF9maWxlcxpbCiVodG1sXzcwODg0Y2FlMjI0ODQxMWY4N2ViYTlhMTY2NWIwZDAzEgsSBxDflJXcsQYYAZIBJAoKcHJvamVjdF9pZBIWQhQxNjExNzI2ODQxNjU5MjgzNjE2Mw&filename=&opi=96797242"
    "public.html" = "https://contribution.usercontent.google.com/download?c=CgthaWRhX2NvZGVmeBJ8Eh1hcHBfY29tcGFuaW9uX2dlbmVyYXRlZF9maWxlcxpbCiVodG1sX2ZmNWMwMzA5YzU2YjRkYmVhMDI4NDRkNzMyNWIxMjNkEgsSBxDflJXcsQYYAZIBJAoKcHJvamVjdF9pZBIWQhQxNjExNzI2ODQxNjU5MjgzNjE2Mw&filename=&opi=96797242"
}

foreach ($item in $urls.GetEnumerator()) {
    Write-Host "Downloading $($item.Name)..."
    Invoke-WebRequest -Uri $item.Value -OutFile "$outDir\$($item.Name)"
}
Write-Host "Done!"
