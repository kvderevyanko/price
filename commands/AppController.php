<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;


class AppController extends Controller
{
    /**
     * Создание базы и простановка прав
     */
    public function actionStart()
    {
        chmod(\Yii::getAlias('@app')."/runtime", 0777);
        chmod(\Yii::getAlias('@app')."/web/assets", 0777);
        chmod(\Yii::getAlias('@app')."/db", 0777);

        $fileDb= \Yii::getAlias('@app')."/db/sqlite.db";
        if(!file_exists($fileDb)) {
            touch($fileDb);
            chmod($fileDb, 0777);
            //chown($fileDb, 'www-data');
            $this->stdout("Выполни!!!!!!  \nsudo chown -R www-data ".$fileDb."\n");
        }
        //chmod -R 755 db/sqlite.db
    }
}
