<?php
$dir = new RecursiveDirectoryIterator('f:/admin_dashboard/Backups/3/food_app/Modules/Admin/resources/views');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/^.+\.blade\.php$/i', RecursiveRegexIterator::GET_MATCH);

$replaced = [];
foreach ($files as $file) {
    $filePath = $file[0];
    if (strpos($filePath, 'layouts\partials\header.blade.php') !== false || strpos($filePath, 'layouts/partials/header.blade.php') !== false) {
        continue;
    }

    $content = file_get_contents($filePath);

    // Match from <!-- Navbar --> or <header class="...bg-white shadow-sm ring-1 ring-gray-200 z-10 w-full"> 
    // down to </header>
    $pattern = '/(?:<!--\s*Navbar\s*-->\s*)?<header[^>]*>.*?<\/header>/is';

    if (preg_match($pattern, $content)) {
        $newContent = preg_replace($pattern, "@include('admin::layouts.partials.header')", $content);
        if ($newContent !== $content) {
            file_put_contents($filePath, $newContent);
            $replaced[] = basename($filePath);
        }
    }
}
echo 'Replaced header in: ' . implode(', ', $replaced) . PHP_EOL;
