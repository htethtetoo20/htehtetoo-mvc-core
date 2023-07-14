<?php
namespace app\core;
use app\core\db\Database;
use app\core\db\DbModel;
use app\models\User;

class Application
{

    public string $layout='main';
    public static string $ROOT_DIR;
    public ?DbModel $userClass;
    public Router $router;
    public Request $request;
    public Response $response;
    public Session $session;
    public Database $db;
    public static Application $app;
    public ?Controller $controller= null;
    public ?DbModel $user=null;
    public string $primaryValue;
    public View $view;


    public function __construct($rootPath,array $config)
    {
        self::$ROOT_DIR=$rootPath;
        self::$app=$this;
        $this->request=new Request();
        $this->response=new Response();
        $this->session=new Session();
        $this->router=new Router($this->request,$this->response);
        $this->db=new Database($config['db']);
        $this->view=new View();
        $this->userClass=new $config['userClass'];
        $this->primaryValue=$this->session->get('user');

        if($this->primaryValue) {

            $primaryKey = $this->userClass->primaryKey();
            $this->user=$this->userClass->findOne([$primaryKey =>$this->primaryValue]);
        }
//        else{
//            $primaryKey = $this->userClass->primaryKey();
//            $this->user=$this->userClass->findOne([$primaryKey =>'3']);
//        }
    }
    public function run(){

        try {
            $this->router->resolve();
        }catch (\Exception $e){
            $this->response->setStatusCode($e->getCode());
            echo $this->view->renderView('_error',['exception'=>$e]);
        }

    }

    public function login(DbModel  $user){

        $this->user=$user;
        $primaryKey=$user->primaryKey();

        $primaryValue=$user->{$primaryKey};

        $this->session->set('user',$primaryValue);
        return true;
    }

    public function logout(){
        $this->user=null;
        $this->session->remove('user');
    }

    public static function isGuest()
    {
        return !self::$app->user;
    }
}