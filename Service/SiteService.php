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

use Announcement\Storage\CategoryMapperInterface;

final class SiteService
{
    /**
     * Announce service
     * 
     * @var \Announcement\Service\AnnounceManager
     */
    private $announceManager;

    /**
     * Any compliant category mapper
     * 
     * @var \Announcement\Storage\CategoryMapperInterface
     */
    private $categoryMapper;

    /**
     * State initialization
     * 
     * @param \Announcement\Service\AnnounceManager $announceManager
     * @param \Announcement\Storage\CategoryMapperInterface $categoryMapper
     * @return void
     */
    public function __construct(AnnounceManager $announceManager, CategoryMapperInterface $categoryMapper)
    {
        $this->announceManager = $announceManager;
        $this->categoryMapper = $categoryMapper;
    }

    /**
     * Gets all announce entities associated with provided category class
     * 
     * @param string $class Category class
     * @return array
     */
    public function getAllByClass($class)
    {
        $id = $this->categoryMapper->fetchIdByClass($class);

        // Do the following query in case right id supplied
        if ($id) {
            return $this->announceManager->fetchAll(null, null, true, $id);
        } else {
            return array();
        }
    }
}
