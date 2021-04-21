<?php

use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;

use yii\helpers\Url;
use yii\helpers\Html;

// mi serve per far caricare bootstrap4
NavBar::begin();
NavBar::end();

?>


<div class="nav-menu" style="display: none;">
	<nav class="menu">

		<!-- Menu navigation start -->
		<div class="nav-container">
			<ul class="main-menu">
				<li class="">
					<a href="<?= Url::to(['/keypad/index']) ?>">
						<i class="fa fa-lg text-primary fa-tablet-alt"></i>
						<strong class="special">
							<?= Yii::t('app','Keypad') ?>
						</strong>
					</a>
				</li>

				<li class="">
					<a href="<?= Url::to(['/invoices/index']) ?>">
						<i class="fa fa-list fa-lg text-primary"></i>
						<strong class="special">
							<?= Yii::t('app','Transactions') ?>
						</strong>
					</a>
				</li>
				<li>
					<?php
			             echo Html::beginForm(['/site/logout'], 'post')
			                . '<i class="fa fa-lg fa-sign-out-alt text-primary mx-3"></i>'

							. Html::submitButton(
			                  'Logout (' . Yii::$app->user->identity->first_name . ')',
			                  ['class' => 'btn btn-link logout']
			                  )
			                  . Html::endForm();
					?>
			     </li>
			</ul>
		</div>
	<!-- Menu navigation end -->
	</nav>
</div>
