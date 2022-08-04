<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\UsersList;
use GuzzleHttp\Psr7\AppendStream;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * UsersController implements the CRUD actions for User model.
 */
class UsersController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::className(),
                    'only' => ['update', 'delete', 'view'],
                    'rules' => [
                        [
                            'actions' => ['update','delete', 'view'],
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all User models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new UsersList();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param int $id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post())) {
            // неизменённый пароль не перезаписывается
            if ($model->hashed_password == $this->request->post()['User']['hashed_password'] ) {
                $model->username = $this->request->post()['User']['username'];
                if ($model->save()) {
                    return $this->redirect(['view', 'id' => $model->id]);
                } 
            }
            // изменённый - хэшируется и перезаписывается
            if ($this->request->post()['User']['hashed_password'] == $this->request->post()['User']['confirm_password']) {
                $model->username = $this->request->post()['User']['username'];
                $model->hashed_password = Yii::$app->getSecurity()->generatePasswordHash($this->request->post()['User']['hashed_password']);
                $model->confirm_password = '';
                if ($model->save()) {
                    return $this->redirect(['view', 'id' => $model->id]);
                } 
            } else {
                $model->confirm_password = 'Пароли не совпадают!';
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
