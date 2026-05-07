/**
 * ================================================
 *  ブルースター株式会社 (BlueStar Co.,Ltd.)
 *  データベース設定情報 (CMS開発用)
 * ================================================
 *
 * 【注意】このファイルは開発環境用の設定テンプレートです。
 * 本番環境では .env ファイルまたは環境変数で管理してください。
 * このファイルは Git にコミットしないでください。
 *
 * ================================================
 *  データベース接続情報 (Lolipop)
 * ================================================
 *
 *  サーバー: mysql321.phy.lolipop.lan
 *  ユーザー: LAA0111254
 *  パスワード: (設定ファイルまたは環境変数で管理)
 *  データベース: LAA0111254-blstarhp
 *  ポート: 3306 (デフォルト)
 *
 * ================================================
 *  メールサーバー設定 (Lolipop)
 * ================================================
 *
 *  メールサーバー: mail232 (lolipop)
 *  POP:   pop.lolipop.jp:995  (SSL)
 *  IMAP:  imap.lolipop.jp:993 (SSL)
 *  SMTP:  smtp.lolipop.jp:465 (SSL)
 *
 *  メールアドレス: info@bl-star.cloud
 *  メールアドレス: idc_info@bl-star.co.jp
 *
 * ================================================
 *  CMS 接続例 (PHP - PDO)
 * ================================================
 *
 *  <?php
 *  $host = 'mysql321.phy.lolipop.lan';
 *  $user = 'LAA0111254';
 *  $pass = getenv('DB_PASSWORD'); // 環境変数から取得
 *  $db   = 'LAA0111254-blstarhp';
 *
 *  try {
 *      $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
 *      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 *  } catch (PDOException $e) {
 *      error_log('DB Connection failed: ' . $e->getMessage());
 *      die('システムエラーが発生しました。管理者にお問い合わせください。');
 *  }
 *  ?>
 *
 * ================================================
 *  お問い合わせフォーム連携
 * ================================================
 *
 *  お問い合わせデータをDBに保存する場合のテーブル構造例:
 *
 *  CREATE TABLE IF NOT EXISTS contacts (
 *      id INT AUTO_INCREMENT PRIMARY KEY,
 *      name VARCHAR(100) NOT NULL,
 *      company VARCHAR(200),
 *      email VARCHAR(200) NOT NULL,
 *      phone VARCHAR(50),
 *      service_type VARCHAR(50),
 *      message TEXT,
 *      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 *      status ENUM('new', 'read', 'replied') DEFAULT 'new'
 *  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
 *
 * ================================================
 *  バージョン情報
 * ================================================
 *
 *  最終更新: 2026-05-08
 *  BlueStar Co.,Ltd. - システム開発チーム
 */

// 開発環境用ダミー設定 (本番では使用しないでください)
const DB_CONFIG = {
  host: 'mysql321.phy.lolipop.lan',
  user: 'LAA0111254',
  database: 'LAA0111254-blstarhp',
  // パスワードは .env または環境変数から読み込むこと
};

export default DB_CONFIG;
