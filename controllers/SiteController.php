<?php

namespace app\controllers;

use app\models\User;
use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\models\LoginForm;
use app\models\RedisHelper;

class SiteController extends Controller
{

    private $redisHelper;
    private $user;

    public function __construct( $id, $module, RedisHelper $helper, User $user, array $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->redisHelper = $helper;
        $this->user = $user;
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        $form = new LoginForm();
        $data = Yii::$app->request->post('LoginForm');
        if ($data){
            $id = rand();
            $obj = (object)['name' => $data['name'], 'handState' => 0];

            if ($this->user->isUserExist($data['name'])){
                \Yii::$app->session->setFlash('danger', 'User with such name is already exists!');
                return $this->refresh();
            }

            $this->user->addUserToSet($id, $obj);
            //$user->addUser($id, $user);
            $this->redisHelper->setUpdateTs();
            Yii::$app->session['id'] = $id;
            Yii::$app->session['name'] = $data['name'];
            Yii::$app->session['user'] = $obj;
            return $this->redirect(['site/members']);
        }

        return $this->render('login', [
            'model' => $form,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        $this->user->deleteUserFromSet(Yii::$app->session['id'],  Yii::$app->session['user']);
        $this->redisHelper->setUpdateTs();
        Yii::$app->session->destroy();
        return $this->redirect(['site/login']);
    }

    public function actionMembers()
    {
        if (!Yii::$app->session['id']){
            return $this->redirect(['site/login']);
        }
        return $this->render('members', [
            'model' => $this->user,
        ]);
    }
}
