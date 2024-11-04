<?php

namespace iutnc\deefy\bd;
use Exception;
use iutnc\deefy\exception\CompteException;

class ConnectionFactory{
    public static array $config;
    public static \PDO $connexion;

    public static function setConfig($file): void
    {
        if(isset(self::$config)===false){
            self::$config=parse_ini_file($file);
        }
    }

    public static function makeConnection(): \PDO
    {
        if(isset(self::$connexion)===false){
            try {
                self::$connexion = new \PDO(self::$config["driver"] . ":host=" . self::$config["hostname"] . ";dbname=" . self::$config["dbname"], self::$config['username'], self::$config['password']);
            } catch (Exception $e){
                throw new CompteException('Utilisateur ou mot de passe incorrect');
            }
        }

        return self::$connexion;
    }
}
