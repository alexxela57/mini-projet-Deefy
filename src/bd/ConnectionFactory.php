<?php

namespace iutnc\deefy\bd;

use Exception;
use iutnc\deefy\exception\CompteException;

class ConnectionFactory{
    /**
     * @var array attribut recuperant les configurations du fichier de configuration ini
     */
    public static array $config;
    public static \PDO $connexion;

    /**
     * fonction qui permet d'appliquer la config initiale
     * @param $file
     * @return void
     */
    public static function setConfig($file): void
    {
        if(isset(self::$config)===false){
            self::$config=parse_ini_file($file);
        }
    }

    /**
     * fonction qui permet de se connecter a la base de donnees a partir
     * des configurations dans le fichier ini
     * @return \PDO
     * @throws CompteException
     */
    public static function makeConnection(): \PDO
    {
        if (!isset(self::$connexion)) {
            try {
                self::$connexion = new \PDO(
                    self::$config["driver"] . ":host=" . self::$config["hostname"] . ";dbname=" . self::$config["dbname"],
                    self::$config['username'],
                    self::$config['password']
                );
            } catch (Exception $e) {
                throw new CompteException('Erreur de connexion : ' . $e->getMessage());
            }
        }

        return self::$connexion;
    }

}
