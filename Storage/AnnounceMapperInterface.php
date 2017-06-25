<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Announcement\Storage;

interface AnnounceMapperInterface
{
    /**
     * Deletes all announces associated with provided category id
     * 
     * @param string $categoryId
     * @return boolean
     */
    public function deleteAllByCategoryId($categoryId);

    /**
     * Fetches announce data by its associated id
     * 
     * @param string $id Announce ID
     * @param boolean $withTranslations Whether to fetch all translations or not
     * @return array
     */
    public function fetchById($id, $withTranslations);

    /**
     * Updates the sort order
     * 
     * @param string $id PK's value
     * @param string $order New sort order
     * @return boolean
     */
    public function updateOrderById($id, $order);

    /**
     * Updates announce's seo value
     * 
     * @param string $id Advice id
     * @param string $seo Either 0 or 1
     * @return boolean
     */
    public function updateSeoById($id, $seo);

    /**
     * Updates announce's published value
     * 
     * @param string $id Advice id
     * @param string $published Either 0 or 1
     * @return boolean
     */
    public function updatePublishedById($id, $published);

    /**
     * Fetches all announces filtered by pagination
     * 
     * @param integer $page Current page number
     * @param integer $itemsPerPage Per page count
     * @param boolean $published Whether to fetch only published announces
     * @param integer $categoryId Optional category ID filter
     * @return array
     */
    public function fetchAll($page, $itemsPerPage, $published, $categoryId);

    /**
     * Fetches all published announces
     * 
     * @param string $id Category id
     * @return array
     */
    public function fetchAllPublished($id);
}
