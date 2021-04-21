<?php

namespace app\controllers;

use Yii;
use app\models\Pos;
use app\models\search\PosSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\components\WebApp;
use app\models\Merchants;
use yii\helpers\Json;
use app\models\Stores;
use yii\helpers\ArrayHelper;


/**
 * PosController implements the CRUD actions for Pos model.
 */
class KeypadController extends Controller
{

    /**
     * Lists all Pos models.
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }


}
