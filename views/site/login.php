<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Url;
use yii\web\View;

app\assets\ServiceWorkerAsset::register($this);
app\assets\SinAsset::register($this);

$this->title = 'Login';

if (isset($_GET['sin']))
    $model->sin = $_GET['sin'];

?>
<div class="container h-100">
  <div class="h-100 justify-content-center align-items-center">
    <div class="site-login">
      <div class="body-content ref-card c1 jumbotron pb-5">
          <div class="text-center">
            <a href="/">
                <img src="css/images/logo.png" alt="" width="220">
                <div class="form-divider"></div>
                <h3  class="txt-white"><?php echo Yii::$app->id; ?></h3>
                <div class="form-divider"></div>
            </a>
            <h5 class="txt-white">
              <?= Yii::t('app','Sign in to start your session') ?>
            </h5>
        </div>

        <div class="form-divider"></div>
        <div class="form-row">
            <?php $form = ActiveForm::begin([
                'id' => 'login-form',
                'layout' => 'horizontal',
                'fieldConfig' => [
            		'template' => "{label}\n<div class=\"col-lg-12\">{input}</div>\n{error}\n<div class=\"col-lg-8\">{error}</div>",
            		'labelOptions' => ['class' => 'col-lg-1 control-label'],
            	],
            ]); ?>
            <div class="txt-left">
                <?= $form->errorSummary($model, ['id' => 'error-summary','class'=>'col-lg-12']) ?>
            </div>

            <?php $fieldOptions1 = [
                'inputTemplate' => '
                                    <div class="form-row-group with-icons">
                                        <div class="form-row no-padding">
                                            <i class="fa fa-lock"></i>
                                            {input}
                                        </div>
                                    </div>',
                'inputOptions' => ['class' => ['widget' => 'form-element']]

            ];
              ?>

            <?= $form->field($model, 'sin', $fieldOptions1)->textInput(['autofocus' => false, 'autocomplete'=>"off"]) ?>

            <div class="form-group row">
                <div class="col-sm-12">
                    <?= Html::submitButton(Yii::t('app','Login'), [
                        'class' => 'btn btn-primary btn-block',
                        'name' => 'login-button',
                        ]) ?>
                </div>
            </div>

            <div class="form-divider"></div>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="form-divider"></div>
        <div class="form-divider"></div>
      </div>
    </div>
  </div>
</div>
