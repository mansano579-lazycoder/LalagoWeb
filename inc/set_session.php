<?php
// inc/set_session.php - UPDATED VERSION

// Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Force JSON response
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Get raw POST data
$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
$postData = [];

if (strpos($contentType, 'application/json') !== false) {
    $input = file_get_contents('php://input');
    $postData = json_decode($input, true);
} else {
    $postData = $_POST;
}

// Validate inputs
$uid = $postData['uid'] ?? '';
$email = $postData['email'] ?? '';
$displayName = $postData['displayName'] ?? '';
$firstName = $postData['firstName'] ?? '';
$lastName = $postData['lastName'] ?? '';

if (empty($uid) || empty($email)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing required fields: UID and email'
    ]);
    exit();
}

// Clear old session and regenerate ID for security
session_regenerate_id(true);

// SET ALL POSSIBLE SESSION VARIABLES FOR COMPATIBILITY
// Your cart.php looks for these:
$_SESSION['user_id'] = $uid;          // MOST IMPORTANT - cart.php checks this!
$_SESSION['uid'] = $uid;              // Your current set
$_SESSION['user_email'] = $email;     // cart.php looks for this
$_SESSION['email'] = $email;          // Your current set

// Set user name - cart.php looks for this
if (!empty($displayName)) {
    $_SESSION['user_name'] = $displayName;
} elseif (!empty($firstName) && !empty($lastName)) {
    $_SESSION['user_name'] = $firstName . ' ' . $lastName;
} else {
    $_SESSION['user_name'] = explode('@', $email)[0];
}

// Set firebase_user array - cart.php checks this too
$_SESSION['firebase_user'] = [
    'uid' => $uid,
    'id' => $uid, // Both for compatibility
    'email' => $email,
    'displayName' => $displayName,
    'firstName' => $firstName,
    'lastName' => $lastName,
    'first_name' => $firstName,
    'last_name' => $lastName
];

// Set timestamps
$_SESSION['login_time'] = time();
$_SESSION['session_start'] = date('Y-m-d H:i:s');

// Force session write
session_write_close();

// Debug log (remove in production)
error_log("Session set - user_id: $uid, email: $email");

// Success response
echo json_encode([
    'status' => 'success',
    'message' => 'Session created successfully',
    'session_data' => [
        'user_id' => $uid,
        'user_email' => $email,
        'user_name' => $_SESSION['user_name']
    ],
    'session_id' => session_id()
]);
exit();