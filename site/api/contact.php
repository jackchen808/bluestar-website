<?php
/**
 * ================================================
 *  BlueStar Co.,Ltd. - お問い合わせフォーム 送信API
 * ================================================
 *
 *  SMTPサーバー: smtp.lolipop.jp:465 (SSL)
 *  送信元アドレス: info@bl-star.cloud
 *  送信先アドレス: idc_info@bl-star.co.jp
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
 *      message: 'お問い合わせ内容...'
 *    })
 *  });
 *
 * ================================================
 *  CORS 設定
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
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}

// ================================================
//  設定
// ================================================

$config = [
    'smtp_host' => 'smtp.lolipop.jp',
    'smtp_port' => 465,
    'smtp_user' => 'idc_info@bl-star.co.jp',
    'smtp_pass' => 'Imku1324Imku1324_',
    'from'      => 'idc_info@bl-star.co.jp',
    'from_name' => 'ブルースター株式会社',
    'to'        => 'idc_info@bl-star.co.jp',
    'to_name'   => 'お問い合わせ担当',
    'bcc'       => 'info@bl-star.cloud',
];

// ================================================
//  入力取得
// ================================================

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$name    = trim($input['name'] ?? '');
$company = trim($input['company'] ?? '');
$email   = trim($input['email'] ?? '');
$phone   = trim($input['phone'] ?? '');
$service = trim($input['service'] ?? '');
$message = trim($input['message'] ?? '');

// ================================================
//  バリデーション
// ================================================

$errors = [];
if (empty($name))    $errors[] = 'お名前は必須です。';
if (empty($email))   $errors[] = 'メールアドレスは必須です。';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'メールアドレスの形式が正しくありません。';
if (empty($message)) $errors[] = 'お問い合わせ内容は必須です。';

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

// ================================================
//  メール送信 (PHPMailer)
// ================================================

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Try to load PHPMailer from various possible paths
$possiblePaths = [
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../../vendor/autoload.php',
    __DIR__ . '/../../../vendor/autoload.php',
];

$phpmailerLoaded = false;
foreach ($possiblePaths as $path) {
    if (file_exists($path)) {
        require_once $path;
        $phpmailerLoaded = true;
        break;
    }
}

if ($phpmailerLoaded) {
    // PHPMailer is available - send real email
    try {
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host       = $config['smtp_host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $config['smtp_user'];
        $mail->Password   = $config['smtp_pass'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = $config['smtp_port'];
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom($config['from'], $config['from_name']);
        $mail->addAddress($config['to'], $config['to_name']);
        $mail->addReplyTo($email, $name);

        if (!empty($config['bcc'])) {
            $mail->addBCC($config['bcc']);
        }

        $serviceLabels = [
            'infrastructure' => 'IDCインフラ構築',
            'delivery'       => 'サーバー導入',
            'operations'     => '運用保守',
            'fullcycle'      => 'フルライフサイクル',
            'other'          => 'その他',
        ];

        $body = <<<EOD
お問い合わせがありました。

━━━━━━━━━━━━━━━━━━
【お名前】 {$name}
【会社名】 {$company}
【メール】 {$email}
【電話】   {$phone}
【種別】   {$serviceLabels[$service] ?? 'その他'}
━━━━━━━━━━━━━━━━━━

【お問い合わせ内容】
{$message}
━━━━━━━━━━━━━━━━━━

本メールは自動送信されています。
ブルースター株式会社
BlueStar Co.,Ltd.
〒169-0075 東京都新宿区高田馬場1-31-8 高田馬場ダイカンプラザ625号
TEL: 03-6824-5796
EOD;

        $mail->isHTML(false);
        $mail->Subject = '【お問い合わせ】' . $name . ' 様より - ' . $company;
        $mail->Body    = $body;

        $mail->send();

        echo json_encode([
            'success' => true,
            'message' => 'お問い合わせを受け付けました。担当者より48時間以内にご連絡いたします。'
        ]);

    } catch (Exception $e) {
        error_log('Mail send failed: ' . $mail->ErrorInfo);
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => '送信に失敗しました。お手数ですがメールにて直接お問い合わせください。'
        ]);
    }
} else {
    // PHPMailer not installed - fallback to mail() function
    $to = $config['to'];
    $subject = '【お問い合わせ】' . $name . ' 様より - ' . $company;

    $serviceLabels = [
        'infrastructure' => 'IDCインフラ構築',
        'delivery'       => 'サーバー導入',
        'operations'     => '運用保守',
        'fullcycle'      => 'フルライフサイクル',
        'other'          => 'その他',
    ];

    $body = "お問い合わせがありました。\n\n";
    $body .= "━━━━━━━━━━━━━━━━━━\n";
    $body .= "【お名前】 {$name}\n";
    $body .= "【会社名】 {$company}\n";
    $body .= "【メール】 {$email}\n";
    $body .= "【電話】   {$phone}\n";
    $body .= "【種別】   " . ($serviceLabels[$service] ?? 'その他') . "\n";
    $body .= "━━━━━━━━━━━━━━━━━━\n\n";
    $body .= "【お問い合わせ内容】\n{$message}\n\n";
    $body .= "━━━━━━━━━━━━━━━━━━\n";
    $body .= "本メールは自動送信されています。\n";
    $body .= "ブルースター株式会社\n";
    $body .= "BlueStar Co.,Ltd.\n";
    $body .= "〒169-0075 東京都新宿区高田馬場1-31-8 高田馬場ダイカンプラザ625号\n";
    $body .= "TEL: 03-6824-5796";

    $headers = 'From: ' . $config['from_name'] . ' <' . $config['from'] . '>' . "\r\n";
    $headers .= 'Reply-To: ' . $email . "\r\n";
    $headers .= 'BCC: ' . $config['bcc'] . "\r\n";
    $headers .= 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-Type: text/plain; charset=UTF-8' . "\r\n";
    $headers .= 'Content-Transfer-Encoding: 8bit' . "\r\n";
    $headers .= 'X-Mailer: PHP/' . phpversion();

    $sent = mb_send_mail($to, $subject, $body, $headers);

    if ($sent) {
        echo json_encode([
            'success' => true,
            'message' => 'お問い合わせを受け付けました。担当者より48時間以内にご連絡いたします。'
        ]);
    } else {
        error_log('Mail send failed via mail()');
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => '送信に失敗しました。お手数ですがメールにて直接お問い合わせください。'
        ]);
    }
}
