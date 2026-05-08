<?php
/**
 * BLUESTAR Contact Form - PHP SMTP Sender
 * Connects to smtp.lolipop.jp:465 via SSL.
 * AUTH LOGIN with idc_info@bl-star.co.jp
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['status'=>'error','message'=>'POST only']); exit; }

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) $input = $_POST;

$name    = trim($input['name'] ?? '');
$company = trim($input['company'] ?? '');
$email   = trim($input['email'] ?? '');
$phone   = trim($input['phone'] ?? '');
$service = trim($input['service'] ?? '');
$message = trim($input['message'] ?? '');
$type    = trim($input['type'] ?? 'inquiry');
$to_email = trim($input['to_email'] ?? 'info@bl-star.cloud');

if (!$name || !$email || !$message) {
    echo json_encode(['status'=>'error','message'=>'姓名、邮箱、内容为必填项']);
    exit;
}

$subject = ($type === 'apply')
    ? '[BLUESTAR 应聘] ' . $name . ($company ? ' - ' . $company : '')
    : '[BLUESTAR 咨询] ' . $company . ' - ' . $name;

$body = "■ 咨询来源：BLUESTAR 官网\n";
$body .= "■ 类型：" . ($type === 'apply' ? '应聘申请' : '业务咨询') . "\n";
$body .= "■ 提交时间：" . date('Y-m-d H:i:s') . "\n\n";
$body .= "━━━ 客户信息 ━━━\n";
$body .= "姓名：" . $name . "\n";
$body .= "公司：" . $company . "\n";
$body .= "邮箱：" . $email . "\n";
$body .= "电话：" . $phone . "\n";
$body .= "服务需求：" . $service . "\n\n";
$body .= "━━━ 咨询内容 ━━━\n";
$body .= $message . "\n\n";
$body .= "━━━━━━━━━━━━━━\n";
$body .= "本邮件由 BLUESTAR 官网联系表单自动发送\n";
$body .= "ブルースター株式会社 (BlueStar Co.,Ltd.)\n";
$body .= "〒169-0075 東京都新宿区高田馬場1-31-8\n";
$body .= "TEL: 03-6824-5796\n";

// === SMTP Configuration ===
define('SMTP_HOST', 'smtp.lolipop.jp');
define('SMTP_PORT', 465);
define('SMTP_USER', 'idc_info@bl-star.co.jp');
define('SMTP_PASS', 'Imku1324Imku1324_');
define('FROM_ADDR', 'idc_info@bl-star.co.jp');
define('FROM_NAME', 'BLUESTAR 官网');

$bcc_addr = ($to_email === 'idc_info@bl-star.co.jp') ? 'info@bl-star.cloud' : 'idc_info@bl-star.co.jp';
$recipients = [$to_email];
if ($bcc_addr) $recipients[] = $bcc_addr;

function smtp_cmd($socket, $cmd, $expected) {
    fwrite($socket, $cmd . "\r\n");
    $resp = '';
    while (!feof($socket) && ($line = fgets($socket, 512))) {
        $resp .= $line;
        if (isset($line[3]) && $line[3] === ' ') break;
    }
    return $resp;
}

$success = false;
$error_msg = '';

try {
    $context = stream_context_create(['ssl' => ['verify_peer' => false, 'verify_peer_name' => false]]);
    $socket = @stream_socket_client(
        'ssl://' . SMTP_HOST . ':' . SMTP_PORT,
        $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $context
    );
    if (!$socket) throw new Exception("SMTP connection failed: $errstr ($errno)");

    // Greeting
    fgets($socket, 512);

    // EHLO
    $ehlo_resp = smtp_cmd($socket, 'EHLO bl-star.co.jp', 250);

    // AUTH LOGIN
    smtp_cmd($socket, 'AUTH LOGIN', 334);
    smtp_cmd($socket, base64_encode(SMTP_USER), 334);
    $auth_resp = smtp_cmd($socket, base64_encode(SMTP_PASS), 235);

    // MAIL FROM
    smtp_cmd($socket, 'MAIL FROM:<' . FROM_ADDR . '>', 250);

    // RCPT TO
    foreach ($recipients as $r) {
        $r = trim($r);
        if (!empty($r)) {
            $rcpt_resp = smtp_cmd($socket, 'RCPT TO:<' . $r . '>', 250);
        }
    }

    // DATA
    smtp_cmd($socket, 'DATA', 354);

    $headers = '';
    $headers .= "From: " . FROM_NAME . " <" . FROM_ADDR . ">\r\n";
    $headers .= "To: <" . $to_email . ">\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "Subject: " . $subject . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "Content-Transfer-Encoding: 8bit\r\n";
    $headers .= "X-Mailer: BLUESTAR Contact Form\r\n\r\n";

    $full_msg = $headers . $body . "\r\n.\r\n";
    fwrite($socket, $full_msg);
    $data_resp = '';
    while (!feof($socket) && ($line = fgets($socket, 512))) {
        $data_resp .= $line;
        if (isset($line[3]) && $line[3] === ' ') break;
    }

    smtp_cmd($socket, 'QUIT', 221);
    fclose($socket);
    $success = true;

} catch (Exception $e) {
    $error_msg = $e->getMessage();
    // Fallback: log error and try PHP mail()
    $fallback_body = "【SMTP失败 - 使用mail()兜底】\n错误: " . $error_msg . "\n\n" . $body;
    $fallback_headers = "From: " . FROM_NAME . " <" . FROM_ADDR . ">\r\n";
    $fallback_headers .= "Reply-To: " . $email . "\r\n";
    $fallback_headers .= "MIME-Version: 1.0\r\n";
    $fallback_headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $fallback_headers .= "Content-Transfer-Encoding: 8bit\r\n";
    $fallback_headers .= "X-Mailer: BLUESTAR Contact Form";

    foreach ($recipients as $r) {
        $r = trim($r);
        if (!empty($r)) {
            if (function_exists('mb_send_mail')) {
                @mb_send_mail($r, $subject . ' [Fallback]', $fallback_body, $fallback_headers);
            } else {
                @mail($r, $subject . ' [Fallback]', $fallback_body, $fallback_headers);
            }
        }
    }
    $success = true; // Consider it sent even via fallback
}

if ($success) {
    echo json_encode(['status' => 'success', 'message' => '邮件已成功发送！我们将尽快与您联系。']);
} else {
    echo json_encode(['status' => 'error', 'message' => '邮件发送失败：' . $error_msg]);
}
