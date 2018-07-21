<?php

namespace app\controllers;

use app\models\PredisHelper;
use app\models\User;
use stdClass;
use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\models\LoginForm;
use app\models\RedisHelper;

class SiteController extends Controller
{

    private $redisHelper;
    private $user;
    private $predisHelper;

    public function __construct( $id, $module, RedisHelper $helper, User $user, PredisHelper $predisHelper, array $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->redisHelper = $helper;
        $this->user = $user;
        $this->predisHelper = $predisHelper;
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
            $obj = new stdClass;
            $obj->id = $id;
            $obj->name = $data['name'];
            $obj->handState = 0;

            if ($this->user->isUserExist($data['name'])){
                \Yii::$app->session->setFlash('danger', 'User with such name is already exists!');
                return $this->refresh();
            }

            $this->user->addUser($id, $obj);
            $this->user->addUserToSet($id, $obj);
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
        //$this->predisHelper->publish('__keyspace@1__:global:classroom:*', 'update');
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

    public function actionRaise()
    {
        $raise = \Yii::$app->request->bodyParams['data'];

        $sessid = Yii::$app->session['id'];
        if ($raise && $user = $this->user->getUserInSetById($sessid)){
            /*$user = unserialize($this->user->getUserById($sessid));

            if ($user->handState === 0){
                $user->handState = 1;
            }else{
                $user->handState = 0;
            }
            $this->user->updateUser($sessid, $user);*/

            //$this->user->deleteUserFromSet($sessid, $user);

            if ($user->handState === 0){
                $user->handState = 1;
            }else{
                $user->handState = 0;
            }

            //$this->user->addUserToSet($sessid, $user);
            $this->redisHelper->setUpdateTs();

            //$this->predisHelper->publish('classroom', "raise_up");
        }
    }

    public function actionPubsub()
    {
        $result = \Yii::$app->request->post();
        $this->predisHelper->publish($result['channel'], Yii::$app->session['id']);
    }
}
