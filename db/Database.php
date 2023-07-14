<?php

namespace app\core\db;

use app\core\Application;

class Database
{


    public \PDO $pdo;
    public function __construct(array $config)

    {
        $dsn=$config['dsn'] ?? '';
        $user=$config['user'] ?? '';
        $password=$config['password'] ?? '';
        $this->pdo=new \PDO($dsn,$user,$password);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION);
    }

    public function applyMigration()
    {
        $this->createMigratioinTable();
        $appliedMigrations=$this->getApplyMigrations();
        $newMigrations=[];
        $files=scandir(Application::$ROOT_DIR.'\migrations');
        $toApplyMigrations=array_diff($files, $this->getApplyMigrations());
        foreach($toApplyMigrations as $migration){
            if($migration === '.' || $migration === '..' ){
                continue;
            }
            require_once Application::$ROOT_DIR.'/migrations/'.$migration;
            $className=pathinfo($migration,PATHINFO_FILENAME);
            $instance=new $className();
            $this->log("Applying migration $migration".PHP_EOL);
            $instance->up();
            $this->log("Applied migration $migration".PHP_EOL);
            $newMigrations[]=$migration;

        }

        if(!empty($newMigrations)){
            $this->saveMigrations($newMigrations);
        }else{
            $this->log("All migrations are applied");
        }


    }

    public function createMigratioinTable()
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS  migrations(
        id INT AUTO_INCREMENT PRIMARY KEY,
        migration VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
        );");
    }
    public function getApplyMigrations()
    {
        $statment=$this->pdo->prepare("select migration from migrations");
        $statment->execute();
        return $statment->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function saveMigrations(array $migrations)
    {
        $str=implode(",",array_map(fn($m)=>"('$m')",$migrations));
        $statement=$this->pdo->prepare("Insert into migrations (migration) values $str");
        $statement->execute();
    }

    public function prepare($sql)
    {
        return $this->pdo->prepare($sql);
    }

    protected function log($message)
    {
        echo '['.date('Y-m-d H:i:s').']-'.$message.PHP_EOL;
    }
}