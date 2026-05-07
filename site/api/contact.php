<?php
/**
 * ================================================
 *  BlueStar Co.,Ltd. - お問い合わせフォーム 送信API
 * ================================================
 *
 * 【重要】このファイルは開発用テンプレートです。
 * 本番環境では適切なセキュリティ対策を施してからご利用ください。
 *
 * ================================================
 *  送信設定
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
 *  CORS 設定（必要に応じて調整）
 * ================================================
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: https://www.bl-star.co.jp');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Preflight 対応
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
    'smtp_user' => 'info@bl-star.cloud',
    'smtp_pass' => getenv('SMTP_PASSWORD'), // 環境変数から取得
    'from'      => 'info@bl-star.cloud',
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
//  メール送信 (PHPMailer推奨)
// ================================================

// 注意: 以下のコードはPHPMailerを使用した例です。
// composer require phpmailer/phpmailer でインストールしてください。

/*
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

$mail = new PHPMailer(true);

try {
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
EOD;

    $mail->isHTML(false);
    $mail->Subject = '【お問い合わせ】' . $name . ' 様より';
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
*/

// ================================================
//  ★ 開発用ダミーレスポンス ★
//  = 上記のPHPMailerコードを有効にしたら削除してください
// ================================================

echo json_encode([
    'success' => true,
    'message' => 'お問い合わせを受け付けました。担当者より48時間以内にご連絡いたします。',
    'debug'   => '※このメッセージは開発用ダミーレスポンスです。PHPMailer設定後に本稼働してください。'
]);
