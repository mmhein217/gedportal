<?php
// Test if questions API is working
session_start();
$_SESSION['user_id'] = 4; // student1
$_SESSION['role'] = 'student';

// Test Math questions
echo "<h2>Testing Math Questions API</h2>";
$url = "http://localhost/Pearson/api/questions.php?subject=math";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . session_id());
$response = curl_exec($ch);
curl_close($ch);

echo "<pre>";
$data = json_decode($response, true);
if ($data && $data['success']) {
    echo "✅ SUCCESS! Found " . count($data['data']['questions']) . " questions\n\n";
    echo "First question:\n";
    print_r($data['data']['questions'][0]);
} else {
    echo "❌ FAILED!\n";
    print_r($data);
}
echo "</pre>";

// Test Science questions
echo "<h2>Testing Science Questions API</h2>";
$url = "http://localhost/Pearson/api/questions.php?subject=science";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . session_id());
$response = curl_exec($ch);
curl_close($ch);

echo "<pre>";
$data = json_decode($response, true);
if ($data && $data['success']) {
    echo "✅ SUCCESS! Found " . count($data['data']['questions']) . " questions\n\n";
    echo "First question:\n";
    print_r($data['data']['questions'][0]);
} else {
    echo "❌ FAILED!\n";
    print_r($data);
}
echo "</pre>";
?>