<?php
/**
 * BLUESTAR Contact Form - メール送信 (lolipop 最適化版)
 * 
 * 推奨: lolipop サーバー上で動作させる
 * 方式1: PHP mb_send_mail() - lolipop のデフォルトSMTPリレーを使う
 * 方式2: SMTP 直接接続 (stream_socket_client + STARTTLS)
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
    echo json_encode(['status'=>'error','message'=>'必須項目（名前・メールアドレス・お問い合わせ内容）を入力してください']);
    exit;
}

$is_jp = (strpos($to_email, 'idc_info') !== false);

$subject = '';
if ($type === 'apply') {
    $subject = '【BLUESTAR 応募】' . $name;
} else {
    $subject = '【BLUESTAR お問い合わせ】' . ($company ? $company . ' - ' : '') . $name;
}

$body = "━━━ BLUESTAR お問い合わせフォーム ━━━\n";
$body .= "種別：" . ($type === 'apply' ? '応募申請' : '業務問い合わせ') . "\n";
$body .= "送信日時：" . date('Y-m-d H:i:s') . " (日本時間)\n\n";
$body .= "◆ お客様情報\n";
$body .= "お名前：" . $name . "\n";
$body .= "会社名：" . $company . "\n";
$body .= "メール：" . $email . "\n";
$body .= "電話番号：" . $phone . "\n";
$body .= "ご用件：" . $service . "\n\n";
$body .= "◆ お問い合わせ内容\n" . $message . "\n\n";
$body .= "─────────────────────────\n";
$body .= "本メールは BLUESTAR 公式サイトお問い合わせフォームより自動送信されています。\n";
$body .= "ブルースター株式会社 (BlueStar Co.,Ltd.)\n";
$body .= "〒169-0075 東京都新宿区高田馬場1-31-8\n";
$body .= "TEL: 03-6824-5796\n";

// ====================
// 方式1: mb_send_mail (lolipop で最も安定)
// ====================
$from_addr = 'idc_info@bl-star.co.jp';
$from_name = 'BLUESTAR お問い合わせフォーム';

$headers = "From: " . $from_name . " <" . $from_addr . ">\r\n";
$headers .= "Reply-To: " . $email . "\r\n";
$headers .= "Return-Path: " . $from_addr . "\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "Content-Transfer-Encoding: 8bit\r\n";
$headers .= "X-Mailer: BLUESTAR Contact Form\r\n";

$recipients = [$to_email];
$bcc = ($to_email === 'idc_info@bl-star.co.jp') ? 'info@bl-star.cloud' : 'idc_info@bl-star.co.jp';
if ($bcc) $recipients[] = $bcc;

$success = true;
$error_msg = '';
$sent_count = 0;

foreach ($recipients as $r) {
    $r = trim($r);
    if (empty($r)) continue;
    if (function_exists('mb_send_mail')) {
        $ok = @mb_send_mail($r, $subject, $body, $headers);
    } else {
        $ok = @mail($r, $subject, $body, $headers);
    }
    if ($ok) {
        $sent_count++;
    } else {
        $error_msg .= 'Failed to send to ' . $r . '; ';
    }
}

if ($sent_count > 0) {
    echo json_encode([
        'status' => 'success',
        'message' => 'お問い合わせを受け付けました。担当者よりご連絡いたします。'
    ]);
} else {
    // 方式2: SMTP direct fallback
    try {
        $context = stream_context_create(['ssl' => ['verify_peer' => false, 'verify_peer_name' => false]]);
        $socket = @stream_socket_client('tls://smtp.lolipop.jp:587', $errno, $errstr, 15, STREAM_CLIENT_CONNECT, $context);
        if (!$socket) throw new Exception("Connection failed: $errstr");

        fgets($socket, 512);
        fwrite($socket, "EHLO bl-star.co.jp\r\n");
        while (!feof($socket) && ($line = fgets($socket, 512))) {
            if (isset($line[3]) && $line[3] === ' ') break;
        }

        fwrite($socket, "AUTH LOGIN\r\n"); fgets($socket, 512);
        fwrite($socket, base64_encode('idc_info@bl-star.co.jp') . "\r\n"); fgets($socket, 512);
        fwrite($socket, base64_encode('Imku1324Imku1324_') . "\r\n"); fgets($socket, 512);

        foreach ($recipients as $r) {
            $r = trim($r);
            if (empty($r)) continue;
            fwrite($socket, "MAIL FROM:<$from_addr>\r\n"); fgets($socket, 512);
            fwrite($socket, "RCPT TO:<$r>\r\n"); fgets($socket, 512);
        }
        fwrite($socket, "DATA\r\n"); fgets($socket, 512);
        fwrite($socket, $headers . "\r\n" . $body . "\r\n.\r\n"); fgets($socket, 512);
        fwrite($socket, "QUIT\r\n"); fclose($socket);
        $success = true;
    } catch (Exception $e) {
        $success = false;
        $error_msg = $e->getMessage();
    }

    if ($success) {
        echo json_encode(['status' => 'success', 'message' => 'お問い合わせを受け付けました。']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'メール送信に失敗しました。お手数ですが info@bl-star.cloud まで直接ご連絡ください。']);
    }
}
