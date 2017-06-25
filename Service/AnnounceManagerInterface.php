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

use Krystal\Stdlib\VirtualEntity;

interface AnnounceManagerInterface
{
    /**
     * Update settings
     * 
     * @param array $settings
     * @return boolean
     */
    public function updateSettings(array $settings);

    /**
     * Returns last announce id
     * 
     * @return integer
     */
    public function getLastId();

    /**
     * Returns prepared paginator's instance
     * 
     * @return \Krystal\Paginate\Paginator
     */
    public function getPaginator();

    /**
     * Fetches announce's entity by its associated id
     * 
     * @param string $id
     * @param boolean $withTranslations Whether to fetch all translations or not
     * @return \Krystal\Stdlib\VirtualEntity|boolean
     */
    public function fetchById($id, $withTranslations);

    /**
     * Fetches all announce entities filtered by pagination
     * 
     * @param integer $page Current page number
     * @param integer $itemsPerPage Per page count
     * @param boolean $published Whether to fetch only published announces
     * @param integer $categoryId Optional category ID filter
     * @return array
     */
    public function fetchAllByPage($page, $itemsPerPage, $published, $categoryId = null);

    /**
     * Fetches all published announce entities associated with provided category id
     * 
     * @param string $categoryId
     * @return array
     */
    public function fetchAllPublished($categoryId);

    /**
     * Deletes an announce by its associated id
     * 
     * @param string $id Announce id
     * @return boolean
     */
    public function deleteById($id);

    /**
     * Delete announces by their associated ids
     * 
     * @param array $ids Array of announce ids
     * @return boolean
     */
    public function deleteByIds(array $ids);
}
