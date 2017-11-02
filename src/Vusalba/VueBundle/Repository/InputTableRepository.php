<?php
/**
 * Created by PhpStorm.
 * User: Ibrahima
 * Date: 13/10/2017
 * Time: 15:48
 */

namespace Vusalba\VueBundle\Repository;


class InputTableRepository extends \Doctrine\ORM\EntityRepository
{
        public  function  getResultByStartAndEndDate($composant, $startDate, $enddate) {

                $query = $this->createQueryBuilder('input_table')
                                ->where('input_table.composant =:comp')
                                ->andWhere('input_table.dateremiseImage >= :startDate ')
                                ->andWhere('input_table.dateremiseImage <= :enddate')
                                ->setParameter('comp',$composant)
                                ->setParameter('startDate',$startDate)
                                ->setParameter('enddate',$enddate)
                                ->getQuery();

                return $query->getResult();
        }
}