<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Announcement\Service;

use Cms\Service\AbstractManager;
use Announcement\Storage\AnnounceMapperInterface;
use Announcement\Storage\CategoryMapperInterface;
use Krystal\Stdlib\VirtualEntity;
use Krystal\Stdlib\ArrayUtils;

final class CategoryManager extends AbstractManager implements CategoryManagerInterface
{
    /**
     * Any-compliant announce mapper
     * 
     * @var \Announcement\Storage\AnnounceMapperInterface
     */
    private $announceMapper;

    /**
     * Any compliant category mapper
     * 
     * @var \Announcement\Storage\CategoryMapperInterface
     */
    private $categoryMapper;

    /**
     * State initialization
     * 
     * @param \Announcement\Storage\CategoryMapperInterface $categoryMapper
     * @param \Announcement\Storage\AnnounceMapperInterface $announceMapper
     * @return void
     */
    public function __construct(CategoryMapperInterface $categoryMapper, AnnounceMapperInterface $announceMapper)
    {
        $this->categoryMapper = $categoryMapper;
        $this->announceMapper = $announceMapper;
    }

    /**
     * {@inheritDoc}
     */
    protected function toEntity(array $category)
    {
        $entity = new VirtualEntity();
        $entity->setId($category['id'], VirtualEntity::FILTER_INT)
               ->setName($category['name'], VirtualEntity::FILTER_HTML)
               ->setClass($category['class'], VirtualEntity::FILTER_HTML);

        return $entity;
    }

    /**
     * Fetches as a list
     * 
     * @return array
     */
    public function fetchList()
    {
        return ArrayUtils::arrayList($this->categoryMapper->fetchList(), 'id', 'name');
    }

    /**
     * Fetches category data by its associated id
     * 
     * @param string $id Category id
     * @return boolean|\Krystal\Stdlib\VirtualEntity
     */
    public function fetchById($id)
    {
        return $this->prepareResult($this->categoryMapper->fetchById($id));
    }

    /**
     * Returns last category id
     * 
     * @return integer
     */
    public function getLastId()
    {
        return $this->categoryMapper->getLastId();
    }

    /**
     * Fetches all category bags
     * 
     * @return array
     */
    public function fetchAll()
    {
        return $this->prepareResults($this->categoryMapper->fetchAll());
    }

    /**
     * Deletes a category by its associated id
     * 
     * @param string $id Category id
     * @return boolean
     */
    public function deleteById($id)
    {
        return $this->categoryMapper->deleteById($id) && $this->announceMapper->deleteAllByCategoryId($id);
    }

    /**
     * Updates a category
     * 
     * @param array $input Raw form data
     * @return boolean Depending on success
     */
    public function update(array $input)
    {
        return $this->categoryMapper->update($input['id'], $input['name'], $input['class']);
    }

    /**
     * Adds a category
     * 
     * @param array $input Raw form data
     * @return boolean
     */
    public function add(array $input)
    {
        return $this->categoryMapper->insert($input['name'], $input['class']);
    }
}
