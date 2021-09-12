<?php

namespace app\controllers;

use app\models\MainTemplate;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class DrawController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
              //  'only' => ['logout'],
                'rules' => [
                    [
                       // 'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete-main-template' => ['post'],
                ],
            ],
        ];
    }


    public function actionDraw($id = null)
    {
        $codeGenerate = null;
        if($id) {
            $template = MainTemplate::findOne($id);
            if($template) {
                $codeGenerate = $template->code;
            }
        }


        $users = User::find()->all();
        return $this->render('draw', ['users' => $users, 'codeGenerate' => $codeGenerate]);
    }


    public function actionSaveMainTemplate (){
        $code = Yii::$app->request->post('code');
        $name = Yii::$app->request->post('name');

        if($name) {
            $template = new MainTemplate();
            $template->userId = Yii::$app->user->getId();
            $template->code = $code;
            $template->name = $name;
            $template->date = time();
            $template->save();

            print_r($template->errors);
            return 1;
        }

        return 0;
    }

    public function actionLoadMainTemplate (){
        $templates = MainTemplate::find()->orderBy(['id' => SORT_DESC])->all();
        $users = User::find()->all();
        $users = ArrayHelper::index($users, 'id');
        return $this->renderPartial('load-main-template', ['templates' => $templates, 'users' => $users]);
    }

    public function actionDeleteMainTemplate (){
        $id = Yii::$app->request->post('id');
        if($id) {
            $template = MainTemplate::findOne(['id' => $id, 'userId' => Yii::$app->user->getId()]);
            if($template) {
                $template->delete();
                return 1;
            }
        }
        return 0;
    }
}
