<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

use app\models\Users;
use app\components\Rows;
use app\components\WebApp;

/* @var $this yii\web\View */
/* @var $searchModel app\models\BoltTokensSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Invoices');
?>
<div class="dash-balance ">
    <div class="dash-content relative">
		<h3 class="w-text"><?= Yii::t('app','Invoice list');?></h3>
	</div>

    <section class="mb-2">

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            // 'filterModel' => $searchModel,
            'showHeader'=> false,
            'tableOptions' => ['class' => 'table-96 table table-sm mb-3 ml-1 mr-1'],
            'columns' => [
                [
                   'attribute'=>'',
                   'format' => 'raw',
                   'class' => 'yii\grid\DataColumn', // can be omitted, as it is the default
                   'value' => function ($data) {
                      return app\components\WebApp::showInvoiceRow($data);
                   },
                ],
            ],
        ]); ?>


        <div class="form-divider"></div>
    </section>


</div>
