<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class DoctrinePortfolioRepository extends EntityRepository implements PortfolioRepositoryInterface
{
    /**
     * Создает портфолио.
     *
     * @param $data
     * @return mixed
     */
    public function create($data)
    {
        $entity = new $this->_entityName();

        return $this->prepare($entity, $data);
    }

    /**
     * Редактирует портфолио.
     *
     * @param $data
     * @param $id
     * @return mixed
     */
    public function update($data, $id)
    {
        $entity = $this->find($id);

        return $this->prepare($entity, $data);
    }

    /**
     * Заполняет поля модели.
     *
     * @param $entity
     * @param $data
     * @return mixed
     */
    protected function prepare($entity, $data)
    {
        $entity->setName($data['name']);

        $entity->setUser($data['user']);

        return $entity;
    }

    /**
     * Сохраняет портфолио.
     *
     * @param $object
     * @return mixed
     */
    public function save($object)
    {
        $this->_em->persist($object);

        $this->_em->flush($object);

        return $object;
    }

    /**
     * Удаляет портфолио.
     *
     * @param $object
     * @return bool
     */
    public function delete($object)
    {
        $this->_em->remove($object);

        $this->_em->flush($object);

        return true;
    }

    /**
     * Отдает процент заполненности портфеля.
     *
     * @param $object
     * @return float
     */
    public function getTotalProcents($object)
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