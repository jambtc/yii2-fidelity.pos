<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Url;
use yii\web\View;


$this->title = Yii::t('app','Register');

?>
<div class="container h-100">
  <div class="row h-100 justify-content-center align-items-center">
    <div class="site-login">
      <div class="body-content dash-balance jumbotron pb-5">

          <div class="text-center">
              <img src="css/images/logo.png" alt="" width="220">
          </div>
          <div class="form-divider"></div>
          <h3 class="alert alert-info"><?= Yii::t('app','Registration form') ?></h3>

          <p class="text-light"><?= Yii::t('app','Please fill out the fields. A link to activate your account will be sent.') ?></p>

          <?php if (Yii::$app->session->hasFlash('registerFormSubmitted')): ?>

              <div class="alert alert-success">
                  <?php echo Yii::t('app','Your registration request has been registered.');?><br>
                  <?php echo Yii::t('app','You will receive an email to confirm your subscription.');?>
              </div>
              <div class="form-row txt-center text-light mt-15">
                  <?php echo Yii::t('app','Back to home!'); ?>
                 <a style="color:#007bff;" href="<?php echo Url::to(['site/index']); ?>" data-loader="show">Home</a>
              </div>



          <?php else: ?>


        <?php $form = ActiveForm::begin([
            'id' => 'register-form',
            'layout' => 'horizontal',
            'fieldConfig' => [
        		'template' => "{label}\n<div class=\"col-lg-12\">{input}</div>\n{error}\n<div class=\"col-lg-8\">{error}</div>",
        		'labelOptions' => ['class' => 'col-lg-1 control-label'],
        	],
        ]); ?>

        <?php $fieldOptions1 = [
            'inputTemplate' => '
                                <div class="form-row-group with-icons">
                                    <div class="form-row no-padding">
                                        <i class="fa fa-envelope"></i>
                                        {input}
                                    </div>
                                </div>',
            'inputOptions' => ['class' => ['widget' => 'form-element']]

        ];
          ?>
          <?php $fieldOptions2 = [
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

        <?= $form->field($model, 'username', $fieldOptions1)->textInput(['autofocus' => false, 'autocomplete'=>"off"]) ?>
        <?= $form->field($model, 'password', $fieldOptions2)->passwordInput(['autocomplete'=>"new-password"]) ?>

        <div class="txt-left">
            <?= $form->errorSummary($model, ['id' => 'error-summary','class'=>'col-lg-12']) ?>
        </div>



        <div class="form-mini-divider"></div>


        <div class="form-group row">
            <div class="col-lg-offset-1 col-lg-11">
                <?= Html::submitButton('Register', ['class' => 'button circle block orange', 'name' => 'login-button']) ?>
            </div>
        </div>


        <div class="form-row txt-center text-light mt-15">
          Already have an account? <a style="color:#007bff;" href="<?php echo Url::to(['site/login']); ?>" data-loader="show">Login</a>
        </div>
        <?php ActiveForm::end(); ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
