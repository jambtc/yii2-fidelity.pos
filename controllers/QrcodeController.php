<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\web\HttpException;
use yii\filters\VerbFilter;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;

use app\models\Invoices;
use app\components\WebApp;


class QrcodeController extends Controller
{
	public function beforeAction($action)
	{
    	$this->enableCsrfValidation = false;
    	return parent::beforeAction($action);
	}


	/**
	 * {@inheritdoc}
	 */
	public function actions()
	{
		return [
			'error' => [
				'class' => 'yii\web\ErrorAction',
			],
			'captcha' => [
				'class' => 'yii\captcha\CaptchaAction',
				'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
			],
		];
	}


	public function actionView($id)
	{
		$this->layout='auth';

		$invoice = Invoices::findOne(WebApp::decrypt($id));

		if (null === $invoice)
			return $this->redirect(['site/error']);


		$pos = $invoice->pos;
		$store = $pos->store;
		$blockchain = $store->blockchain;

		return $this->render('index', [
			'invoice' => $invoice,
			'pos' => $pos,
			'store' => $store,
			'blockchain' => $blockchain
		]);
	}






}
