<?php
/**
 * Created by PhpStorm.
 * User: Aly Seck
 * Date: 02/08/2017
 * Time: 16:38
 */

namespace Vusalba\VueBundle\Constante;


class Database
{

    public static function getDatabase($dbname, $user, $password, $host) {
        $params = array(
            'dbname' => $dbname,
            'user' => $user,
            'password' => $password,
            'host' => $host,
            'driver' => 'pdo_mysql',
        );
        return $params;
    }

}