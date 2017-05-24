<?php

namespace app\controllers;

use Yii;
use app\models\Profesor;
use app\models\SearchProfesor;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Persona;
use app\models\Usuario;
use app\models\Model;
use app\models\ProfesorGrupoPeriodo;

/**
 * ProfesorController implements the CRUD actions for Profesor model.
 */
class ProfesorController extends Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {
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
     * Lists all Profesor models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new SearchProfesor();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Profesor model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Profesor model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Profesor();
        $persona = new Persona();
        $usuario = new Usuario();

        $profesorGrupoPeriodos = [new ProfesorGrupoPeriodo()];

        // SE ACTIVA ESTO CUANDO SE CREA UN PROFESOR DESDE EL MODULO PROFESORES
        $model->enIntegradora = 1;

        if ($model->load(Yii::$app->request->post()) &&
                $persona->load(Yii::$app->request->post()) &&
                $usuario->load(Yii::$app->request->post())) {

            $profesorGrupoPeriodos = Model::createMultiple(ProfesorGrupoPeriodo::className());
            Model::loadMultiple($profesorGrupoPeriodos, \Yii::$app->request->post());

            foreach ($profesorGrupoPeriodos as $pgp) {
                $pgp->idProfesor = 1;
            }

            //validacion ajax
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return \yii\helpers\ArrayHelper::merge(
                                ActiveForm::validateMultiple($profesorGrupoPeriodos), ActiveForm::validate($model), ActiveForm::validate($persona), ActiveForm::validate($usuario)
                );
            }

            // validacion php
            $valid = $model->validate() && $persona->validate() && $usuario->validate();
            $valid = Model::validateMultiple($profesorGrupoPeriodos) && $valid;

            if ($valid) {
                $model->enIntegradora = 1;
                if ($model->registrar($persona, $usuario, $profesorGrupoPeriodos, false)) {
                    return $this->redirect(['view', 'id' => $model->idProfesor]);
                }
            }
        }

        return $this->render('create', [
                    'model' => $model,
                    'persona' => $persona,
                    'usuario' => $usuario,
                    'profesorGrupoPeriodos' => $profesorGrupoPeriodos,
        ]);
    }

    /**
     * Updates an existing Profesor model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = Profesor::findOne($id);
        $usuario = $model->idProfesor0;
        $persona = $usuario->idUsuario0;

        $profesorGrupoPeriodos = $model->profesorGrupoPeriodos;

        if ($model->load(Yii::$app->request->post()) &&
                $persona->load(Yii::$app->request->post()) &&
                $usuario->load(Yii::$app->request->post())) {

            $profesorGrupoPeriodosN = Model::createMultiple(ProfesorGrupoPeriodo::className());
            Model::loadMultiple($profesorGrupoPeriodosN, \Yii::$app->request->post());

            foreach ($profesorGrupoPeriodosN as $pgp) {
                $pgp->idProfesor = $model->idProfesor;
            }

            $transaccion = Yii::$app->db->beginTransaction();

            $profesorGrupoPeriodosOld = array_udiff($profesorGrupoPeriodos, $profesorGrupoPeriodosN, ['app\models\ProfesorGrupoPeriodo', 'compare']);
            $profesorGrupoPeriodosN = array_udiff($profesorGrupoPeriodosN, $profesorGrupoPeriodos, ['app\models\ProfesorGrupoPeriodo', 'compare']);

            $profesorGrupoPeriodosN = \app\models\Utilerias::my_array_unique($profesorGrupoPeriodosN);

            //echo '<script>alert("'. $profesorGrupoPeriodosOld[0]->idGrupo.'");</script>';
            ProfesorGrupoPeriodo::eliminarMultiple($profesorGrupoPeriodosOld);

            //validacion ajax
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return \yii\helpers\ArrayHelper::merge(
                                ActiveForm::validateMultiple($profesorGrupoPeriodosN), ActiveForm::validate($model), ActiveForm::validate($persona), ActiveForm::validate($usuario)
                );
            }

            // validacion php
            $valid = $model->validate() && $persona->validate() && $usuario->validate();
            $valid = Model::validateMultiple($profesorGrupoPeriodosN) && $valid;

            if ($valid) {
                if ($model->registrar($persona, $usuario, $profesorGrupoPeriodosN, false)) {
                    $transaccion->commit();
                    return $this->redirect(['view', 'id' => $model->idProfesor]);
                }
            }
            $transaccion->rollBack();
        }

        return $this->render('update', [
                    'model' => $model,
                    'persona' => $persona,
                    'usuario' => $usuario,
                    'profesorGrupoPeriodos' => $profesorGrupoPeriodos,
        ]);
    }

    /**
     * Deletes an existing Profesor model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Profesor model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Profesor the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Profesor::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
