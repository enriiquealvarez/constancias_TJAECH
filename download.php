<?php
$dir = "c:/laragon/www/constancias.tjaech.gob.mx/stitch_export";
if (!is_dir($dir)) mkdir($dir, 0777, true);
$files = [
    'login.html' => 'https://contribution.usercontent.google.com/download?c=CgthaWRhX2NvZGVmeBJ8Eh1hcHBfY29tcGFuaW9uX2dlbmVyYXRlZF9maWxlcxpbCiVodG1sXzI5MTIwZDk5OWIzMjQwYjk5YzZlNzA2ZTkyYmJkY2IwEgsSBxDflJXcsQYYAZIBJAoKcHJvamVjdF9pZBIWQhQxNjExNzI2ODQxNjU5MjgzNjE2Mw&filename=&opi=96797242',
    'dashboard.html' => 'https://contribution.usercontent.google.com/download?c=CgthaWRhX2NvZGVmeBJ8Eh1hcHBfY29tcGFuaW9uX2dlbmVyYXRlZF9maWxlcxpbCiVodG1sXzcwODg0Y2FlMjI0ODQxMWY4N2ViYTlhMTY2NWIwZDAzEgsSBxDflJXcsQYYAZIBJAoKcHJvamVjdF9pZBIWQhQxNjExNzI2ODQxNjU5MjgzNjE2Mw&filename=&opi=96797242',
    'public.html' => 'https://contribution.usercontent.google.com/download?c=CgthaWRhX2NvZGVmeBJ8Eh1hcHBfY29tcGFuaW9uX2dlbmVyYXRlZF9maWxlcxpbCiVodG1sX2ZmNWMwMzA5YzU2YjRkYmVhMDI4NDRkNzMyNWIxMjNkEgsSBxDflJXcsQYYAZIBJAoKcHJvamVjdF9pZBIWQhQxNjExNzI2ODQxNjU5MjgzNjE2Mw&filename=&opi=96797242'
];
foreach($files as $name => $url) {
    echo "Downloading $name...\n";
    file_put_contents("$dir/$name", file_get_contents($url));
}
echo "Done\n";
