<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "Please log in first, then run this test.\n";
    echo "Or set a test user session:\n";
    echo "  \$_SESSION['user_id'] = 1; // Replace with actual user ID\n";
    exit();
}

echo "Testing Bet Placement API\n";
echo "========================\n\n";

$testPayload = [
    'match_id' => 12345, 
    'bet_type' => 'home_win',
    'amount' => 10.00
];

echo "Test Payload:\n";
print_r($testPayload);
echo "\n";

$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['SCRIPT_NAME'] = '/Footcast/php/api/bet.php';

ob_start();

try {
    include 'php/api/bet.php';
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

$output = ob_get_clean();

echo "API Response:\n";
echo $output . "\n";

$response = json_decode($output, true);
if ($response) {
    echo "\nDecoded Response:\n";
    print_r($response);
    
    if (isset($response['error'])) {
        echo "\nError Details:\n";
        echo "  Message: " . ($response['message'] ?? 'N/A') . "\n";
        echo "  Error: " . ($response['error'] ?? 'N/A') . "\n";
        if (isset($response['file'])) {
            echo "  File: " . $response['file'] . "\n";
            echo "  Line: " . $response['line'] . "\n";
        }
    }
} else {
    echo "\nResponse is not valid JSON\n";
    echo "Raw output length: " . strlen($output) . " bytes\n";
}
?>
