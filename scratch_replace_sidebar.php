<?php
$dir = new RecursiveDirectoryIterator('f:/admin_dashboard/Backups/3/food_app/Modules/Admin/resources/views');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/^.+\.blade\.php$/i', RecursiveRegexIterator::GET_MATCH);

$replaced = [];
foreach ($files as $file) {
    $filePath = $file[0];
    if (strpos($filePath, 'layouts\partials\sidebar.blade.php') !== false || strpos($filePath, 'layouts/partials/sidebar.blade.php') !== false) {
        continue;
    }

    $content = file_get_contents($filePath);
    $pattern = '/(?:<!--\s*Sidebar\s*-->\s*)?(?:<!--\s*Mobile sidebar backdrop\s*-->\s*)?<div id=\"sidebarBackdrop\"[^>]*>.*?<\/aside>/is';

    if (preg_match($pattern, $content)) {
        $newContent = preg_replace($pattern, "@include('admin::layouts.partials.sidebar')", $content);
        if ($newContent !== $content) {
            file_put_contents($filePath, $newContent);
            $replaced[] = basename($filePath);
        }
    }
}
echo 'Replaced in: ' . implode(', ', $replaced) . PHP_EOL;
