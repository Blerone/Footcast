<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/football_api.php';

echo "<h2>Matches API Test</h2>";
echo "<pre>";

echo "1. Checking API Configuration...\n";
if (FOOTBALL_API_KEY === '' || FOOTBALL_API_KEY === 'your_rapidapi_key_here' || FOOTBALL_API_KEY === 'YOUR_FOOTBALL_DATA_TOKEN_HERE') {
    echo "FAILED: API key not configured\n";
    echo "Please set FOOTBALL_API_KEY in .env or your environment\n\n";
} else {
    echo "API key is configured\n";
    echo "Key: " . substr(FOOTBALL_API_KEY, 0, 6) . "...\n\n";
}

echo "2. Testing getUpcomingMatches('PL', 30)...\n";
$result = getUpcomingMatches('PL', 30);

if (!$result['success']) {
    echo "FAILED: " . ($result['error'] ?? 'Unknown error') . "\n";
    if (isset($result['offline']) && $result['offline']) {
        echo "Network/Connection Issue\n";
    }
    if (isset($result['rate_limited']) && $result['rate_limited']) {
        echo "Rate Limited\n";
    }
} else {
    $matches = $result['matches'] ?? [];
    echo "Success!\n";
    echo "Matches found: " . count($matches) . "\n";
    if (count($matches) > 0) {
        echo "   First match:\n";
        $first = $matches[0];
        echo "   - Fixture ID: " . ($first['fixture']['id'] ?? 'N/A') . "\n";
        echo "   - Home: " . ($first['teams']['home']['name'] ?? 'N/A') . "\n";
        echo "   - Away: " . ($first['teams']['away']['name'] ?? 'N/A') . "\n";
    }
}

echo "\n3. Testing getLiveMatches('PL')...\n";
$liveResult = getLiveMatches('PL');
if ($liveResult['success']) {
    echo "Success! Live matches: " . count($liveResult['matches'] ?? []) . "\n";
} else {
    echo "" . ($liveResult['error'] ?? 'No live matches or error') . "\n";
}

echo "\n4. Testing getFinishedMatches('PL', 7)...\n";
$finishedResult = getFinishedMatches('PL', 7);
if ($finishedResult['success']) {
    echo "Success! Finished matches: " . count($finishedResult['matches'] ?? []) . "\n";
} else {
    echo "" . ($finishedResult['error'] ?? 'No finished matches or error') . "\n";
}

echo "\n=== Test Complete ===\n";
echo "</pre>";
