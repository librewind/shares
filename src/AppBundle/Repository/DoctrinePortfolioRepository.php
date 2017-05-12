<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Portfolio;

class DoctrinePortfolioRepository extends EntityRepository implements PortfolioRepositoryInterface
{
    /**
     * Создает портфолио.
     *
     * @param array $data
     *
     * @return Portfolio
     */
    public function create(array $data) : Portfolio
    {
        $entity = new $this->_entityName();

        return $this->prepare($entity, $data);
    }

    /**
     * Редактирует портфолио.
     *
     * @param array $data
     * @param int   $id
     *
     * @return Portfolio
     */
    public function update(array $data, int $id) : Portfolio
    {
        $entity = $this->find($id);

        return $this->prepare($entity, $data);
    }

    /**
     * Заполняет поля модели.
     *
     * @param Portfolio $entity
     * @param array     $data
     *
     * @return Portfolio
     */
    protected function prepare(Portfolio $entity, array $data) : Portfolio
    {
        $entity->setName($data['name']);

        $entity->setUser($data['user']);

        return $entity;
    }

    /**
     * Сохраняет портфолио.
     *
     * @param Portfolio $object
     *
     * @return Portfolio
     */
    public function save(Portfolio $object) : Portfolio
    {
        $this->_em->persist($object);

        $this->_em->flush($object);

        return $object;
    }

    /**
     * Удаляет портфолио.
     *
     * @param Portfolio $object
     *
     * @return bool
     */
    public function delete(Portfolio $object) : bool
    {
        $this->_em->remove($object);

        $this->_em->flush($object);

        return true;
    }

    /**
     * Отдает процент заполненности портфеля.
     *
     * @param Portfolio $object
     *
     * @return float
     */
    public function getTotalProcents(Portfolio $object) : float
    {
        $query = $this->_em->createQuery(
            'SELECT sum(ps.proportion) as totalProcents 
            FROM AppBundle\Entity\PortfolioShare ps 
            WHERE ps.portfolio = :portfolio'
        )->setParameter('portfolio', $object->getId());

        $result = $query->getResult();

        if (is_array($result) && isset($result[0]) && isset($result[0]['totalProcents']) && $result[0]['totalProcents'] > 0) {
            return floatval($result[0]['totalProcents']);
        }

        return 0;
    }
}