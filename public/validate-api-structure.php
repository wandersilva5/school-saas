<?php
// Validate API folder structure and files

echo "<h1>API Structure Validation</h1>";

// Check API Controllers directory
$apiDir = __DIR__ . '/../app/Controllers/Api';
echo "<h2>API Controllers Directory</h2>";
if (is_dir($apiDir)) {
    echo "✅ Directory exists: " . htmlspecialchars($apiDir) . "<br>";
    
    // List files in the directory
    echo "<h3>Files in API Controllers Directory:</h3>";
    echo "<ul>";
    foreach (scandir($apiDir) as $file) {
        if ($file !== '.' && $file !== '..') {
            echo "<li>" . htmlspecialchars($file) . "</li>";
        }
    }
    echo "</ul>";
} else {
    echo "❌ Directory does not exist: " . htmlspecialchars($apiDir) . "<br>";
    echo "Create this directory and place your API controllers there.";
}

// Check specific controller files
$controllers = [
    'ApiBaseController.php',
    'AuthController.php'
];

echo "<h2>Required Controller Files</h2>";
echo "<ul>";
foreach ($controllers as $controller) {
    $path = $apiDir . '/' . $controller;
    if (file_exists($path)) {
        echo "<li>✅ " . htmlspecialchars($controller) . " exists</li>";
    } else {
        echo "<li>❌ " . htmlspecialchars($controller) . " is missing</li>";
    }
}
echo "</ul>";

// Check if API routes file exists
$apiRoutesFile = __DIR__ . '/../routes/api.php';
echo "<h2>API Routes File</h2>";
if (file_exists($apiRoutesFile)) {
    echo "✅ File exists: " . htmlspecialchars($apiRoutesFile) . "<br>";
    
    // Show first few lines of the file
    echo "<h3>Routes File Content (first 20 lines):</h3>";
    echo "<pre>";
    $lines = file($apiRoutesFile);
    for ($i = 0; $i < min(10, count($lines)); $i++) {
        echo htmlspecialchars($lines[$i]);
    }
    echo "</pre>";
} else {
    echo "❌ File does not exist: " . htmlspecialchars($apiRoutesFile) . "<br>";
}