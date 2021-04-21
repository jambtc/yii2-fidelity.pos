<?php

namespace app\controllers;

use Yii;
use app\models\Invoices;
use app\models\Pos;
use app\models\search\InvoicesSearch;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\components\Settings;
use app\components\WebApp;
use app\components\Messages;
use app\components\Seclib;
use yii\helpers\Url;
use yii\web\Response;

/**
 * InvoicesController implements the CRUD actions for Invoices model.
 */
class InvoicesController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Invoices models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new InvoicesSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->setPagination(['pageSize' => 20]);
		$dataProvider->sort->defaultOrder = ['invoice_timestamp' => SORT_DESC];

        $dataProvider->query->andWhere(['=','id_user', Yii::$app->user->id]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Invoices model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel(\app\components\WebApp::decrypt($id)),
        ]);
    }

     /**
 	 * CREA UNA invoice su Invoices con stato new che verrà mostrata
 	 * per il successivo pagamento tramite qr-code
 	 * @param _POST['amount'] the amount to be send
 	 * @param _POST['wallet_address'] the wallet_address will receive
 	 * @param _POST['id_pos'] the pos creating invoice
 	 * @exec  il comando in background di ricerca di una transazione
 	 * @return URL con la pagina per visualizzare il qrcode
 	 */
    public function actionCreate()
    {
        $model = new Invoices();

        $pos = Pos::find(['id' => $_POST['posId']])->one();
        $store = $pos->store;
        $blockchain = Settings::poa($store->id_blockchain);

        $timestamp = time();
        $expiration = $timestamp + $blockchain->invoice_expiration*60;

        $model->id_user = Yii::$app->user->id;
        $model->status = 'new';
        $model->price = $_POST['amount'];
        $model->received = 0;
        $model->id_pos = $pos->id;
        $model->invoice_timestamp = $timestamp;
        $model->expiration_timestamp = $expiration;
        $model->from_address = '';
        $model->to_address = $store->wallet_address;
        $model->txhash = '';
        $model->message = Yii::t('app','Pay to: ') . $store->denomination;

        if ($model->save()) {
            // notifica per chi ha generato l'invoice (to_address)
    		$notification = [
                'timestamp' => time(),
    			'type' => 'invoice',
                'status' => 'new',
                'description' => Yii::t('app','You have generated a new invoice.'),
    			'url' => Url::to(["/invoices/view",'id'=>WebApp::encrypt($model->id)]),
    			'price' => $model->price,
    			'id_user' => Yii::$app->user->id,
    		];
    		Messages::push($notification);

            //eseguo lo script che si occuperà in background di verificare lo stato dell'invoice appena creata...
			$cmd = Yii::$app->basePath.DIRECTORY_SEPARATOR.'yii receive '.WebApp::encrypt($model->id);
			Seclib::execInBackground($cmd);

            return $this->redirect(['/qrcode/view', 'id' => WebApp::encrypt($model->id)]);
        } else {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['error'=>Yii::t('app','Error! Cannot create invoice!')];
        }
    }

    /**
     * Update Invoices model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionExpired()
    {
        $model = $this->findModel(\app\components\WebApp::decrypt($_POST['id']));
        if ($model->status == 'new'){
            $model->status = 'expired';
            $model->update();
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['status'=> 'expired'];
    }

    /**
     * Update Invoices model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionCheckStatus()
    {
        $model = $this->findModel(\app\components\WebApp::decrypt($_POST['id']));

        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['status'=> $model->status];
    }


    /**
     * Finds the Invoices model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Invoices the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Invoices::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
