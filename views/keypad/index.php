<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use app\models\Pos;
use app\models\Stores;
use app\components\Settings;

app\assets\KeypadAsset::register($this);

// echo '<pre>'.print_r(Yii::$app->user->identity,true);exit;

$this->title = Yii::t('app', 'Keypad');
$sin = $_COOKIE['sin'];
$pos = Pos::find(['sin' => $sin])->one();
$store = $pos->store;
$decimals = Settings::poa($store->id_blockchain)->decimals;

$options = [
    'spinner' => '<div class="button-spinner spinner-border text-light" role="status"><span class="sr-only">Loading...</span></div>',
    'confirm' => Yii::t('app','Confirm'),
    'poaDecimals' => $decimals,
    'msgDecimalError' => Yii::t('app','Use a maximum of {count} decimal places.',[
        'count' => $decimals,
    ]),
    'msgWalletError' => Yii::t('app','Wallet address not found or not configured.'),
    'tokenAuth' => (empty($store->wallet_address) ? false : true),
    'invoiceCreate' => Url::to(["invoices/create"]),
    'posId' => $pos->id,

];

$this->registerJs(
    "var yiiOptions = ".yii\helpers\Json::htmlEncode($options).";",
    yii\web\View::POS_HEAD,
    'yiiOptions'
);
?>
<div class="dash-balance ">
    <div class="row">
        <div class="col px-0 mx-0">
            <div class='keypad-get'></div>
        </div>
    </div>
</div>
