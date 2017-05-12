<?php

namespace AppBundle\Repository;

use Doctrine\ORM\PersistentCollection;

interface ShareRepositoryInterface
{
    /**
     * Отдает все акции с исключением.
     *
     * @param PersistentCollection $shares
     *
     * @return array
     */
    public function findAllWithExclude(PersistentCollection $shares) : array;
}