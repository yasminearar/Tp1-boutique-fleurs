<?php

require_once 'vendor/autoload.php';

require_once 'config.php';

use App\Routes\Route;
use App\Providers\Auth;

Auth::init();

session_start();

if (isset($_SESSION['user_id'])) {
    if (!isset($_SESSION['fingerPrint']) || $_SESSION['fingerPrint'] != md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR'])) {
        Auth::logout();
    }

    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 7200)) {
        $_SESSION['flash_messages'][] = [
            'message' => 'Votre session a expiré. Veuillez vous reconnecter.',
            'type' => 'warning'
        ];
        Auth::logout();
    }
}

$_SESSION['LAST_ACTIVITY'] = time();

define('BASE_URL', BASE);
define('ASSETS_URL', ASSET);

require_once ROOT_DIR . '/src/Routes/web.php';

Route::dispatch();
?>
