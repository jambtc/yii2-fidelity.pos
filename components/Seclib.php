<?php
/**
 * @author Sergio Casizzone
 * Esegue in background programmi e comandi per windows e linux
 *
 * utilizza la nuova libreria phpseclib
 * [Browse Git](https://github.com/phpseclib/phpseclib)
 */

namespace app\components;

use Yii;
use yii\base\Component;
use phpseclib\Net\SSH2;
use app\components\WebApp;
use app\components\Settings;
use yii\web\NotFoundHttpException;

class Seclib extends Component
{
    public function execInBackground($cmd)
    {
        if (substr(php_uname(), 0, 7) == "Windows"){
            pclose(popen("start /B ". $cmd, "r"));
        } else {
            $ssh = new SSH2('localhost', 22);
            $host = Settings::host();
            if (!$ssh->login($host->user, WebApp::decrypt($host->password))) {
                throw new NotFoundHttpException(Yii::t('app', 'Login to localhost server failed.'));
            }
            $action = $cmd . " > /dev/null &";
            $ssh->exec($action);
        }
    }
}
