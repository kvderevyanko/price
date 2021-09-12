<?php

namespace app\models;

use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "main_template".
 *
 * @property int $id
 * @property int $userId
 * @property string|null $name
 * @property string|null $code
 * @property int|null $date
 */
class MainTemplate extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'main_template';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code'], 'string'],
            [['date', 'userId'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['userId'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'code' => 'Code',
            'date' => 'Date',
        ];
    }


    /**
     * Переводит код в html
     * @param $code
     * @return mixed
     */
    public static function returnCodeHtml($code){
        try {
            if($code) {
                $code = Json::decode($code);
            }

            if(!is_array($code)) {
                $code = [];
            }

        } catch (\Exception $e) {
            //echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
        }

        $content = "<table>";

        foreach ($code as $codeTr){
            $content .= "<tr>";
            foreach ($codeTr as $codeTd){
                $content .= "<td";
                //Если пиксель активный - добавляем класс
                if($codeTd) {
                    $content .= ' class="ba" ';
                }
                $content .= "></td>";
            }
            $content .= "</tr>";
        }
        $content .= "</table>";

        return $content;
    }
}
