<?php

class base
{
    protected $app;

    public function __construct($app, $db_config)
	{
        $this->app = $app;

        # 是否开启调试
        $this->app->set('handle_errors', true);

        # 初始化数据库配置
        $app->db()->set_connection($db_config);

        # 初始化模板引擎配置
        $this->app->view()->tpl_dir = 'app/template/';

        # 模板引擎后缀
        $this->app->view()->tpl_ext = '.tpl.php';

        # 模板所在目录
        $this->app->view()->cache_dir = 'app/cache/template/';

        # 模板缓存时间
        $this->app->view()->cache_time = 0;

        # 赋值tpl变量
        $this->app->set('tpl', ($this->app->get('root') == '/' ? $this->app->get('root') : $this->app->get('root').'/') .$this->app->view()->tpl_dir);

        # 模板赋值app所在根目录
        $this->app->view()->assign('root', $this->app->get('root'));

        # 模板赋值app模板所在目录
        $this->app->view()->assign('tpl', $this->app->get('tpl'));
        
        # 模板赋值app模板css所在目录
        $this->app->view()->assign('css', $this->app->get('tpl').'assets/css/');

        # 模板赋值app模板img所在目录
        $this->app->view()->assign('img', $this->app->get('tpl').'assets/img/');

        # 模板赋值app模板js所在目录
        $this->app->view()->assign('js', $this->app->get('tpl').'assets/js/');
	}

	protected function setViewBase($title, $tpl)
	{
        $this->app->view()->assign('title', $title);

        $this->app->view()->display($tpl);
	}
}

?>