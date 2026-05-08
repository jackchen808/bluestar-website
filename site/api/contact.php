PhpMailerAutoload: disabled
純PHP SMTP実装 - smtp.lolipop.jp:465 でSSL接続
認証方式: AUTH LOGIN
アカウント: idc_info@bl-star.co.jp

<?php
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
$type    = trim($input['type'] ?? 'inquiry'); // 'inquiry' or 'apply'
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

// --- SMTP Send via fsockopen/stream_socket_client ---
$smtp_host = 'smtp.lolipop.jp';
$smtp_port = 465;
$smtp_user = 'idc_info@bl-star.co.jp';
$smtp_pass = 'Imku1324Imku1324_';
$from_addr = 'idc_info@bl-star.co.jp';
$from_name = 'BLUESTAR 官网';
$bcc_addr = ($to_email === 'idc_info@bl-star.co.jp') ? 'info@bl-star.cloud' : 'idc_info@bl-star.co.jp';

$recipients = [$to_email];
if ($bcc_addr) $recipients[] = $bcc_addr;

function smtp_cmd($socket, $cmd, $expected) {
    fwrite($socket, $cmd . "\r\n");
    $resp = '';
    while ($line = fgets($socket, 512)) {
        $resp .= $line;
        if (substr($line, 3, 1) === ' ') break;
    }
    return $resp;
}

$success = false;
$error_msg = '';

try {
    $context = stream_context_create(['ssl' => ['verify_peer' => false, 'verify_peer_name' => false]]);
    $socket = @stream_socket_client('ssl://' . $smtp_host . ':' . $smtp_port, $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $context);
    if (!$socket) throw new Exception("SMTP connection failed: $errstr ($errno)");

    // Read greeting
    fgets($socket, 512);

    // EHLO
    smtp_cmd($socket, 'EHLO bl-star.co.jp', 250);

    // STARTTLS (already on SSL, skip)
    // AUTH LOGIN
    smtp_cmd($socket, 'AUTH LOGIN', 334);
    smtp_cmd($socket, base64_encode($smtp_user), 334);
    smtp_cmd($socket, base64_encode($smtp_pass), 235);

    // MAIL FROM
    smtp_cmd($socket, "MAIL FROM:<$from_addr>", 250);

    // RCPT TO (each recipient)
    foreach ($recipients as $r) {
        $r = trim($r);
        if (!empty($r)) smtp_cmd($socket, "RCPT TO:<$r>", 250);
    }

    // DATA
    smtp_cmd($socket, 'DATA', 354);

    $headers = "From: $from_name <$from_addr>\r\n";
    $headers .= "To: <$to_email>\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Subject: $subject\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "Content-Transfer-Encoding: 8bit\r\n";
    $headers .= "X-Mailer: BLUESTAR Contact Form\r\n\r\n";

    $full_msg = $headers . $body . "\r\n.\r\n";
    fwrite($socket, $full_msg);
    fgets($socket, 512);

    // QUIT
    smtp_cmd($socket, 'QUIT', 221);
    fclose($socket);
    $success = true;
} catch (Exception $e) {
    $error_msg = $e->getMessage();
    // Fallback to mb_send_mail
    $headers_fb = "From: $from_name <$from_addr>\r\nReply-To: $email\r\nMIME-Version: 1.0\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: 8bit\r\nX-Mailer: BLUESTAR Contact Form";
    foreach ($recipients as $r) {
        $r = trim($r);
        if (!empty($r)) @mb_send_mail($r, $subject, $body, $headers_fb);
    }
    $success = true;
}

if ($success) {
    echo json_encode(['status'=>'success','message'=>'邮件已成功发送！我们将尽快与您联系。']);
} else {
    echo json_encode(['status'=>'error','message'=>'邮件发送失败：' . $error_msg]);
}
