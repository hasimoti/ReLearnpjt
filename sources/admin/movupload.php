<?php
require 'vendor/autoload.php';

putenv('GOOGLE_APPLICATION_CREDENTIALS=credentials.json');

$client = new Google_Client();
$client->setAuthConfig('credentials.json');
$client->addScope(Google_Service_Drive::DRIVE_FILE);
$client->setAccessType('offline');

// 認証トークンの保存場所
$tokenPath = 'token.json';

if (file_exists($tokenPath)) {
    $accessToken = json_decode(file_get_contents($tokenPath), true);
    $client->setAccessToken($accessToken);
}

if ($client->isAccessTokenExpired()) {
    if ($client->getRefreshToken()) {
        $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
    } else {
        // 初回のみこの処理が必要（ブラウザで認証）
        $authUrl = $client->createAuthUrl();
        echo "認証してください: <a href='$authUrl'>ここをクリック</a>";
        exit;
    }
    file_put_contents($tokenPath, json_encode($client->getAccessToken()));
}

$service = new Google_Service_Drive($client);

// アップロード処理
if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $file = new Google_Service_Drive_DriveFile();
    $file->setName($_FILES['file']['name']);

    $result = $service->files->create($file, [
        'data' => file_get_contents($_FILES['file']['tmp_name']),
        'mimeType' => $_FILES['file']['type'],
        'uploadType' => 'multipart'
    ]);

    $permission = new Google_Service_Drive_Permission();
    $permission->setType('anyone');
    $permission->setRole('reader');
    $service->permissions->create($result->id, $permission);

    echo '共有リンク: https://drive.google.com/drive/folders/18oaw8f7mBpSy1q42ik1_M1jehzrGi_pr' . $result->id . '/preview';






    echo 'アップロード完了！ファイルID: ' . $result->id;
} else {
    echo 'アップロードエラー';
}
?>