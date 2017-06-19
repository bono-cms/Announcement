<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Announcement\Storage\MySQL;

use Cms\Storage\MySQL\AbstractMapper;
use Cms\Storage\MySQL\WebPageMapper;
use Announcement\Storage\AnnounceMapperInterface;
use Krystal\Db\Sql\RawSqlFragment;

final class AnnounceMapper extends AbstractMapper implements AnnounceMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getTableName()
    {
        return self::getWithPrefix('bono_module_announcement_announces');
    }

    /**
     * Append web page relation by linked IDs
     * 
     * @return void
     */
    private function appendWebPageRelation()
    {
        $this->db->leftJoin(WebPageMapper::getTableName())
                 ->on()
                 ->equals(self::getFullColumnName('web_page_id'), new RawSqlFragment(WebPageMapper::getFullColumnName('id')));
    }

    /**
     * Returns shared columns to be selected
     * 
     * @return array
     */
    private function getSharedColumns()
    {
        return array(
            self::getFullColumnName('id'),
            self::getFullColumnName('lang_id'),
            self::getFullColumnName('web_page_id'),
            self::getFullColumnName('category_id'),
            self::getFullColumnName('title'),
            self::getFullColumnName('name'),
            self::getFullColumnName('intro'),
            self::getFullColumnName('full'),
            self::getFullColumnName('icon'),
            self::getFullColumnName('order'),
            self::getFullColumnName('published'),
            self::getFullColumnName('seo'),
            self::getFullColumnName('keywords'),
            self::getFullColumnName('meta_description'),
            WebPageMapper::getFullColumnName('slug'),
        );
    }

    /**
     * Deletes all announces associated with provided category id
     * 
     * @param string $categoryId
     * @return boolean
     */
    public function deleteAllByCategoryId($categoryId)
    {
        return $this->deleteByColumn('category_id', $categoryId);
    }

    /**
     * Deletes an announce by its associated id
     * 
     * @param string $id Announce id
     * @return boolean
     */
    public function deleteById($id)
    {
        return $this->deleteByPk($id);
    }

    /**
     * Adds an announce
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function insert(array $input)
    {
        return $this->persist($this->getWithLang($input));
    }

    /**
     * Updates an announce
     * 
     * @param array $input Raw input data
     * @return boolean Depending on success
     */
    public function update(array $input)
    {
        return $this->persist($input);
    }

    /**
     * Updates the sort order
     * 
     * @param string $id PK's value
     * @param string $order New sort order
     * @return boolean
     */
    public function updateOrderById($id, $order)
    {
        return $this->updateColumnByPk($id, 'order', $order);
    }

    /**
     * Updates SEO value
     * 
     * @param string $id
     * @param string $seo Either 0 or 1
     * @return boolean
     */
    public function updateSeoById($id, $seo)
    {
        return $this->updateColumnByPk($id, 'seo', $seo);
    }

    /**
     * Updates published value
     * 
     * @param string $id
     * @param string $published Either 0 or 1
     * @return boolean
     */
    public function updatePublishedById($id, $published)
    {
        return $this->updateColumnByPk($id, 'published', $published);
    }

    /**
     * Fetches announce title by its associated id
     * 
     * @param string $id Announce id
     * @return string
     */
    public function fetchTitleById($id)
    {
        return $this->findColumnByPk($id, 'title');
    }

    /**
     * Fetches announce data by its associated id
     * 
     * @param string $id
     * @return array
     */
    public function fetchById($id)
    {
        $db = $this->db->select($this->getSharedColumns())
                       ->from(self::getTableName());

        $this->appendWebPageRelation();

        return $db->whereEquals(self::getFullColumnName('lang_id'), $this->getLangId())
                  ->andWhereEquals(self::getFullColumnName('id'), $id)
                  ->query();
    }

    /**
     * Fetches all announces filtered by pagination
     * 
     * @param integer $page Current page number
     * @param integer $itemsPerPage Per page count
     * @param boolean $published Whether to fetch only published announces
     * @param integer $categoryId Optional category ID filter
     * @return array
     */
    public function fetchAll($page, $itemsPerPage, $published, $categoryId)
    {
        $db = $this->db->select($this->getSharedColumns())
                       ->from(self::getTableName());

        $this->appendWebPageRelation();
        $db->whereEquals(self::getFullColumnName('lang_id'), $this->getLangId());

        if ($categoryId !== null) {
            $db->andWhereEquals(self::getFullColumnName('category_id'), $categoryId);
        }

        if ($published === true) {
            $db->andWhereEquals(self::getFullColumnName('published'), '1')
               ->orderBy(new RawSqlFragment('`order`, CASE WHEN `order` = 0 THEN `id` END DESC'));
        } else {
            $db->orderBy(self::getFullColumnName('id'))
               ->desc();
        }

        // Paginate if required
        if ($page !== null && $itemsPerPage !== null) {
            $db->paginate($page, $itemsPerPage);
        }

        return $db->queryAll();
    }

    /**
     * Fetches all published announces
     * 
     * @param string $id Category id
     * @return array
     */
    public function fetchAllPublished($id)
    {
        return $this->fetchAll(null, null, true, $id);
    }
}
