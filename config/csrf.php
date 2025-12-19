<?php
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function csrfToken(): string {
    return $_SESSION['csrf_token'];
}

function verifyCsrf(): void {
    $headers = getallheaders();
    if (
        empty($headers['X-CSRF-TOKEN']) ||
        !hash_equals($_SESSION['csrf_token'], $headers['X-CSRF-TOKEN'])
    ) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
        exit;
    }
}
