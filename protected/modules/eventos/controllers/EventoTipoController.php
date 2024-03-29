<?php

class EventoTipoController extends AweController {
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '//layouts/column2';

    /**
     * @var bool the type of menu
     */
    public $admin = true;
    public $defaultAction = 'admin';

    public function filters()
    {
        return array(
            array('CrugeAccessControlFilter'),
        );
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        $model = new EventoTipo;
        
        if(isset($_POST['EventoTipo']))
        {
            $model->attributes = $_POST['EventoTipo'];
            if($model->save()) {
                $this->redirect(array('admin'));
            }
        }

        $this->render('create',array(
                'model' => $model,
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id)
    {
        $model = $this->loadModel($id);


        if(isset($_POST['EventoTipo']))
        {
            $model->attributes = $_POST['EventoTipo'];
            if($model->save()) {
                $this->redirect(array('admin'));
            }
        }

        $this->render('update',array(
            'model' => $model,
        ));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id)
    {
        if(Yii::app()->request->isPostRequest)
        {
            // we only allow deletion via POST request
            $this->loadModel($id)->delete();

            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if(!isset($_GET['ajax']))
                    $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
        }
        else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }

    /**
     * Manages all models.
     */
    public function actionAdmin()
    {
        $model = new EventoTipo('search');
        $model->unsetAttributes(); // clear any default values
        if(isset($_GET['EventoTipo']))
                $model->attributes = $_GET['EventoTipo'];

        $this->render('admin', array(
                'model' => $model,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id, $modelClass=__CLASS__)
    {
        $model = EventoTipo::model()->findByPk($id);
        if($model === null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param CModel the model to be validated
     */
    protected function performAjaxValidation($model, $form=null)
    {
        if(isset($_POST['ajax']) && $_POST['ajax'] === 'evento-tipo-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
}
