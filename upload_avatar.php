<?php
require_once 'config.php';
require_once 'functions.php';
require_login();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

csrf_verify();

// Check config
$account_id = getenv('R2_ACCOUNT_ID');
$api_token  = getenv('R2_API_TOKEN');
$bucket     = getenv('R2_BUCKET')     ?: 'productioncentral';
$public_url = rtrim(getenv('R2_PUBLIC_URL') ?: '', '/');

if (!$account_id || !$api_token || !$public_url) {
    http_response_code(500);
    echo json_encode(['error' => 'Storage not configured. Add R2 env vars to Railway.']);
    exit;
}

// Validate upload
if (empty($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
    $upload_errors = [
        UPLOAD_ERR_INI_SIZE  => 'File too large (server limit).',
        UPLOAD_ERR_FORM_SIZE => 'File too large.',
        UPLOAD_ERR_PARTIAL   => 'Upload incomplete.',
        UPLOAD_ERR_NO_FILE   => 'No file selected.',
    ];
    $code = $_FILES['avatar']['error'] ?? UPLOAD_ERR_NO_FILE;
    echo json_encode(['error' => $upload_errors[$code] ?? 'Upload failed.']);
    exit;
}

$file     = $_FILES['avatar'];
$max_size = 5 * 1024 * 1024; // 5MB

if ($file['size'] > $max_size) {
    echo json_encode(['error' => 'File must be under 5MB.']);
    exit;
}

// Validate mime type by reading actual file bytes
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime  = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

$allowed = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/gif'  => 'gif',
    'image/webp' => 'webp',
];

if (!isset($allowed[$mime])) {
    echo json_encode(['error' => 'Only JPG, PNG, GIF, and WebP images are allowed.']);
    exit;
}

$ext      = $allowed[$mime];
$filename = 'avatars/' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;
$content  = file_get_contents($file['tmp_name']);

// Upload via Cloudflare R2 API
$url = "https://api.cloudflare.com/client/v4/accounts/{$account_id}/r2/buckets/{$bucket}/objects/{$filename}";

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_CUSTOMREQUEST  => 'PUT',
    CURLOPT_POSTFIELDS     => $content,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => [
        'Authorization: Bearer ' . $api_token,
        'Content-Type: '         . $mime,
        'Content-Length: '       . strlen($content),
    ],
    CURLOPT_TIMEOUT => 30,
]);

$response    = curl_exec($ch);
$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error  = curl_error($ch);
curl_close($ch);

if ($http_status !== 200) {
    http_response_code(500);
    echo json_encode([
        'error'  => 'Upload failed (HTTP ' . $http_status . '). Check R2 credentials.',
        'detail' => $curl_error ?: $response,
    ]);
    exit;
}

// Save public URL to DB
$final_url = "{$public_url}/{$filename}";
$pdo->prepare('UPDATE users SET avatar_url = ? WHERE id = ?')
    ->execute([$final_url, $_SESSION['user_id']]);

echo json_encode(['url' => $final_url]);
