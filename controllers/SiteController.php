<?php
namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use \yii\web\Cookie;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\LoginForm;

use app\models\Users;

use app\components\WebApp;



define ('NONCE_TIMEOUT', 24 * 60 * 60); // 1 day

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                    // 'activate' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
                'layout' => 'auth',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'onAuthSuccess'],
            ]
        ];
    }

    public function onAuthSuccess($client)
    {
        (new AuthHandler($client))->handle();
    }

    public function beforeAction($action)
	{
    	$this->enableCsrfValidation = false;
    	return parent::beforeAction($action);
	}

    // private static function setCookieForGoogleLogout()
    // {
    //   setcookie('G_AUTHUSER_LOGOUT','AVOID');
    // }

    public function actionError(){
        $this->layout = 'auth';
        return $this->render('error');
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $this->layout = 'auth';

        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['keypad/index']);
        }
        return $this->render('index');
    }


    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        $this->layout = 'auth';

        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        // echo "<pre>".print_r($_POST,true)."</pre>";
		// exit;

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            // return $this->goBack();
            return $this->redirect(['keypad/index']);
        }

        // $this->setCookieForGoogleLogout();

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

}
