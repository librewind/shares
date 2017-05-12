<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\PersistentCollection;

class DoctrineShareRepository extends EntityRepository implements ShareRepositoryInterface
{
    /**
     * Отдает все акции с исключением.
     *
     * @param PersistentCollection $shares
     *
     * @return array
     */
    public function findAllWithExclude(PersistentCollection $shares) : array
    {
        if (count($shares) == 0) {
            return $this->findAll();
        }

        $ids = [];

        foreach ($shares as $share) {
            $ids[] = $share->getShare()->getId();
        }

        $idsString = implode(',', $ids);

        return $this->_em->createQuery(
            "SELECT s 
            FROM AppBundle\Entity\Share s 
            WHERE s.id NOT IN (" . $idsString . ")"
        )->getResult();
    }
}