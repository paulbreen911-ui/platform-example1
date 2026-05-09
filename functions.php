<?php
// ============================================================
//  PRODUCTION CENTRAL — Shared Functions
//  Require this AFTER config.php on any page that needs it.
// ============================================================


// ── CSRF ─────────────────────────────────────────────────────

/**
 * Generate (or reuse session-scoped) CSRF token.
 * Stores in $_SESSION for simple stateless check, and optionally
 * in the DB for extra auditability.
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Emit a hidden CSRF input field.
 */
function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

/**
 * Validate the submitted CSRF token. Dies on failure.
 */
function csrf_verify(): void {
    $submitted = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $submitted)) {
        http_response_code(403);
        die('Invalid or missing CSRF token.');
    }
}


// ── RATE LIMITING ─────────────────────────────────────────────

/**
 * Check and increment rate limit.
 * Returns TRUE if the action is allowed, FALSE if limit exceeded.
 *
 * @param PDO    $pdo
 * @param string $key        Identifier, e.g. "login:{$_SERVER['REMOTE_ADDR']}"
 * @param string $action     e.g. 'login', 'register', 'forgot_password'
 * @param int    $max        Max attempts in window
 * @param int    $window_sec Window size in seconds
 */
function rate_limit_check(PDO $pdo, string $key, string $action, int $max = 5, int $window_sec = 300): bool {
    try {
        // Clean expired windows
        $pdo->prepare('DELETE FROM rate_limits WHERE window_start < NOW() - INTERVAL \'1 second\' * ?')
            ->execute([$window_sec]);

        $stmt = $pdo->prepare('SELECT attempts, window_start FROM rate_limits WHERE key = ? AND action = ?');
        $stmt->execute([$key, $action]);
        $row = $stmt->fetch();

        if (!$row) {
            // First attempt
            $pdo->prepare('INSERT INTO rate_limits (key, action, attempts, window_start) VALUES (?, ?, 1, NOW())')
                ->execute([$key, $action]);
            return true;
        }

        if ($row['attempts'] >= $max) {
            return false;
        }

        $pdo->prepare('UPDATE rate_limits SET attempts = attempts + 1 WHERE key = ? AND action = ?')
            ->execute([$key, $action]);
        return true;

    } catch (PDOException $e) {
        // Fail open — don't block users due to DB hiccups
        return true;
    }
}

/**
 * Reset rate limit (e.g. after successful login).
 */
function rate_limit_reset(PDO $pdo, string $key, string $action): void {
    try {
        $pdo->prepare('DELETE FROM rate_limits WHERE key = ? AND action = ?')->execute([$key, $action]);
    } catch (PDOException $e) { /* ignore */ }
}


// ── EMAIL ─────────────────────────────────────────────────────

/**
 * Send email via Resend API (https://resend.com).
 * Requires RESEND_API_KEY environment variable set in Railway.
 */
function send_email(string $to, string $subject, string $body): bool {
    $api_key = getenv('RESEND_API_KEY');

    if (!$api_key) {
        // Fallback: log to file if no API key configured
        $log = implode("\n", [
            '--- ' . date('Y-m-d H:i:s') . ' ---',
            'To: ' . $to,
            'Subject: ' . $subject,
            $body,
            '',
        ]);
        @file_put_contents('/tmp/mail.log', $log, FILE_APPEND);
        return false;
    }

    $payload = json_encode([
        'from'    => 'Production Central <noreply@productioncentral.org>',
        'to'      => [$to],
        'subject' => $subject,
        'text'    => $body,
    ]);

    $ch = curl_init('https://api.resend.com/emails');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . $api_key,
            'Content-Type: application/json',
        ],
        CURLOPT_TIMEOUT        => 10,
    ]);

    $response = curl_exec($ch);
    $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $status === 200 || $status === 201;
}

/**
 * Create a password-reset token, store it, and email the user.
 */
function send_password_reset(PDO $pdo, array $user): bool {
    $token = bin2hex(random_bytes(32));

    // Expire any previous tokens
    $pdo->prepare('UPDATE password_resets SET used = TRUE WHERE user_id = ? AND used = FALSE')
        ->execute([$user['id']]);

    $pdo->prepare('INSERT INTO password_resets (user_id, token) VALUES (?, ?)')
        ->execute([$user['id'], $token]);

    $link = 'https://www.productioncentral.org/reset_password.php?token=' . urlencode($token);
    $body = <<<TEXT
Hi {$user['username']},

Someone requested a password reset for your Production Central account.

Reset your password here (link expires in 1 hour):
{$link}

If you didn't request this, you can ignore this email — your password won't change.

— Production Central
TEXT;

    return send_email($user['email'], 'Reset your Production Central password', $body);
}

/**
 * Create an email-verification token and send it.
 */
function send_verification_email(PDO $pdo, array $user): bool {
    $token = bin2hex(random_bytes(32));

    $pdo->prepare('INSERT INTO email_verifications (user_id, token) VALUES (?, ?)')
        ->execute([$user['id'], $token]);

    $link = 'https://www.productioncentral.org/verify_email.php?token=' . urlencode($token);
    $body = <<<TEXT
Hi {$user['username']},

Welcome to Production Central! Please verify your email address:

{$link}

This link expires in 24 hours.

— Production Central
TEXT;

    return send_email($user['email'], 'Verify your Production Central email', $body);
}


// ── USER HELPERS ──────────────────────────────────────────────

/**
 * Fetch a user row by ID. Returns array or NULL.
 */
function get_user_by_id(PDO $pdo, int $id): ?array {
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}

/**
 * Require a logged-in session or redirect to login.
 */
function require_login(string $redirect = '/login.php'): void {
    if (empty($_SESSION['user_id'])) {
        header('Location: ' . $redirect);
        exit;
    }
}

/**
 * Get the IP address of the current request.
 */
function client_ip(): string {
    return $_SERVER['HTTP_X_FORWARDED_FOR']
        ?? $_SERVER['HTTP_CF_CONNECTING_IP']
        ?? $_SERVER['REMOTE_ADDR']
        ?? '0.0.0.0';
}

/**
 * Sanitise output for HTML contexts.
 */
function e(?string $s): string {
    return htmlspecialchars($s ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Format a timestamptz string as a human-friendly relative time.
 */
function time_ago(string $datetime): string {
    $diff = time() - strtotime($datetime);
    if ($diff < 60)      return 'just now';
    if ($diff < 3600)    return floor($diff / 60) . 'm ago';
    if ($diff < 86400)   return floor($diff / 3600) . 'h ago';
    if ($diff < 604800)  return floor($diff / 86400) . 'd ago';
    if ($diff < 2592000) return floor($diff / 604800) . 'w ago';
    return date('M j, Y', strtotime($datetime));
}

/**
 * Format post body: plain text only, line breaks preserved, URLs linked.
 * No HTML or markdown — all input is escaped before processing.
 */
function format_post(string $text): string {
    // Escape everything first
    $s = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

    // Auto-link http/https URLs (nofollow + new tab, no JS schemes possible)
    $s = preg_replace_callback(
        '/https?:\/\/[^\s\'"<>()\[\]]{3,}/i',
        function ($m) {
            $url = rtrim($m[0], '.,;:!?)');
            $display = mb_strlen($url) > 60 ? mb_substr($url, 0, 60) . '…' : $url;
            return '<a href="' . $url . '" target="_blank" rel="noopener noreferrer nofollow">' . $display . '</a>';
        },
        $s
    );

    $s = nl2br($s);
    return $s;
}
/**
 * Truncate text to $max chars, appending ellipsis.
 */
function truncate(string $text, int $max = 120): string {
    if (mb_strlen($text) <= $max) return $text;
    return mb_substr($text, 0, $max) . '…';
}
