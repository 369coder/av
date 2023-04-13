<?php

/**
 * @upload.php
 */

$params = session_get_cookie_params();
session_set_cookie_params([
  'lifetime' => 600,
  'path' => $params["path"],
  'domain' => $params["domain"],
  'secure' => $params["secure"],
  'httponly' => true,
  'samesite' => $params["samesite"],
]);
session_cache_limiter('none');
session_start();

if (isset($_POST['token'])) {
  $token = $_POST['token'];
}
if (isset($_SESSION['token'])) {
  $session_token = $_SESSION['token'];
}
unset($_SESSION['token']);

if ((empty($token) || $token != $session_token)) {
  $errorMessage = [];
  $errorMessage[] = '不正なリクエストです。';
  $_SESSION['e-message'] = $errorMessage;
  header('Location: http://localhost:3000/index.php');
  exit;
}

$_SESSION['token'] = $token;

$action = null;
if (isset($_POST['action'])) {
  $action = $_POST['action'];
}

if ($action === "upload") {
  if (isset($_FILES['up_file'])) {


    try {
      if (!isset($_FILES['up_file']['error']) || is_array($_FILES['up_file']['error'])) {
        throw new RuntimeException('パラメータが無効です。');
      }

      $error = (int)$_FILES['up_file']['error'];
      $message = '';
      switch ((int)$error) {
        case 0:
          $message = null;
          break;
        case 1:
          $message = '設定のアップロードファイルサイズの制限を超えています。';
          break;
        case 2:
          $message = 'HTMLフォームで指定されたファイルサイズの制限を超えています。';
          break;
        case 3:
          $message = 'アップロードファイルの内容が不十分です。';
          break;
        case 4:
          $message = 'ファイルを選択してください。';
          break;
        case 6:
          $message = 'サーバーにTMPフォルダがありません。';
          break;
        case 7:
          $message = 'サーバーがディスクの書き込みに失敗しました。';
          break;
        case 8:
          $message = 'サーバーがファイルのアップロードを中止しました。';
          break;
        default:
          $message = 'サーバーに未知のエラーが発生しました。';
      }

      if (!empty($message)) {
        throw new RuntimeException($message);
      }

      //ファイルサイズ確認(50MB制限)
      if ($_FILES['up_file']['size'] > 50000000) {
        throw new RuntimeException('ファイルサイズの制限を超えています。');

      }

      //拡張子判断
      $arrowExt = ['avi', 'mov', 'avi', 'wmv', 'mpeg', 'mp4'];
      $fileExt = pathinfo($_FILES['up_file']['name'], PATHINFO_EXTENSION);
      if (in_array($fileExt, $arrowExt) === false) {
        throw new RuntimeException('ファイル形式が無効です。');
      }

      //移動する
      if (is_uploaded_file($_FILES['up_file']['tmp_name'])) {
        //名前を日時に変える
        $newFilename = time() . '.' . $fileExt;
        $up_dir = './uploads/';
        $moveFile = $up_dir . $newFilename;

        if (!move_uploaded_file($_FILES['up_file']['tmp_name'], $moveFile)) {
          throw new RuntimeException('ファイルの移動に失敗しました。');
        }

        echo 'ファイルのアップロードに成功しました。';

        //ここからファイルの変換
        $movieExt = '.mp4';
        $movieName = time();
        $movieDir = './movies';
        $movieFile = $movieDir . '/' . $movieName . $movieExt;

        //ffmpeg コマンド
        define("FFMPEG_COMMAND1", ' ffmpeg -i %s -f mp4 -vcodec libx264 -acodec aac -b 2000k -y %s 2>&1');
        $command1 = sprintf(FFMPEG_COMMAND1, $moveFile, $movieFile);
        $log = null;
        exec($command1, $log);
        //var_dump($log);

        //poster作成
        $imageFile = $movieDir . '/' . $movieName . '.jpg';
        define("FFMPEG_COMMAND2", ' ffmpeg -ss 5 -i %s -vframes 1 -f image2 %s 2>&1');
        $command2 = sprintf(FFMPEG_COMMAND2, $movieFile, $imageFile);
        $log = null;
        exec($command2, $log);

        if (file_exists($movieFile)) {
          unlink($moveFile);
        }

      } else {
        throw new RuntimeException('ファイルのアップロードに失敗しました。');
      }

    } catch (RuntimeException $e) {

      $_SESSION['e-message'][] = $e->getMessage();
      header('Location: http://localhost:3000/index.php');
      exit;
    }
  } else {
    $_SESSION['e-message'][] = 'ファイルを選択してください';
    header('Location: http://localhost:3000/index.php');
    exit;
  }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>動画の表示</title>
</head>
<body>
  <section class="w-full mx-auto text-center">
    <div class="w-1/2 mx-auto text-center p-8">
      <video src="<?php echo $movieFile;?>" controls width="720" poster="<?php echo $imageFile;?>"></video>
    </div>
  </section>
</body>
</html>