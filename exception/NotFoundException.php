<?php

namespace htethtetoo\phpmvc\exception;

use Throwable;

class NotFoundException extends \Exception
{
    protected $code='404';
    protected $message='Not found';

}