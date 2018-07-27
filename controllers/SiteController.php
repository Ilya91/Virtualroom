<?php

namespace app\controllers;

use app\models\PredisHelper;
use app\models\User;
use stdClass;
use Yii;
use yii\helpers\Json;
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

            if ($this->user->isUserExist($data['name'])){
                \Yii::$app->session->setFlash('danger', 'User with such name is already exists!');
                return $this->refresh();
            }
            $id = rand();
            $this->user->addUser($id, Json::encode([
                'id' => $id,
                'name' => $data['name'],
                'handState' => 0,
            ]));

            $this->redisHelper->setUpdateTs();
            Yii::$app->session['id'] = $id;
            Yii::$app->session['name'] = $data['name'];
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

        $members = $this->user->getAllUsers();

        return $this->render('members', [
            'members' => $members,
        ]);
    }

    /**
     * Get ajax request, get user by sessid, change handState, save to Redis
     */
    public function actionRaise()
    {
        $result = \Yii::$app->request->post('raise');

        $sessid = Yii::$app->session['id'];

        if ($result && $user = $this->user->getUserById($sessid)){

            if ($user['handState'] === 0){
                $user['handState'] = 1;
            }else{
                $user['handState'] = 0;
            }

            $this->user->updateUser($sessid, $user);
        }
    }
}
