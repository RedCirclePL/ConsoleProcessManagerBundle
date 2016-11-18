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

    public function update(Call $call) {

        $em = $this->getEntityManager();
        $em->persist($call);
        $em->flush($call);

        return $call;
    }

}
