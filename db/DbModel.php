<?php

namespace app\core\db;

use app\core\Application;
use app\core\Model;

abstract class DbModel extends Model
{
    abstract public function tableName():string;

    abstract public function attributes():array;

    abstract public function primaryKey():string;

    public static function prepare($sql)
    {
       return Application::$app->db->pdo->prepare($sql);
    }

    public function save()
    {
        $tableName=$this->tableName();
        $attributes=$this->attributes();
        $params=array_map(fn($attr)=>":$attr",$attributes);
        $statement=self::prepare("Insert into $tableName (".implode(',',$attributes).") values(".implode(',',$params).")");
        foreach ($attributes as $attribute){
            $statement->bindValue(":$attribute",$this->{$attribute});
        }
        $statement->execute();
        return true;

    }

    public static function findOne($where)
    {
        $className=static::class;
        $class=new $className();
        $tableName= $class->tableName();
        $attributes=array_keys($where);
        $sql=implode("and",array_map(fn($attr)=>"$attr= :$attr",$attributes));
        $statement=self::prepare("select * from $tableName where $sql");
        foreach ($where as $key=>$item){
            $statement->bindValue(":$key",$item);
        }
        $statement->execute();
        return $statement->fetchObject($className);
        // select * from $tableName where email= :email and firstname= :firstname

    }


}