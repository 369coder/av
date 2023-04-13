<?php

/**
 * @index.php
 */

session_start();
$arr_cookie_options = array(
  'expires' => time() + 60 * 60,
  'path' => '/',
  'domain' => 'localhost',
  'secure' => true,
  'httponly' => true,
  'samesite' => 'Lax'
);
setcookie(session_name(), session_id(), $arr_cookie_options);

$token = uniqid('', true);
$_SESSION['token'] = $token;

?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>動画のアップロードと変換</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="./style.css">
</head>

<body>
  <section class="text-gray-600 body-font relative">
    <div class="container px-5 py-24 mx-auto">
      <div class="flex flex-col text-center w-full mb-12">
        <h1 class="sm:text-3xl text-2xl font-medium title-font mb-4 text-gray-900">動画ファイルのアップロード</h1>
        <p class="lg:w-2/3 mx-auto leading-relaxed text-base">mov.avi.wmv.mpeg.mp4のファイルがアップロードできます。</p>
      </div>
      <?php if (isset($_SESSION['e-message'])) : ?>
        <div class="my-6 border border-red-600 rounded bg-red-50">
          <?php foreach ($_SESSION['e-message'] as $message) : ?>
            <p class="mb-4 text-left lg:text-center text-sm text-red-600"><?php echo $message; ?></p>
          <?php endforeach; ?>
          <?php unset($_SESSION['e-message']); ?>
        </div>
      <?php endif; ?>
      <form action="./upload.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="upload">
        <input type="hidden" name="token" value="<?php echo $token; ?>">
        <div class="lg:w-1/2 md:w-2/3 mx-auto">
          <div class="flex flex-wrap -m-2">
            <div class="p-2 w-full">
              <div class="relative">
                <label for="up_file" class="leading-7 text-sm text-gray-600">動画ファイル</label>
                <input type="file" id="up_file" name="up_file" accept="video/*" capture="user" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 resize-none leading-6 transition-colors duration-200 ease-in-out">
              </div>
            </div>
            <div class="p-2 w-full">
              <button class="block w-full flex mx-auto justify-center text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg tracking-widest">アップロードする</button>
            </div>
            <div class="p-2 w-full pt-8 mt-8 border-t border-gray-200 text-center">
              <a class="text-indigo-500">アップロードテスト</a>
              <p class="leading-normal my-5">369code</p>
              <span class="inline-flex">
                <a class="text-gray-500">
                  <svg fill="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                    <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"></path>
                  </svg>
                </a>
                <a class="ml-4 text-gray-500">
                  <svg fill="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                    <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"></path>
                  </svg>
                </a>
              </span>
            </div>
          </div>
        </div>
      </form>
    </div>
  </section>
</body>

</html>