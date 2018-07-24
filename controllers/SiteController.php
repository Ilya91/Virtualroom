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

/**
 * Class SiteController - main controller
 * @package app\controllers
 */
class SiteController extends Controller
{

    private $redisHelper;
    private $user;
    private $predisHelper;

    /**
     * SiteController constructor.
     * @param $id
     * @param $module
     * @param RedisHelper $helper
     * @param User $user
     * @param PredisHelper $predisHelper
     * @param array $config
     */
    public function __construct($id, $module, RedisHelper $helper, User $user, PredisHelper $predisHelper, array $config = [])
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
            $this->redisHelper->setUpdateTs();
            $this->predisHelper->publish('global:classroom:*', 'class_config_changed');
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
        $this->user->deleteUser(Yii::$app->session['id']);
        $this->redisHelper->setUpdateTs();
        $this->predisHelper->publish('global:classroom:*', 'class_config_changed');
        Yii::$app->session->destroy();
        return $this->redirect(['site/login']);
    }

    /**
     * @return string|Response
     */
    public function actionMembers()
    {
        if (!Yii::$app->session['id']){
            return $this->redirect(['site/login']);
        }
        $this->predisHelper->publish('global:classroom:*', 'class_config_changed');
        return $this->render('members', [
            'model' => $this->user,
        ]);
    }

    /**
     * Get ajax request, get user by sessid, change handState, save to Redis, publish student_state_changed
     */
    public function actionRaise()
    {
        $result = \Yii::$app->request->post('raise');

        $sessid = Yii::$app->session['id'];

        if ($result && $user = $this->user->getUserById($sessid)){

            if ($user->handState === 0){
                $user->handState = 1;
            }else{
                $user->handState = 0;
            }

            $this->user->updateUser($sessid, $user);
            $this->redisHelper->setUpdateTs();

            $this->predisHelper->publish('global:classroom:users:*' , 'student_state_changed-' . $sessid);
        }
    }
}
