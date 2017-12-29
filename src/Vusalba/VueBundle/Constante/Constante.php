<?php

namespace Vusalba\VueBundle\Constante;


class Constante
{
    public static $table_created = 0 ;
    const ORM_PATH = 'src\Vusalba\VueBundle\Resources\config\doctrine';
    const PATH = __DIR__.'/../Entity';
    const MAPPING_IMPORT = array(
        'command' => 'doctrine:mapping:import',
        'bundle' => 'VueBundle',
        'mapping-type' => 'annotation',
        '--filter' => 'InputTable',
        '--force' => true,
        '--no-interaction'
    );
    const CACHE_ENV_PROD = array(
        'command' => 'cache:clear',
        '--no-warmup',
        '--env' => 'prod'
    );
    const MAPPING_CONVERT = array(
        'command' => 'doctrine:mapping:convert',
        'to-type' => 'annotation',
        'dest-path' => 'C:\wamp64\www\vusalba\src\Vusalba\VueBundle\Entity',
//        'dest-path' => Constante::PATH,
//        'dest-path' => '/var/www/vusalba/src/Vusalba/VueBundle/Entity',
//        '--filter' => 'InputTable',
        '--no-interaction'
    );
    const GENERATE_ENTITIES = array(
        'command' => 'doctrine:generate:entities',
        'name' => 'VueBundle:InputTable',
        '--no-interaction'
    );
    const GENERATE_CRUD = array(
        'command' => 'doctrine:generate:crud',
        '--entity' => 'VueBundle:InputTable',
        '--format' => 'annotation',
        '--no-interaction',
        '--overwrite',
        '--no-debug',
        '--with-write'
    );
    /**
     * @param $fields
     * @return string
     */
   public static function getCreateQuery($fields) {
       $query = "CREATE TABLE IF NOT EXISTS input_table( `id` INTEGER NOT NULL AUTO_INCREMENT,
                                  `composant_id` INTEGER NULL ,
                                  `tags` VARCHAR(1000)  NULL ,
                                  `node_id` INTEGER  NULL ,
                                   $fields,
                                   PRIMARY KEY (`id`),
                                   CONSTRAINT `comp_inputtable_id` FOREIGN KEY (`composant_id`) REFERENCES `composant` (`id`) ON DELETE CASCADE,
                                   CONSTRAINT `node_inputtable_id` FOREIGN KEY (`node_id`) REFERENCES `node` (`id`) ON DELETE CASCADE
                                    )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
       return $query;
   }
   public static function getDistinctInputs(\Doctrine\DBAL\Connection $conn) {
       $query = "SELECT   composant_id FROM input_table GROUP BY composant_id" ;
       $stmt = $conn->prepare($query);
       $stmt->execute();
       return $stmt->fetchAll();
   }
    public static function getDistinctNode(\Doctrine\DBAL\Connection $conn) {
       $query = "SELECT  DISTINCT node_id, tags  FROM input_table" ;
       $stmt = $conn->prepare($query);
       $stmt->execute();
       return $stmt->fetchAll();
   }
//   public static function queryTest2(\Doctrine\DBAL\Connection $conn) {
//      $query = "select  *  from node WHERE node.parent_id= '01063'" ;
//      $stmt = $conn->prepare($query);
//      $stmt->execute();
//      return $stmt->fetchAll();
//   }
   public static function queryTest(\Doctrine\DBAL\Connection $conn) {
      $query = "select  *  from input_table WHERE node_id LIKE 'SN%01063'" ;
      $stmt = $conn->prepare($query);
      $stmt->execute();
      return $stmt->fetchAll();
   }

   public static function getLastInput(\Doctrine\DBAL\Connection $conn) {
       $query = "SELECT id, composant_id, tags FROM input_table ORDER BY id DESC LIMIT 1";
       $stmt = $conn->prepare($query);
       $stmt->execute();
       return $stmt->fetchAll();
   }
   public function getLastNodeInserted() {
   }
//   public static function insert($champs, $comp) {
//       $query = "INSERT INTO `inputtable`".$champs . " VALUES(".$comp.")";
//   }
}