<?php

/* @var $this yii\web\View */
/* @var $templates array */
/* @var $template \app\models\MainTemplate */
/* @var $users array */
/* @var $user \app\models\User */

use yii\helpers\Html;
use yii\bootstrap4\Modal;


?>
<div class="row">
    <?php foreach ($templates as $template): ?>
        <div class="col-sm-4 modalTemplateBlock" data-user="<?=$template->userId?>">
            <div class="templateModalPrev">
                <?php if($template->userId === Yii::$app->user->getId()):?>
                    <a href="#" data-id="<?=$template->id?>" class="deleteTemplate float-right" title="Удалить шаблон"><small>Уд.</small></a>
                <?php endif;?>
                <div class="main-template-title"> <?=$template->name?></div>
                <?php if(array_key_exists($template->userId, $users) && ($user = $users[$template->userId])): ?>
                    <?=$user->name?>
                <?php endif; ?>
                <small><em><?=date('m.d H:i')?></em></small>
                <div  class="text-center">
                    <a href="<?=\yii\helpers\Url::to(['/draw/draw', 'id' => $template->id])?>" target="_blank">
                        <?=\app\models\MainTemplate::returnCodeHtml($template->code)?>
                    </a>
                </div>


            </div>
        </div>
    <?php endforeach; ?>
</div>