<?php

class home extends base
{
    public function index()
    {
        $this->app->view()->assign('value', 'Hello World');

        $this->setViewBase('首页', 'index');
    }
}

?>