<?php

namespace htethtetoo\phpmvc;

use htethtetoo\phpmvc\db\DbModel;

abstract class UserModel extends DbModel
{
abstract public function getDisplayName():string;
}