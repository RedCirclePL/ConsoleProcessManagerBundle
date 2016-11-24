<?php

namespace RedCircle\ConsoleProcessManagerBundle\Repository;

use Doctrine\ORM\EntityRepository;
use RedCircle\ConsoleProcessManagerBundle\Entity\Call;
use RedCircle\ConsoleProcessManagerBundle\Entity\Process;

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
     * @param Process $process
     * @param null $status
     * @param null $inLastHours
     * @param bool $autoUpdateProcess
     * @return mixed
     * @internal param $processId
     */
    public function countByProcessIdAndStatus(Process $process, $status = null, $inLastHours = null, $autoUpdateProcess = false)
    {
        $qb = $this->getEntityManager()
            ->createQueryBuilder('pmc')
            ->from('ConsoleProcessManagerBundle:Call', 'pmc')
            ->select('count(pmc)')
            ->where('pmc.process = :id')
            ->setParameter('id', $process->getId());

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
        $forReturn = $result && is_array($result) ? (int)$result[1] : 0;

        if($autoUpdateProcess) {
            $process->setCallErrorCount($forReturn);
            $em = $this->getEntityManager();
            $em->persist($process);
            $em->flush($process);
        }

        return $forReturn;
    }
}
