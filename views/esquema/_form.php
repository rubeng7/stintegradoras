<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Esquema */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="esquema-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'noIntegradora')->dropDownList([ 'Integradora I' => 'Integradora I', 'Integradora II' => 'Integradora II', 'Integradora III' => 'Integradora III', 'Integradora IV' => 'Integradora IV', 'Integradora V' => 'Integradora V', 'Integradora VI' => 'Integradora VI', 'Integradora VII' => 'Integradora VII', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'noFases')->dropDownList([ 1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6', 7 => '7', 8 => '8', 9 => '9', 10 => '10', 11 => '11', 12 => '12', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'fechaCreacion')->textInput() ?>

    <?= $form->field($model, 'idCarrera')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>