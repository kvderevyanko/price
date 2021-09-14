<?php

namespace app\controllers;

use app\models\MainTemplate;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\httpclient\Client;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
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
                   // 'sync-base' => ['post'],
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

    public function actionSavePrice($id, $contrast = 125, $blue = true, $bluePwm = 1000){
        $template = MainTemplate::findOne($id);
        if ($template === null) {
            throw new NotFoundHttpException("Шаблон не найден");
        }
        $code = "";
        if($template->code) {
            try {
                $code = Json::decode($template->code);
            } catch (\Exception $e) {}
        }

        if(!is_array($code)) {
            throw new NotFoundHttpException('$code не массив');
        }

        $zipLines = [];
        foreach ($code as $lineKey => $codeLine) {
            $zipLines[] = $this->zipLine($codeLine);
        }

        $request = [
            'contrast' => $contrast,
            'bluePwm' => $bluePwm,
            'blue' => $blue?1:0,
            'cl' => '',
            'part' => 0,
        ];

        $a = [];
        //Разбиваем на 5 массивов, и каждый посылаем отдельно
        $a[1] = array_slice($zipLines, 0, 10);
        $a[2] = array_slice($zipLines, 10, 10);
        $a[3] = array_slice($zipLines, 20, 10);
        $a[4] = array_slice($zipLines, 30, 10);
        $a[5] = array_slice($zipLines, 40, 10);

        $client = new Client();

        for ($i = 1; $i <= 5; $i++) {
            $request['cl'] =  implode(',', $a[$i]);;
            $request['part'] =  $i;
            usleep(100);
            $response = $client->createRequest()
                ->setMethod('POST')
                ->setUrl('http://192.168.1.27/nokia5110-save.lua')
                ->setData($request)
                ->setOptions([
                    'timeout' => 5, // set timeout to 5 seconds for the case server is not responding
                ])
                ->send();
            if ($response->isOk) {
                print_r($response->content);
            } else {
                print_r($response);
            }
        }




    }

    //Отсылка прайса по частям
    private function sendPricePart(){

    }

    public function actionShowPrice(){
        usleep(100);
        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('GET')
            ->setUrl('http://192.168.1.27/nokia5110-load.lua')
            ->setOptions([
                'timeout' => 10, // set timeout to 5 seconds for the case server is not responding
            ])
            ->send();
        if ($response->isOk) {
            print_r($response->content);
        } else {
            print_r($response);
        }

    }

    public function actionSyncBase (){
        $total = 0;

        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('GET')
            ->setUrl('https://art-street.ru/site/sync-base-json')
            ->setOptions([
                'timeout' => 10, // set timeout to 5 seconds for the case server is not responding
            ])
            ->send();
        if ($response->isOk) {
            try {
                $content = Json::decode($response->content);
                if(is_array($content)) {
                    foreach ($content as $t) {
                        $template = MainTemplate::findOne($t['id']);
                        if($template === null) {
                            $template = new MainTemplate();
                        }
                        $template->attributes = $t;
                        if($template->save()) {
                            $total++;
                        }
                    }
                }
            } catch (\Exception $e) {
                echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
            }


        } else {
            print_r($response);
        }
        return $total;
    }


    /**
     * Сжимаем строку.
     * Алгоритм - начинаем с 0
     * Определяем количество символов. Потом 1, потом опять 0
     * 001111001 = 2421
     * 110001001 = 023121
     */
    private function zipLine($lineArray){
        //$lineArray = [0,0,1,1,1,1,0,0,1];
        //print_r($lineArray);
        $lastChar = 0;
        $resultString = "";
        $count = 0;
        foreach (array_values($lineArray) as $key => $char) {
            if($lastChar === $char) {
                $count++;
            } else {
                $resultString .= (string)$count.'.';
                $lastChar = $char;
                $count = 1;
            }
        }
        $resultString .= (string)$count;
        return $resultString;
    }


    private function sendLine($str, $lineCount) {
        $params = ['cl' => implode('', $str), 'line' => $lineCount];

        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl('http://192.168.1.27/nokia5110.lua')
            ->setData($params)
            ->send();
        if ($response->isOk) {
            echo time()." - Удачная попытка отправки строки";
            return true;
        } else {
            echo time()." - ошибка отправки строки";
            sleep(1);
            return $this->sendLine($str, $lineCount);
        }
    }

    /**
     * считываем количество нулей в строке
     * @param $str
     * @return int
     */
    private function countZero($str){
        preg_match('/^0+/', $str, $res);
        if(count($res) > 0) {
            return strlen($res[0]);
        } else {
            return 0;
        }
    }
}
