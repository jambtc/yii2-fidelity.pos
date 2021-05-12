<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

use app\components\WebApp;
use app\components\Rows;
use app\models\Users;
use app\models\Pos;

$this->title = Yii::t('app','Transaction details') .' - '. $model->id;
\yii\web\YiiAsset::register($this);

?>
<div class="dash-balance">
    <div class="ref-card c2 mb-3">
		<div class="dash-content relative">
			<h3 class="w-text"><?= Yii::t('app','Transaction details');?></h3>
		</div>
	</div>
    <section class="trans-sec mt-0 purp" style="padding:15px 0px 0px 0px !important;">

		<div class="ref-card ">
			<div class="d-flex align-items-center">
                <div class="d-flex flex-grow">
                  <div class="mr-auto">
                      <?= DetailView::widget([
                          'model' => $model,
                          'attributes' => [
                              //'id',
                              //'id_user',
                              //'status',
                              [
                                  'attribute' => 'status',
                                  'format' => 'raw',
                                  'value' => function ($data) {
                                      $color = Rows::statuscolor($data->status);
                                      $tag = Html::a($data->status, Url::to(['/qrcode/view','id'=>WebApp::encrypt($data->id)]), ['target'=>'_self']);
                                      $row = '<span class="btn btn-'.$color.'">'.$tag.'</span>';

                                      return $row;
                                  },
                              ],
                              'invoice_timestamp:datetime',
                              'price',
                              'received',
                              [
                                  'type'=>'raw',
                                  'attribute'=>Yii::t('app','from_address'),
                                  'value'=>$model->from_address,
                                  'contentOptions' => ['class' => 'text-break']
                              ],
                              [
                                  'type'=>'raw',
                                  'attribute'=>Yii::t('app','to_address'),
                                  'value'=>$model->to_address,
                                  'contentOptions' => ['class' => 'text-break']
                              ],
                              [
                                  'type'=>'raw',
                                  'attribute'=>Yii::t('app','txhash'),
                                  'value'=>$model->txhash,
                                  'contentOptions' => ['class' => 'text-break']
                              ],
                              [
                                  'type'=>'raw',
                                  'attribute'=>Yii::t('app','message'),
                                  'value'=>$model->message,
                                  'contentOptions' => ['class' => 'text-break']
                              ],
                          ],
                      ]) ?>
                  </div>
                </div>
             </div>
		</div>

	</section>





</div>
