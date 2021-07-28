<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

app\assets\KeypadAsset::register($this);
$this->title = Yii::t('app', 'Keypad');

$options = [
    'spinner' => '<div class="button-spinner spinner-border text-light" role="status"><span class="sr-only">Loading...</span></div>',
    'confirm' => Yii::t('app','Confirm'),
    'poaDecimals' => $blockchain->decimals,
    'msgDecimalError' => Yii::t('app','Use a maximum of {count} decimal places.',[
        'count' => $blockchain->decimals,
    ]),
    'msgWalletError' => Yii::t('app','Wallet address not found or not configured.'),
    'tokenAuth' => (empty($store->wallet_address) ? false : true),
    'invoiceCreate' => Url::to(["invoices/create"]),
    'posId' => $pos->id,
    'balance' => 'Balance: '.$balance,

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
