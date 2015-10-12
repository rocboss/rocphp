<?php

namespace frontend;

use \Roc;

class IndexController extends BaseController
{
    public static function index()
    {
        echo "Hello world <br>";

        echo 'Spend : '.Roc::getRunTime().'s';
    }
}

