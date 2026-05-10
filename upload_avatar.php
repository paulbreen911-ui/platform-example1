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
$access_key = getenv('R2_ACCESS_KEY');
$secret_key = getenv('R2_SECRET_KEY');
$bucket     = getenv('R2_BUCKET')     ?: 'productioncentral';
$public_url = rtrim(getenv('R2_PUBLIC_URL') ?: '', '/');

if (!$account_id || !$access_key || !$secret_key || !$public_url) {
    http_response_code(500);
    echo json_encode(['error' => 'Storage not configured. Check R2 env vars in Railway.']);
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

// Validate mime type from file bytes — never trust $_FILES['type']
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

// ── AWS Signature V4 for R2 (S3-compatible) ───────────────────
$endpoint   = "https://{$account_id}.r2.cloudflarestorage.com";
$host       = "{$account_id}.r2.cloudflarestorage.com";
$region     = 'auto';
$service    = 's3';
$date       = gmdate('Ymd\THis\Z');
$date_short = gmdate('Ymd');

$content_sha256 = hash('sha256', $content);

// Canonical request
$canonical_uri     = "/{$bucket}/{$filename}";
$canonical_query   = '';
$canonical_headers = "content-type:{$mime}\nhost:{$host}\nx-amz-content-sha256:{$content_sha256}\nx-amz-date:{$date}\n";
$signed_headers    = 'content-type;host;x-amz-content-sha256;x-amz-date';

$canonical_request = implode("\n", [
    'PUT',
    $canonical_uri,
    $canonical_query,
    $canonical_headers,
    $signed_headers,
    $content_sha256,
]);

// String to sign
$credential_scope = "{$date_short}/{$region}/{$service}/aws4_request";
$string_to_sign   = implode("\n", [
    'AWS4-HMAC-SHA256',
    $date,
    $credential_scope,
    hash('sha256', $canonical_request),
]);

// Signing key
$k_date    = hash_hmac('sha256', $date_short, 'AWS4' . $secret_key, true);
$k_region  = hash_hmac('sha256', $region,     $k_date,              true);
$k_service = hash_hmac('sha256', $service,    $k_region,            true);
$k_signing = hash_hmac('sha256', 'aws4_request', $k_service,        true);
$signature = hash_hmac('sha256', $string_to_sign, $k_signing);

$auth_header = "AWS4-HMAC-SHA256 Credential={$access_key}/{$credential_scope}, SignedHeaders={$signed_headers}, Signature={$signature}";

// Upload to R2
$url = "{$endpoint}/{$bucket}/{$filename}";

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_CUSTOMREQUEST  => 'PUT',
    CURLOPT_POSTFIELDS     => $content,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => [
        'Authorization: '          . $auth_header,
        'Content-Type: '           . $mime,
        'Content-Length: '         . strlen($content),
        'x-amz-content-sha256: '   . $content_sha256,
        'x-amz-date: '             . $date,
        'Host: '                   . $host,
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
        'error'  => 'Upload failed (HTTP ' . $http_status . ').',
        'detail' => $curl_error ?: $response,
    ]);
    exit;
}

// Save to DB
$final_url = "{$public_url}/{$filename}";
$pdo->prepare('UPDATE users SET avatar_url = ? WHERE id = ?')
    ->execute([$final_url, $_SESSION['user_id']]);

echo json_encode(['url' => $final_url]);
