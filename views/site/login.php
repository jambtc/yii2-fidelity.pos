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
?>
<div class="container h-100">
  <div class="row h-100 justify-content-center align-items-center">
    <div class="site-login">
      <div class="body-content dash-balance jumbotron pb-5">
          <div class="login-logo text-left text-light" style="font-size: xx-large;">
              <img src="css/images/logo.png" alt="" width="100">
              <?= Yii::$app->name ?>
          </div>
          <div class="form-divider"></div>
          <p class="text-light text-center"><?= Yii::t('app','Sign in to start your session') ?></p>
        <div class="form-divider"></div>

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
            <div class="col-lg-offset-1 col-lg-11">
                <?= Html::submitButton(Yii::t('app','Login'), [
                    'class' => 'btn btn-primary',
                    'name' => 'login-button',
                    ]) ?>
            </div>
        </div>

        <div class="form-divider"></div>
        <?php ActiveForm::end(); ?>
      </div>
    </div>
  </div>
</div>
