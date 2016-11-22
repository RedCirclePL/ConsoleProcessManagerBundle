<?php

namespace RedCircle\ConsoleProcessManagerBundle\Repository;

use Doctrine\ORM\EntityRepository;
use RedCircle\ConsoleProcessManagerBundle\Entity\Call;

/**
 * CallRepository
 *
 * @author Mateusz Krysztofiak <mateusz@krysztofiak.net>
 */
class CallRepository extends EntityRepository
{

    /**
     * @param Call $call
     * @return Call
     */
    public function update(Call $call) {

        $em = $this->getEntityManager();
        $em->persist($call);
        $em->flush($call);

        return $call;
    }

    /**
     * @param $processId
     * @param null $inLastHours
     * @return mixed
     */
    public function countByProcessIdAndStatus($processId, $status = null, $inLastHours = null)
    {
        $qb = $this->getEntityManager()
            ->createQueryBuilder('pmc')
            ->from('ConsoleProcessManagerBundle:Call', 'pmc')
            ->select('count(pmc)')
            ->where('pmc.process = :id')
            ->setParameter('id', $processId);

        if($status) {
            if(is_array($status)) {
                $qb->andWhere('pmc.status IN(:status)');
            } else {
                $qb->andWhere('pmc.status = :status');
            }
            $qb->setParameter('status', $status);
        }

        if($inLastHours) {
            $now = new \DateTime();
            $qb->andWhere('pmc.finishedAt > :inLastHours')
                ->setParameter('inLastHours', $now->sub(new \DateInterval('PT' . $inLastHours . 'H')));
        }

        $result = $qb->getQuery()->getOneOrNullResult();
        return $result && is_array($result) ? (int)$result[1] : 0;
    }

}
