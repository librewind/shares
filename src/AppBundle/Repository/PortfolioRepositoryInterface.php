<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Portfolio;

interface PortfolioRepositoryInterface
{
    /**
     * Создает портфолио.
     *
     * @param array $data
     *
     * @return Portfolio
     */
    public function create(array $data) : Portfolio;

    /**
     * Редактирует портфолио.
     *
     * @param array $data
     * @param int   $id
     *
     * @return Portfolio
     */
    public function update(array $data, int $id) : Portfolio;

    /**
     * Сохраняет портфолио.
     *
     * @param Portfolio $object
     *
     * @return Portfolio
     */
    public function save(Portfolio $object) : Portfolio;

    /**
     * Удаляет портфолио.
     *
     * @param Portfolio $object
     *
     * @return bool
     */
    public function delete(Portfolio $object) : bool;

    /**
     * Ищет портфолил.
     *
     * @param $id
     *
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
     * @param Portfolio $object
     *
     * @return float
     */
    public function getTotalProcents(Portfolio $object) : float;
}
