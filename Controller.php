<?php

namespace htethtetoo\phpmvc;

use htethtetoo\phpmvc\middlewares\BaseMiddleware;

class Controller
{
    public string $layout='main';

    /** @var \htethtetoo\phpmvc\middlewares\BaseMiddleware[] */
    protected array $middlewares=[];

    public string $action='';

    public function setLayout($layout){
        $this->layout=$layout;

    }
    public function render($view,$params=[]){
    return Application::$app->view->renderView($view,$params);
    }

    public function registerMiddleware(BaseMiddleware $middleware)
    {
        $this->middlewares[]=$middleware;

    }

    /**
     * @return array
     */
    public function getMiddleware(): array
    {
        return $this->middlewares;
    }
}