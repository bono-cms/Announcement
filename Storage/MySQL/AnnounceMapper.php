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
        return AnnounceTranslationMapper::getTableName();
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
            self::column('id'),
            AnnounceTranslationMapper::column('lang_id'),
            AnnounceTranslationMapper::column('web_page_id'),
            self::column('category_id'),
            self::column('order'),
            self::column('published'),
            self::column('seo'),
            self::column('icon'),
            AnnounceTranslationMapper::column('intro'),
            AnnounceTranslationMapper::column('name'),
            WebPageMapper::column('slug')
        );

        if ($all === true) {
            $columns = array_merge($columns, array(
                AnnounceTranslationMapper::column('title'),
                AnnounceTranslationMapper::column('full'),
                AnnounceTranslationMapper::column('keywords'),
                AnnounceTranslationMapper::column('meta_description')
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
        return $this->findWebPage($this->getSharedColumns(true), $id, $withTranslations);
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
            array(CategoryMapper::column('name') => 'category')
        );

        $db = $this->createWebPageSelect($columns)
                    // Category relation
                    ->innerJoin(CategoryMapper::getTableName(), array(
                        CategoryMapper::column('id') => self::getRawColumn('category_id')
                    ))
                    ->whereEquals(AnnounceTranslationMapper::column('lang_id'), $this->getLangId());

        if ($categoryId !== null) {
            $db->andWhereEquals(self::column('category_id'), $categoryId);
        }

        if ($published === true) {
            $db->andWhereEquals(self::column('published'), '1')
               ->orderBy(new RawSqlFragment(sprintf('`order`, CASE WHEN `order` = 0 THEN %s END DESC', self::column('id'))));
        } else {
            $db->orderBy(self::column('id'))
               ->desc();
        }

        // Paginate if required
        if ($page !== null && $itemsPerPage !== null) {
            $db->paginate($page, $itemsPerPage);
        }

        return $db->queryAll();
    }
}
