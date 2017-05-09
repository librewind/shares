<?php

namespace AppBundle\Repository;

interface ShareRepositoryInterface
{
    /**
     * Отдает все акции с исключением.
     *
     * @return mixed
     */
    public function findAllWithExclude($ids);
}