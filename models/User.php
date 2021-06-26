<?php

namespace app\models;

use Yii;
use Google_Client;
use Google_Service_Calendar;

class User extends \yii\base\BaseObject implements \yii\web\IdentityInterface
{
    public $id;
    public $username;
    public $password;
    public $authKey;
    public $accessToken;

    private static $users = [
        '100' => [
            'id' => '100',
            'username' => 'admin',
            'password' => 'admin',
            'authKey' => 'test100key',
            'accessToken' => '100-token',
        ],
        '101' => [
            'id' => '101',
            'username' => 'demo',
            'password' => 'demo',
            'authKey' => 'test101key',
            'accessToken' => '101-token',
        ],
    ];


    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return isset(self::$users[$id]) ? new static(self::$users[$id]) : null;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        foreach (self::$users as $user) {
            if ($user['accessToken'] === $token) {
                return new static($user);
            }
        }

        return null;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        foreach (self::$users as $user) {
            if (strcasecmp($user['username'], $username) === 0) {
                return new static($user);
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return $this->password === $password;
    }

    public static function getClient()
    {
        // Запомнаем адрес, чтобы потом по нему перейти после входа
        $session = Yii::$app->session;
        if (!$session->isActive) {
            $session->open();
        }
        $_SESSION['before_url'] = $_SERVER["REQUEST_URI"];
        // Запомнаем адрес, чтобы потом по нему перейти после входа


        $clientSecrets = __DIR__ . '/../config/api/client_secrets.json';
        if (!file_exists($clientSecrets)) {
            echo '!file_exists';
            echo '<br>';
            echo $clientSecrets;
            exit;
        }

        $client = new Google_Client();

        // echo '<pre>';
        // print_r($client);
        // echo '</pre>';
        // exit;

        $client->setApplicationName('Google Calendar API PHP my test');
        $client->setScopes(Google_Service_Calendar::CALENDAR);
        $client->setAuthConfig($clientSecrets);
        $client->setAccessType('offline');

       // if (!$client->getRefreshToken())
       //     $_SESSION['access_token'] = null;


        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            $client->setAccessToken($_SESSION['access_token']);
            // Refresh the token if it's expired.
            if ($client->isAccessTokenExpired()) {
                if (!$client->getRefreshToken()) { // RefreshToken создается один раз, его потом нет???
                    $_SESSION['access_token'] = NULL;
                    $auth_url = $client->createAuthUrl();
                    header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
                    exit;
                }
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                $_SESSION['access_token'] = $client->getAccessToken();
            }
            // echo '<pre>';
            // print_r($client);
            // echo '</pre>';
            return $client;
        } else {
            $auth_url = $client->createAuthUrl();
            header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
            exit;
        }
    }

    public static function getClientPhoto()
    {
        // Запомнаем адрес, чтобы потом по нему перейти после входа
        $session = Yii::$app->session;
        if (!$session->isActive) {
            $session->open();
        }
        $_SESSION['before_url'] = $_SERVER["REQUEST_URI"];
        // Запомнаем адрес, чтобы потом по нему перейти после входа

        $clientSecrets = __DIR__ . '/../config/api/client_secrets_photo.json';
        if (!file_exists($clientSecrets)) {
            echo '!file_exists';
            echo '<br>';
            echo $clientSecrets;
            exit;
        }


        $client = new Google_Client();

        $client->setApplicationName('Google Photo API PHP my test');
        $client->setScopes([
            'https://www.googleapis.com/auth/photoslibrary.readonly',
            'https://www.googleapis.com/auth/photoslibrary.appendonly',
            'https://www.googleapis.com/auth/photoslibrary.readonly.appcreateddata',
            'https://www.googleapis.com/auth/photoslibrary.edit.appcreateddata',
            'https://www.googleapis.com/auth/photoslibrary',
            'https://www.googleapis.com/auth/photoslibrary.sharing'
        ]);
        $client->setAuthConfig($clientSecrets);
        $client->setAccessType('offline');

       // if (!$client->getRefreshToken())
       //     $_SESSION['access_token'] = null;


        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            $client->setAccessToken($_SESSION['access_token']);
            // Refresh the token if it's expired.
            if ($client->isAccessTokenExpired()) {
                if (!$client->getRefreshToken()) { // RefreshToken создается один раз, его потом нет???
                    $_SESSION['access_token'] = NULL;
                    $auth_url = $client->createAuthUrl();
                    header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
                    exit;
                }
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                $_SESSION['access_token'] = $client->getAccessToken();
            }
            // echo '<pre>';
            // print_r($client);
            // echo '</pre>';
            return $client;
        } else {
            $auth_url = $client->createAuthUrl();
            // echo $auth_url;
            // exit;
            header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
            exit;
        }
    }

//    public static function loginGoogle()
//    {
//
//    }

}
