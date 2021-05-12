<?php
use yii\helpers\Url;
use yii\helpers\Html;
use Da\QrCode\QrCode;
use app\assets\ClipboardCopyAsset;
use app\assets\QrcodeAsset;
use app\components\WebApp;

$this->title = Yii::$app->id;


$qrCodeMessage = $invoice->to_address
                ."&amount=" . $invoice->price
                ."&message=" . Html::encode($invoice->message);

$qrCode = (new QrCode($qrCodeMessage))
    ->setSize(600)
    ->setMargin(5)
    // ->useForegroundColor(51, 153, 255);
    ->useForegroundColor(11, 11, 11);


ClipboardCopyAsset::register($this);
QrcodeAsset::register($this);
$options = [
    'progressbarText' => Yii::t('app','Invoice almost expired...'),
    'progressbarTextComplete' => Yii::t('app','Invoice paid.'),
    'progressbarTextExpired' => Yii::t('app','Invoice expired.'),
    'checkInvoiceUrl' => Url::to(["invoices/check-status"]),
    'invoiceExpiredUrl' => Url::to(["invoices/expired"]),
    'invoiceId' => WebApp::encrypt($invoice->id),
];

$this->registerJs(
    "var yiiOptions = ".yii\helpers\Json::htmlEncode($options).";",
    yii\web\View::POS_HEAD,
    'yiiOptions'
);

$visible = [
    'complete' => 'none;',
    'expired' => 'none;',
    'waiting' => 'none;'
];

$progress = [
    'expired' => 'bg-danger',
    'waiting' => 'bg-success',
    'almost' => 'bg-warning'
];

$perctime = 100;
$progressbarSpinner = 'none;';
$totalSeconds = $invoice->expiration_timestamp - $invoice->invoice_timestamp;

// testing...
// $invoice->expiration_timestamp = time() + 360;

if ($invoice->status == 'new'){
    if (time() >= $invoice->expiration_timestamp){
        // invoice è scaduta e va aggiornata
        $visible['expired'] = 'ihnerit;';
        $background = $progress['expired'];
        $progressbarText = Yii::t('app','Invoice expired');
        if ($invoice->received < $invoice->price){
            $invoice->status = 'expired';
        } else {
            $invoice->status = 'complete';
        }
        $invoice->update();
    } else {
        // in attesa di pagamento e non ancora scaduta
        $visible['waiting'] = 'ihnerit;';
        $background = $progress['waiting'];
        $perctime = 10;
        $progressbarText = Yii::t('app','Waiting payment...');
        $progressbarSpinner = 'ihnerit;';
    }
} else if ($invoice->status == 'complete') {
    // invoice già pagata
    $visible['complete'] = 'ihnerit;';
    $background = $progress['waiting'];
    $progressbarText = Yii::t('app','Invoice paid');
} else {
    // invoice già scaduta
    $visible['expired'] = 'ihnerit;';
    $background = $progress['expired'];
    $progressbarText = Yii::t('app','Invoice expired');
}

?>
<div class="row" style="justify-content: center;">
    <div class="col-sm-4">
        <div class="card text-center ">
            <div class="card-header pb-0 px-1">
                <div class="login-logo text-left" style="font-size: large;">
                    <img src="css/images/logo.png" alt="" width="50">
                    <?= $store->denomination ?>
                </div>
            </div>
            <div class="card-body pt-1 mx-0 px-1">
                <div class="timer-row px-1">
                    <div class="progress bg-secondary" style="height: 30px;">
                        <div class="timer-progress progress-bar <?= $background ?>"
                            role="progressbar"
                            style="width: <?= $perctime ?>%;"
                            aria-valuenow="<?= $perctime ?>"
                            aria-valuemin="0"
                            aria-valuemax="100">
                                <div style="display: <?= $progressbarSpinner ?>" class="button-spinner spinner-border <?= $background ?>" role="status"></div>
                                <span class="ml-5 text-bold progressbar-text" style="position:absolute;">
                                    <?= $progressbarText ?>
                                </span>
                        </div>
                        <?php echo \russ666\widgets\Countdown::widget([
                            'id' => 'countdown-ticker',
                            'datetime' => date('Y-m-d H:i:s O', $invoice->expiration_timestamp),
                            // 'format' => '\<span style=\"background: red\"\>%M</span>:%S',
                            'format' => '<b>%M:%S</b>',
                            'tagName' => 'span',
                            'events' => [
                                'update' => 'function(e){
                                    console.log("[countdown update]", e);
                                    invoiceUpdate(e, '.($totalSeconds).');
                                }',
                                'finish' => 'function(e){
                                    console.log("[countdown finish]", e);
                                    invoiceExpired();
                                }',
                            ],
                        ])
                        ?>
                    </div>
                </div>
                <div class="order-details coin-box p-1" style="display: <?= $visible['waiting'] ?>">
                    <div class="d-flex align-items-center justify-content-between border shadow-lg">
		                <div class="d-flex align-items-center">
                            <div class="text-left">
                                <i class="fa fa-shopping-cart fa-2x"></i>
                                <!-- <h4 class="coin-name"><?= $store->denomination ?></h4> -->
                                <span class="text-muted"><?= $pos->denomination ?></span>
                            </div>
                        </div>
                        <div>
                            <small class="d-block mb-0 txt-green">
                                <i class="txt-green fa fa-star mr-10 mb-5"></i><?= Yii::t('app','Amount') ?>
                            </small>
                            <span class="text-muted d-block"><?= $invoice->price ?></span>
                        </div>
                    </div>
                </div>

                <!-- Qrcode -->
                <section class="contentQrcode container p-1 m-0 shadow-lg" style="display: <?= $visible['waiting'] ?>">
                    <div class="tab-item">
                        <div class="tab-menu fix-width" style="margin-bottom: 30px;">
							<a class="menu-item active" href="javascript:void(0);" data-content="contentScan" style="width: 50%;">
                                <i class="fa fa-qrcode mr-10"></i> <?= Yii::t('app','Scan') ?>
                            </a>
							<a class="menu-item" href="javascript:void(0);" data-content="contentCopy" style="width: 50%;">
                                <i class="fa fa-copy mr-10"></i> <?= Yii::t('app','Copy') ?>
                            </a>
						</div>

                        <div class="tab-content">
                            <div class="text-center" >
                                <div class="content-item active" id="contentScan" >
                                    <?php echo '<img style="max-width:300px;" class="rounded mx-auto mt-4 d-block" src="' . $qrCode->writeDataUri() . '">'; ?>
                                </div>
                            </div>
                            <div class="content-item mt-4" id="contentCopy">
                                <p class="alert alert-info">
                                    <?= Yii::t('app','To complete payment, please send the correct amount to the address below.') ?>
                                </p>


                                <nav class="copyBox">
                                    <div class="alert alert-info">
                                        <p class="lead"><?= Yii::t('app','Amount') ?>: 
                                            <span class="h3">
                                                <?= $invoice->price ?>
                                            </span>
                                        </p>
                                    </div>
                                    <div class="separatorGem my-3"></div>
                                    <?= Yii::t('app','Click on address to copy in the clipboard') ?>
                                    <div class="alert alert-warning text-muted text-break copyonClickAddress">
                                        <p class="lead"><?= Yii::t('app','Address') ?></p>
                                        <?= $invoice->to_address; ?>
                                        <input type="hidden" readonly="readonly" id="inputcopyWalletAddress" value="<?= $invoice->to_address; ?>" />
                                    </div>
                                </nav>

                            </div>
                        </div>
                    </div>
                </section>

                <!-- PAID -->
                <section id="invoicePaid" class="container p-1 m-0 shadow-lg" style="display: <?= $visible['complete'] ?>">
                    <div  class="bp-view">
						<div class="status-block">
							<div class="success-block">
								<div class="status-icon__wrapper">
									<div class="inner-wrapper">
										<div class="status-icon__wrapper__icon">
											<img src="css/images/checkmark.svg">
										</div>
										<div class="status-icon__wrapper__outline"></div>
									</div>
								</div>
								<div class="alert alert-success"><?= Yii::t('app','The invoice was paid') ?></div>
							</div>
						</div>
					</div>
                </section>

                <!-- EXPIRED -->
                <section id="invoiceExpired" class="container p-1 m-0 shadow-lg" style="display: <?= $visible['expired'] ?>">
					<div class="jumbotron">
                        <h4 class="alert alert-danger"><?= Yii::t('app','What happened?') ?></h4>
                        <p class="lead">
                            <?= Yii::t('app','This invoice has expired. An invoice is only valid for <b>{minutes}</b> minutes. You can return to <strong>{store}</strong> if you would like to submit your payment again.',[
                                'minutes' => $blockchain->invoice_expiration,
                                'store' => $store->denomination,
                            ]); ?>

                        </p>
                        <p class="lead">
                            <?= Yii::t('app','If you tried to send a payment, it has not yet been accepted by the network. We have not yet received your funds.') ?>
                        </p>

                        <p class="alert alert-info"><?= Yii::t('app','If we receive it at a later point, we will either process your order or contact you to make refund arrangements...') ?>
                        </p>
                        <hr class="my-4">
                        <p class="text-left"><b><?= Yii::t('app','Invoice ID:') ?> </b> <?= app\components\WebApp::encrypt($invoice->id) ?></p>
                        <p class="text-left"><b><?= Yii::t('app','Store:') ?> </b> <?= $store->denomination ?></p>

                    </div>
                </section>
            </div>
            <div class="card-footer">
                <div class="form-group row">
                    <div class="col-lg-offset-1 col-lg-12">
                        <?php if (!Yii::$app->user->isGuest) : ?>
                            <a href="<?= Url::to(['keypad/index']) ?>">
                                <?= Html::Button(Yii::t('app','Back to {pos}',['pos' => $pos->denomination]), ['class' => 'button circle block orange', 'name' => 'login-button']) ?>
                            </a>
                        <?php else: ?>
                            <a href="javascript: history.back();">
                                <?= Html::Button(Yii::t('app','Go back'), ['class' => 'button circle block yellow', 'name' => 'back-button']) ?>
                            </a>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
