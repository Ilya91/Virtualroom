<?php

namespace app\controllers;

use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\RedisHelper;

class SiteController extends Controller
{

    private $redisHelper;

    public function __construct( $id, $module, RedisHelper $helper, array $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->redisHelper = $helper;
    }


    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
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
        session_unset();
        session_destroy();
        $form = new LoginForm();
        $user = new User();
        $user->deleteUser(Yii::$app->session['id']);
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $data = Yii::$app->request->post('LoginForm');
        if ($data){
            $id = rand();
            $key = 'global:classroom:users:' . $id;
            $obj = (object)['name' => $data['name'], 'handState' => 0];
            $user->setUser($key, serialize($obj));
            $this->redisHelper->setUpdateTs();
            Yii::$app->session['id'] = $id;
            return $this->redirect(['site/members']);
        }
        /*if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }*/

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
        /*session_unset();
        session_destroy();*/
    }

    public function actionMembers()
    {
        return $this->render('members');
    }
}
