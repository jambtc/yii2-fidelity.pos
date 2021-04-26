<?php

namespace app\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use app\models\NotificationsReaders;
use app\models\search\NotificationsReadersSearch;


use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BoltTokensController implements the CRUD actions for BoltTokens model.
 */
class MessagesController extends Controller
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
     * Lists all BoltTokens models.
     * @return mixed
     */
    public function actionIndex()
    {
        // $dataProvider = new ActiveDataProvider([
        //     'query' => Notifications::find()
        //         //->andWhere(['id_user' => Yii::$app->user->id])
        //         //->orderBy(['id_notification' => SORT_DESC])
        // ]);
        //
        // return $this->render('index', [
        //     'dataProvider' => $dataProvider,
        // ]);
        $searchModel = new NotificationsReadersSearch();
        // echo '<pre>'.print_r(Yii::$app->request->queryParams,true);exit;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        //

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single BoltTokens model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Deletes an existing Notifications model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete()
    {
        //echo '<pre>'.print_r($_POST,true);
        $json = json_decode($_POST['keys']);

        foreach ($json as $idx => $key)
            $this->findModel($key)->delete();

        return $this->redirect(['index']);
    }




    /**
     * Finds the BoltTokens model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Notifications the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = NotificationsReaders::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
