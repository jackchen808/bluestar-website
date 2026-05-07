<?php
/**
 * ================================================
 *  BlueStar Co.,Ltd. - お問い合わせフォーム 送信API
 * ================================================
 *
 *  Pure PHP SMTP — no external libraries required.
 *  Connects to smtp.lolipop.jp:465 via SSL.
 *
 * ================================================
 *  使用例 (JavaScript fetch)
 * ================================================
 *
 *  fetch('/api/contact.php', {
 *    method: 'POST',
 *    headers: { 'Content-Type': 'application/json' },
 *    body: JSON.stringify({
 *      name: '山田太郎',
 *      company: '株式会社サンプル',
 *      email: 'taro@example.com',
 *      phone: '03-1234-5678',
 *      service: 'infrastructure',
 *      message: 'お問い合わせ内容...',
 *      to_email: 'info@bl-star.cloud'
 *    })
 *  });
 *
 * ================================================
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: https://www.bl-star.co.jp');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
    exit;
}

// ================================================
//  SMTP 設定 (lolipop)
// ================================================

$smtp_config = [
    'host' => 'smtp.lolipop.jp',
    'port' => 465,
    'user' => 'idc_info@bl-star.co.jp',
    'pass' => 'Imku1324Imku1324_',
    'from' => 'idc_info@bl-star.co.jp',
    'from_name' => 'ブルースター株式会社 (BLUESTAR)',
];

// ================================================
//  入力取得
// ================================================

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$name       = trim($input['name'] ?? '');
$company    = trim($input['company'] ?? '');
$email      = trim($input['email'] ?? '');
$phone      = trim($input['phone'] ?? '');
$service    = trim($input['service'] ?? '');
$message    = trim($input['message'] ?? '');
$to_email   = trim($input['to_email'] ?? 'idc_info@bl-star.co.jp');
$is_japanese = !empty($input['is_japanese']);

// ================================================
//  バリデーション
// ================================================

$errors = [];
if (empty($name))    $errors[] = $is_japanese ? 'お名前は必須です。' : '姓名是必填项。';
if (empty($email))   $errors[] = $is_japanese ? 'メールアドレスは必須です。' : '邮箱是必填项。';
if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = $is_japanese ? 'メールアドレスの形式が正しくありません。' : '邮箱格式不正确。';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'errors' => $errors]);
    exit;
}

if (empty($to_email) || !filter_var($to_email, FILTER_VALIDATE_EMAIL)) {
    $to_email = 'idc_info@bl-star.co.jp';
}

// ================================================
//  サービス種別ラベル
// ================================================

$serviceLabels = [
    'infrastructure' => $is_japanese ? 'IDCインフラ構築' : 'IDC 室内基建',
    'delivery'       => $is_japanese ? 'サーバー導入' : '服务器上架交付',
    'operations'     => $is_japanese ? '運用保守' : '运维管理服务',
    'fullcycle'      => $is_japanese ? 'フルライフサイクル' : '全生命周期服务',
    'other'          => $is_japanese ? 'その他' : '其他',
];

$serviceLabel = $serviceLabels[$service] ?? ($is_japanese ? 'その他' : '其他');

// ================================================
//  メール本文構築
// ================================================

$subject = $is_japanese
    ? '【お問い合わせ】' . $name . ' 様より - ' . $company
    : '【咨询】' . $name . ' - ' . $company;

$body = '';
if ($is_japanese) {
    $body .= "お問い合わせがありました。\n\n";
    $body .= "━━━━━━━━━━━━━━━━━━\n";
    $body .= "【お名前】 {$name}\n";
    $body .= "【会社名】 {$company}\n";
    $body .= "【メール】 {$email}\n";
    $body .= "【電話】   {$phone}\n";
    $body .= "【種別】   {$serviceLabel}\n";
    $body .= "━━━━━━━━━━━━━━━━━━\n\n";
    $body .= "【お問い合わせ内容】\n{$message}\n\n";
    $body .= "━━━━━━━━━━━━━━━━━━\n";
    $body .= "本メールは自動送信されています。\n";
    $body .= "ブルースター株式会社\n";
    $body .= "BlueStar Co.,Ltd.\n";
    $body .= "〒169-0075 東京都新宿区高田馬場1-31-8 高田馬場ダイカンプラザ625号\n";
    $body .= "TEL: 03-6824-5796\n";
    $body .= "Email: idc_info@bl-star.co.jp\n";
} else {
    $body .= "收到新的咨询消息。\n\n";
    $body .= "━━━━━━━━━━━━━━━━━━\n";
    $body .= "【姓名】 {$name}\n";
    $body .= "【公司名称】 {$company}\n";
    $body .= "【邮箱】 {$email}\n";
    $body .= "【电话】 {$phone}\n";
    $body .= "【服务需求】 {$serviceLabel}\n";
    $body .= "━━━━━━━━━━━━━━━━━━\n\n";
    $body .= "【项目描述】\n{$message}\n\n";
    $body .= "━━━━━━━━━━━━━━━━━━\n";
    $body .= "本邮件由系统自动发送。\n";
    $body .= "ブルースター株式会社 (BlueStar Co.,Ltd.)\n";
    $body .= "〒169-0075 東京都新宿区高田馬場1-31-8 高田馬場ダイカンプラザ625号\n";
    $body .= "TEL: 03-6824-5796\n";
    $body .= "Email: info@bl-star.cloud\n";
}

// ================================================
//  純PHP SMTP送信 (stream_socket_client)
// ================================================

function smtp_send($config, $to, $subject, $body, $reply_to, $reply_to_name) {
    $errno = 0;
    $errstr = '';

    $context = stream_context_create([
        'ssl' => [
            'verify_peer'       => false,
            'verify_peer_name'  => false,
            'allow_self_signed' => true,
        ]
    ]);

    $socket = @stream_socket_client(
        'ssl://' . $config['host'] . ':' . $config['port'],
        $errno, $errstr, 30,
        STREAM_CLIENT_CONNECT,
        $context
    );

    if (!$socket) {
        throw new RuntimeException("Cannot connect to SMTP server: $errstr ($errno)");
    }

    stream_set_timeout($socket, 30);

    function smtp_cmd($socket, $cmd, $expected = 250) {
        if ($cmd !== null) {
            fwrite($socket, $cmd . "\r\n");
        }
        $response = '';
        while (true) {
            $line = fgets($socket, 512);
            if ($line === false) break;
            $response .= $line;
            if (isset($line[3]) && $line[3] === ' ') break;
        }
        $code = (int)substr($response, 0, 3);
        if ($code !== $expected) {
            throw new RuntimeException("SMTP error (expected $expected): $response");
        }
        return $response;
    }

    // Read greeting (220)
    smtp_cmd($socket, null, 220);

    // EHLO
    smtp_cmd($socket, 'EHLO bl-star.co.jp');

    // AUTH LOGIN
    smtp_cmd($socket, 'AUTH LOGIN', 334);
    smtp_cmd($socket, base64_encode($config['user']), 334);
    smtp_cmd($socket, base64_encode($config['pass']));

    // MAIL FROM
    smtp_cmd($socket, 'MAIL FROM:<' . $config['from'] . '>');

    // RCPT TO
    $recipients = explode(',', $to);
    foreach ($recipients as $r) {
        $r = trim($r);
        if (!empty($r)) {
            smtp_cmd($socket, 'RCPT TO:<' . $r . '>');
        }
    }

    // DATA
    smtp_cmd($socket, 'DATA', 354);

    // Build RFC822 headers
    $headers = '';
    $headers .= 'From: ' . $config['from_name'] . ' <' . $config['from'] . ">\r\n";
    $headers .= 'To: ' . $to . "\r\n";
    $headers .= 'Reply-To: ' . $reply_to_name . ' <' . $reply_to . ">\r\n";
    $headers .= 'Subject: ' . $subject . "\r\n";
    $headers .= 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-Type: text/plain; charset=UTF-8' . "\r\n";
    $headers .= 'Content-Transfer-Encoding: 8bit' . "\r\n";
    $headers .= 'X-Mailer: PHP/SMTP-BlueStar' . "\r\n";

    $fullMessage = $headers . "\r\n" . $body;

    // Send the message data (dot-stuffing)
    $lines = explode("\n", $fullMessage);
    foreach ($lines as $line) {
        $line = rtrim($line, "\r");
        if (isset($line[0]) && $line[0] === '.') {
            $line = '.' . $line;
        }
        fwrite($socket, $line . "\r\n");
    }
    fwrite($socket, "\r\n.\r\n");

    // Read response (250)
    $response = '';
    while (true) {
        $line = fgets($socket, 512);
        if ($line === false) break;
        $response .= $line;
        if (isset($line[3]) && $line[3] === ' ') break;
    }
    $code = (int)substr($response, 0, 3);
    if ($code !== 250) {
        throw new RuntimeException("SMTP data error: $response");
    }

    // QUIT
    smtp_cmd($socket, 'QUIT', 221);

    fclose($socket);
    return true;
}

// ================================================
//  実行
// ================================================

try {
    smtp_send($smtp_config, $to_email, $subject, $body, $email, $name);

    // Also BCC to the other address
    $bcc_email = ($to_email === 'idc_info@bl-star.co.jp') ? 'info@bl-star.cloud' : 'idc_info@bl-star.co.jp';
    if (!empty($bcc_email) && $bcc_email !== $to_email) {
        try {
            smtp_send($smtp_config, $bcc_email, '[BCC] ' . $subject, $body, $email, $name);
        } catch (Exception $bcc_e) {
            error_log('BCC send failed: ' . $bcc_e->getMessage());
        }
    }

    $successMessage = $is_japanese
        ? 'お問い合わせを受け付けました。担当者より48時間以内にご連絡いたします。'
        : '您的咨询已成功提交，我们的项目经理将在48小时内与您联系。';

    echo json_encode([
        'status' => 'success',
        'message' => $successMessage
    ]);

} catch (Exception $e) {
    error_log('SMTP send failed: ' . $e->getMessage());

    // Fallback: try mb_send_mail()
    try {
        $fallback_headers = 'From: ' . $smtp_config['from_name'] . ' <' . $smtp_config['from'] . '>' . "\r\n";
        $fallback_headers .= 'Reply-To: ' . $name . ' <' . $email . '>' . "\r\n";
        $fallback_headers .= 'MIME-Version: 1.0' . "\r\n";
        $fallback_headers .= 'Content-Type: text/plain; charset=UTF-8' . "\r\n";
        $fallback_headers .= 'Content-Transfer-Encoding: 8bit' . "\r\n";
        $fallback_headers .= 'X-Mailer: PHP/' . phpversion();

        $sent = mb_send_mail($to_email, $subject, $body, $fallback_headers);

        if ($sent) {
            $successMessage = $is_japanese
                ? 'お問い合わせを受け付けました。担当者より48時間以内にご連絡いたします。'
                : '您的咨询已成功提交，我们的项目经理将在48小时内与您联系。';

            echo json_encode([
                'status' => 'success',
                'message' => $successMessage
            ]);
            exit;
        }
    } catch (Exception $fb_e) {
        error_log('Fallback mail() also failed: ' . $fb_e->getMessage());
    }

    $errorMessage = $is_japanese
        ? '送信に失敗しました。お手数ですが直接メールにてご連絡ください。'
        : '发送失败，请通过邮箱直接联系我们。';

    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $errorMessage
    ]);
}
