<?php

class AdminController extends Controller {

       /**
        * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
        * using two-column layout. See 'protected/views/layouts/column2.php'.
        */
       public $layout = '//layouts/column2MasterData';

       /**
        * @return array action filters
        */
       public function filters() {
              return array(
                  'accessControl', // perform access control for CRUD operations
                  'postOnly + delete', // we only allow deletion via POST request
              );
       }

       /**
        * Specifies the access control rules.
        * This method is used by the 'accessControl' filter.
        * @return array access control rules
        */
       public function accessRules() {
              return array(
                  array('allow', // allow all users to perform 'index' and 'view' actions
                      'actions' => array('index', 'view', 'login'),
                      'users' => array('*'),
                  ),
                  array('allow', // allow authenticated user to perform 'create' and 'update' actions
                      'actions' => array('update', 'create', 'listPendaftaran', 'manageMasterData', 'dashboard', 'universitasAsal'),
                      'roles' => array('1'),
                  ),
                  array('allow', // allow admin user to perform 'admin' and 'delete' actions
                      'actions' => array('admin', 'delete'),
                      'users' => array('admin'),
                  ),
                  array('deny', // deny all users
                      'users' => array('*'),
                  ),
              );
       }

       /**
        * Displays a particular model.
        * @param integer $id the ID of the model to be displayed
        */
       public function actionView($id) {
              $this->render('view', array(
                  'model' => $this->loadModel($id),
              ));
       }

       /**
        * Creates a new model.
        * If creation is successful, the browser will be redirected to the 'view' page.
        */
       public function actionCreate() {
              $model = new TimAdministrasi;

              // Uncomment the following line if AJAX validation is needed
              // $this->performAjaxValidation($model);

              if (isset($_POST['TimAdministrasi'])) {
                     $model->attributes = $_POST['TimAdministrasi'];
                     $pass_nosalt = $model->password;
//                     echo 'raw_password : '.$pass_nosalt.'<br />';
                     $model->salt = $model->generateSalt();
//                     echo 'salt : '.$model->salt.'<br />';
                     $model->password = $model->hashPassword($pass_nosalt, $model->salt);
//                     echo 'password : '.$model->password;
                     if ($model->save()) {
                            $this->redirect(array('view', 'id' => $model->id_tim_administrasi));
                     }
              }

              $this->render('create', array(
                  'model' => $model,
              ));
       }
       
       /**
        * Creates a new model.
        * If creation is successful, the browser will be redirected to the 'view' page.
        */
       public function actionManageMasterData() {
              $this->render('masterDataIndex');
       }

       /**
        * Updates a particular model.
        * If update is successful, the browser will be redirected to the 'view' page.
        * @param integer $id the ID of the model to be updated
        */
       public function actionUpdate($id) {
              $model = $this->loadModel($id);

              // Uncomment the following line if AJAX validation is needed
              // $this->performAjaxValidation($model);

              if (isset($_POST['TimAdministrasi'])) {
                     $model->attributes = $_POST['TimAdministrasi'];
                     if ($model->save())
                            $this->redirect(array('view', 'id' => $model->id_tim_administrasi));
              }

              $this->render('update', array(
                  'model' => $model,
              ));
       }

       /**
        * Deletes a particular model.
        * If deletion is successful, the browser will be redirected to the 'admin' page.
        * @param integer $id the ID of the model to be deleted
        */
       public function actionDelete($id) {
              $this->loadModel($id)->delete();

              // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
              if (!isset($_GET['ajax']))
                     $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
       }

       /**
        * Lists all models.
        */
       public function actionIndex() {
              if (isset(Yii::app()->user->roles)) {
                     if(Yii::app()->user->roles == 1)
                     {
                            $this->redirect(Yii::app()->getBaseUrl(true) . '/index.php/dashboard');
                     }
                     if(Yii::app()->user->roles == Yii::app()->params['analis'])
                     {
                            $this->redirect(Yii::app()->getBaseUrl(true) . '/index.php/dashboardAnalis');
                     }
                     if(Yii::app()->user->roles == Yii::app()->params['reviewer'])
                     {
                            $this->redirect(Yii::app()->getBaseUrl(true) . '/index.php/dashboardReviewer');
                     }
                     if(Yii::app()->user->roles == Yii::app()->params['direktur'])
                     {
                            $this->redirect(Yii::app()->getBaseUrl(true) . '/index.php/dashboardDirektur');
                     }
              } 
              else 
              {
                     $this->redirect(Yii::app()->getBaseUrl(true) . '/index.php/admin/login');
              }
       }

	public function actionDashboard() {
              $this->render('dashboard');
       }

       /**
        * Manages all models.
        */
       public function actionListPendaftaran() {
              $model = new PendaftaranBeasiswa('search');
              $model->unsetAttributes();  // clear any default values
              if (isset($_GET['PendaftaranBeasiswa']))
                     $model->attributes = $_GET['PendaftaranBeasiswa'];

              $this->render('listPendaftaran', array(
                  'model' => $model,
              ));
       }

       /**
        * Returns the data model based on the primary key given in the GET variable.
        * If the data model is not found, an HTTP exception will be raised.
        * @param integer the ID of the model to be loaded
        */
       public function loadModel($id) {
              $model = TimAdministrasi::model()->findByPk($id);
              if ($model === null)
                     throw new CHttpException(404, 'The requested page does not exist.');
              return $model;
       }

       /**
        * Performs the AJAX validation.
        * @param CModel the model to be validated
        */
       protected function performAjaxValidation($model) {
              if (isset($_POST['ajax']) && $_POST['ajax'] === 'tim-administrasi-form') {
                     echo CActiveForm::validate($model);
                     Yii::app()->end();
              }
       }

       public function actionLogin() {
              $model = new LoginFormAdministrasi;

              // if it is ajax validation request
              if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
                     echo CActiveForm::validate($model);
                     Yii::app()->end();
              }

              // collect user input data
              if (isset($_POST['LoginFormAdministrasi'])) {

                     $model->attributes = $_POST['LoginFormAdministrasi'];
                     // validate user input and redirect to the previous page if valid
                     if ($model->validate() && $model->login()) {
                            $this->redirect(Yii::app()->homeUrl . '/admin');
                     }
              }
              // display the login form
              $this->render('login', array('model' => $model));
       }
       
        //called on rendering the column for each row 
       protected function namaLengkap($data, $row) {
              // ... generate the output for the column
              // Params:
              // $data ... the current row data   
              // $row ... the row index 
              $user=User::model()->findByPk($data->id_user);
              
              return $user->nama_lengkap;
       }
       
        //called on rendering the column for each row 
       protected function programBeasiswa($data, $row) {
              // ... generate the output for the column
              // Params:
              // $data ... the current row data   
              // $row ... the row index 
              $programBeasiswa=ProgramBeasiswa::model()->findByPk($data->id_program_beasiswa);
              
              return $programBeasiswa->program_beasiswa;
       }
       
        //called on rendering the column for each row 
       protected function prodi($data, $row) {
              // ... generate the output for the column
              // Params:
              // $data ... the current row data   
              // $row ... the row index 
              $prodi=Prodi::model()->findByPk($data->id_prodi);
              
              return $prodi->prodi;
       }
       
        //called on rendering the column for each row 
       protected function universitasAsal($data, $row) {
              // ... generate the output for the column
              // Params:
              // $data ... the current row data   
              // $row ... the row index 
              $user = User::model()->findByPk($data->id_user);
              $universitasAsal=UniversitasAsal::model()->findByPk($user->id_universitas_asal);
              
              return $universitasAsal->universitas_asal;
       }

	/**
	 * Lists all models.
	 */
	public function actionUniversitasAsal()
	{
		$model=new UniversitasAsal('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['UniversitasAsal']))
			$model->attributes=$_GET['UniversitasAsal'];

              $model2=new UniversitasAsal;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['UniversitasAsal']))
		{
			$model2->attributes=$_POST['UniversitasAsal'];
			if($model2->save())
				$this->redirect(array('index', 'pesan' => 'penambahan data berhasil dilakukan'));
		}
              
		$this->render('universitasAsal',array(
			'model'=>$model, 'model2'=>$model2
		));
	}


}
