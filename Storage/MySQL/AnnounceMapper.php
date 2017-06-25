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
     * {@inheritDoc}
     */
    public static function getTranslationTable()
    {
        return self::getWithPrefix('bono_module_announcement_announces_translations');
    }

    /**
     * Returns shared columns to be selected
     * 
     * @param boolean $all Whether to select all columns
     * @return array
     */
    private function getSharedColumns($all)
    {
        $columns = array(
            self::getFullColumnName('id'),
            self::getFullColumnName('lang_id', self::getTranslationTable()),
            self::getFullColumnName('web_page_id', self::getTranslationTable()),
            self::getFullColumnName('category_id'),
            self::getFullColumnName('order'),
            self::getFullColumnName('published'),
            self::getFullColumnName('seo'),
            WebPageMapper::getFullColumnName('slug'),
            self::getFullColumnName('name', self::getTranslationTable()),
        );

        if ($all === true) {
            $columns = array_merge($columns, array(
                self::getFullColumnName('icon'),
                self::getFullColumnName('title', self::getTranslationTable()),
                self::getFullColumnName('intro', self::getTranslationTable()),
                self::getFullColumnName('full', self::getTranslationTable()),
                self::getFullColumnName('keywords', self::getTranslationTable()),
                self::getFullColumnName('meta_description', self::getTranslationTable()),
            ));
        }

        return $columns;
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
     * Update settings
     * 
     * @param array $settings
     * @return boolean
     */
    public function updateSettings(array $settings)
    {
        return $this->updateColumns($settings, array('order', 'seo', 'published'));
    }

    /**
     * Fetches announce data by its associated id
     * 
     * @param string $id Announce ID
     * @param boolean $withTranslations Whether to fetch all translations or not
     * @return array
     */
    public function fetchById($id, $withTranslations)
    {
        $db = $this->db->select($this->getSharedColumns(true))
                       ->from(self::getTableName())
                       ->innerJoin(self::getTranslationTable())
                       ->on()
                       ->equals(
                            self::getFullColumnName('id'), 
                            new RawSqlFragment(self::getFullColumnName('id', self::getTranslationTable()))
                        )
                        ->innerJoin(WebPageMapper::getTableName())
                        ->on()
                        ->equals(
                            WebPageMapper::getFullColumnName('id'),
                            new RawSqlFragment(self::getFullColumnName('web_page_id', self::getTranslationTable()))
                        );

        $db->whereEquals(self::getFullColumnName('id'), $id);

        if ($withTranslations === true) {
            return $db->queryAll();
        } else {
            return $db->andWhereEquals(self::getFullColumnName('lang_id', self::getTranslationTable()), $this->getLangId())
                      ->query();
        }
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
        $columns = array_merge(
            $this->getSharedColumns(false), 
            array(CategoryMapper::getFullColumnName('name') => 'category')
        );

        $db = $this->db->select($columns)
                       ->from(self::getTableName())
                       // Translation relation
                       ->innerJoin(self::getTranslationTable())
                       ->on()
                       ->equals(
                            self::getFullColumnName('id'), 
                            new RawSqlFragment(self::getFullColumnName('id', self::getTranslationTable()))
                        )
                        // Web page relation
                        ->innerJoin(WebPageMapper::getTableName())
                        ->on()
                        ->equals(
                            WebPageMapper::getFullColumnName('id'),
                            new RawSqlFragment(self::getFullColumnName('web_page_id', self::getTranslationTable()))
                        )
                        ->rawAnd()
                        ->equals(
                            WebPageMapper::getFullColumnName('lang_id'),
                            new RawSqlFragment(self::getFullColumnName('lang_id', self::getTranslationTable()))
                        )
                        // Category relation
                        ->innerJoin(CategoryMapper::getTableName())
                        ->on()
                        ->equals(
                            CategoryMapper::getFullColumnName('id'),
                            new RawSqlFragment(self::getFullColumnName('category_id'))
                        );

        $db->whereEquals(self::getFullColumnName('lang_id', self::getTranslationTable()), $this->getLangId());

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
}
