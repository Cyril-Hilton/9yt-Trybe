<?php
/**
 * 9yt !Trybe Image & Path Diagnostic
 * Purpose: Check why images are 404ing.
 */

echo "--- Path Investigation ---\n";
$currentDir = __DIR__;
echo "Current Directory (should be public_html): $currentDir\n";

$targetFile = 'storage/app/public/events/banners/jPQnKA8IKtsUcJZ6TbQnM1SiKX0HrPOo7hdAWJJ7.jpg';
$absTarget = $currentDir . '/' . $targetFile;

echo "Target File Path: $absTarget\n";
if (file_exists($absTarget)) {
    echo "SUCCESS: Physical file exists at expected location.\n";
} else {
    echo "ERROR: Physical file NOT FOUND at $absTarget\n";
    
    // Check if it's in a different spot
    echo "Searching for jPQnKA8IKtsUcJZ6TbQnM1SiKX0HrPOo7hdAWJJ7.jpg...\n";
    $cmd = "find " . escapeshellarg($currentDir) . " -name 'jPQnKA8IKtsUcJZ6TbQnM1SiKX0HrPOo7hdAWJJ7.jpg'";
    echo shell_exec($cmd);
}

echo "\n--- Folder Repair ---\n";
$folders = [
    'storage/app/public/events/banners',
    'storage/app/public/categories/icons',
    'storage/framework/views',
    'storage/framework/cache',
    'storage/framework/sessions',
];

foreach ($folders as $folder) {
    if (!file_exists($currentDir . '/' . $folder)) {
        echo "Creating missing folder: $folder\n";
        mkdir($currentDir . '/' . $folder, 0755, true);
    } else {
        echo "Folder exists: $folder\n";
    }
}

echo "\n--- Symlink Investigation ---\n";
$linksToCheck = [
    $currentDir . '/public/storage',
    $currentDir . '/storage' // cPanel often needs this in the root if serving from there
];

foreach ($linksToCheck as $link) {
    if (is_link($link)) {
        echo "Link exists: $link\n";
        echo "Points to: " . readlink($link) . "\n";
        if (file_exists(readlink($link))) {
            echo "   Status: VALID\n";
        } else {
            echo "   Status: BROKEN (Target does not exist)\n";
        }
    } else {
        echo "NOT A LINK: $link\n";
    }
}

echo "\n--- Web Server Root Check ---\n";
// Create a test file in PUBLIC and see if we can access it via URL
$testFile = 'public/path_test.txt';
file_put_contents($currentDir . '/' . $testFile, "Working from " . $currentDir . "/public");
echo "Created test file: $testFile\n";
echo "Try to access: https://9yttrybe.com/path_test.txt\n";
echo "If that fails, try: https://9yttrybe.com/public/path_test.txt\n";
