<?php

namespace AppBundle\Repository;

interface PortfolioRepositoryInterface
{
    /**
     * Создает портфолио.
     *
     * @param $data
     * @return mixed
     */
    public function create($data);

    /**
     * Редактирует портфолио.
     *
     * @param $data
     * @param $id
     * @return mixed
     */
    public function update($data, $id);

    /**
     * Сохраняет портфолио.
     *
     * @param $object
     * @return mixed
     */
    public function save($object);

    /**
     * Удаляет портфолио.
     *
     * @param $object
     * @return mixed
     */
    public function delete($object);

    /**
     * Ищет портфолил.
     *
     * @param $id
     * @return mixed
     */
    public function find($id);

    /**
     * Отдает все портфолио.
     *
     * @return mixed
     */
    public function findAll();

    /**
     * Отдает процент заполненности портфеля.
     *
     * @param $object
     * @return float
     */
    public function getTotalProcents($object);
}
