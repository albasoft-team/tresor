<?php

namespace Vusalba\VueBundle\Repository;
use Doctrine\ORM\EntityManager;
use Vusalba\VueBundle\Entity\Level;

/**
 * LevelRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class LevelRepository extends \Doctrine\ORM\EntityRepository
{
    public function createLevel($name, $scope,$niveau) {
        $level = new Level();
        $level->setName($name);
        $level->setScopeAnalysis($scope);
        $level->setNiveau($niveau);
        $em = $this->getEntityManager();
        $this->persitLevel($em, $level);
    }
    public function getLevel($niveau_parent) {
        $niveau = $niveau_parent + 1 ;
        $query = $this->createQueryBuilder('level')
            ->where('level.niveau='.$niveau)
            ->getQuery();
        return $query->getResult();
    }
    private function persitLevel(EntityManager $em, $level) {
        $em->persist($level);
        $em->flush();
    }
}