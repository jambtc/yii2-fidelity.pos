<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\assets\NotificationsAsset;
use app\assets\ServiceWorkerAsset;
use app\assets\SinAsset;

use app\components\Settings;

function isLocalhost($whitelist = ['127.0.0.1', '::1']) {
    return in_array($_SERVER['REMOTE_ADDR'], $whitelist);
}

$sin = '';
if (isset($_COOKIE['sin']))
    $sin = $_COOKIE['sin'];

// impostazioni variabili globali per tutti i js
$options = [
    'userSin' => $sin,
    // ...
];
$this->registerJs(
    "var yiiGlobalOptions = ".\yii\helpers\Json::htmlEncode($options).";",
    yii\web\View::POS_HEAD,
    'yiiGlobalOptions'
);


AppAsset::register($this);
NotificationsAsset::register($this);
ServiceWorkerAsset::register($this);
SinAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>

    <!-- Manifest Progressive Web App -->
    <link rel="manifest" href="manifest.json">

    <!-- Google font file. If you want you can change. -->
  	<link href="https://fonts.googleapis.com/css?family=Nunito:300,400,600,700,900" rel="stylesheet">

    <!-- Fontawesome font file css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" integrity="sha512-HK5fgLBL+xu6dm/Ii3z4xhlSUyZgTT9tuc/hSrtw6uzJOvgRr2a9jyxxT1ely+B+xFAmJKVSTbpM/CuL7qxO8w==" crossorigin="anonymous" />

    <?php $this->head() ?>

</head>
<body>

<?php $this->beginBody() ?>



<div class="wrapper">
    <?php
    if (!Yii::$app->user->isGuest) {
        echo $this->render('_navbar');
    }
    ?>
    <div class="wrapper-inline">
        <?php $this->beginContent('@app/views/layouts/base.php') ?>

        <?php echo $this->render('_searchform'); ?>

        <div id="snackbar">
            <?= Yii::t('app','A new version of this app is available.'); ?>
            <p>
                <?= Yii::t('app','Click'); ?>
                <a id="reload">
                    <button type="button" class="btn btn-warning px-5">
                        <?= Yii::t('app','here') ?>
                    </button>
                </a>
                <?= Yii::t('app',' to update.') ?>
            </p>
        </div>

        <div id="wss_server">
            <p>
                <?= Yii::t('app','The server synchronization is not working.'); ?>
                <?= Yii::t('app','Try reloading the page, otherwise contact support.'); ?>
            </p>
            <div class="row">
                <div class="col-6">
                    <a href="javascript: window.location.reload();">
                        <button type="button" class="btn btn-primary px-2">
                            <?= Yii::t('app','Reload') ?>
                        </button>
                    </a>
                </div>
                <div class="col">
                    <a href="mailto:<?php echo Yii::$app->params['senderEmail']; ?>?subject=WSS%20Error">
                        <button type="button" class="btn btn-danger px-2">
                            <?= Yii::t('app','Support') ?>
                        </button>
                    </a>
                </div>
            </div>
        </div>

        <main class="margin mt-0">
            <?= Alert::widget() ?>
            <?= $content ?>
        </main>
        <?php $this->endContent() ?>
    </div>

</div>



<?php $this->endBody() ?>

</body>

<?php
// modal PAGE

if (Yii::$app->controller->id == 'users'){
    echo $this->render('_pin-manage');
    echo $this->render('_push-manage');
    echo $this->render('_masterseed');
}

if (Yii::$app->controller->id == 'receive'){
    echo $this->render('_clipboard-copy');
}

echo $this->render('_pin-request');

?>

</html>
<?php $this->endPage() ?>
