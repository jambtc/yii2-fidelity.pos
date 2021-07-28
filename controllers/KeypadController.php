<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

use app\models\Pos;
use app\models\Merchants;
use app\models\Stores;

use app\components\WebApp;
use app\components\Settings;

/**
 * PosController implements the CRUD actions for Pos model.
 */
class KeypadController extends Controller
{

    /**
     * Lists all Pos models.
     * @return mixed
     */
    public function actionIndex()
    {
        $sin = !isset($_COOKIE['sin']) ? '' : $_COOKIE['sin'];
        $pos = Pos::find(['sin' => $sin])->one();

        if (null === $pos){
            return $this->redirect(['site/logout']);
        }

        $store = $pos->store;
        if (empty($store->wallet_address)){
            return $this->redirect(['site/logout']);
        }

        $blockchain = Settings::poa($store->id_blockchain);
        $ERC20 = new Yii::$app->Erc20($blockchain->id);

        return $this->render('index',[
            'balance' => $ERC20->Balance($store->wallet_address),
            'store' => $store,
            'pos' => $pos,
            'blockchain' => $blockchain
        ]);
    }


}
