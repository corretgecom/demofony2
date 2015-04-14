<?php
namespace Demofony2\AppBundle\Repository;
use Doctrine\ORM\EntityRepository;

class ParticipationStatisticsRepository extends EntityRepository
{
    public function getBetweenDate($startAt, $endAt, $groupBy)
    {
        $qb = $this->createQueryBuilder('s');

        if ('month' === $groupBy) {
            $qb->select('SUM(s.comments) AS comments, SUM(s.votes) as votes, SUM(s.proposals) as proposals, (SUM(s.comments) + SUM(s.votes) + SUM(s.proposals)) as total , MONTH(s.day) as HIDDEN month, YEAR(s.day) as HIDDEN year, s.day as date ')
                ->groupBy('month')
                ->addGroupBy('year');
        }

        $qb->where('s.day >= :startAt')
            ->andWhere('s.day < :endAt')
            ->setParameter('startAt', $startAt)
            ->setParameter('endAt', $endAt);

        return $qb->getQuery()->getArrayResult();
    }

}