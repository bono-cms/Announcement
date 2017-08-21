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
use Krystal\Db\Sql\QueryBuilderInterface;

final class SearchMapper extends AbstractMapper
{
    /**
     * {@inheritDoc}
     */
    public function appendQuery(QueryBuilderInterface $queryBuilder, $placeholder)
    {
        // Columns to be selected
        $columns = array(
            AnnounceMapper::getFullColumnName('id'),
            AnnounceTranslationMapper::getFullColumnName('web_page_id'),
            AnnounceTranslationMapper::getFullColumnName('lang_id'),
            AnnounceTranslationMapper::getFullColumnName('title'),
            AnnounceTranslationMapper::getFullColumnName('full') => 'content',
            AnnounceTranslationMapper::getFullColumnName('name')
        );

        $queryBuilder->select($columns)
                     ->from(AnnounceMapper::getTableName())
                     // Translation relation
                     ->innerJoin(AnnounceTranslationMapper::getTableName())
                     ->on()
                     ->equals(
                        AnnounceMapper::getFullColumnName('id'),
                        AnnounceTranslationMapper::getFullColumnName('id')
                     )
                     // Constraints
                     ->whereEquals(AnnounceTranslationMapper::getFullColumnName('lang_id'), "'{$this->getLangId()}'")
                     ->andWhereEquals(AnnounceMapper::getFullColumnName('published'), '1')
                     // Search
                     ->andWhereLike(AnnounceTranslationMapper::getFullColumnName('name'), $placeholder)
                     ->orWhereLike(AnnounceTranslationMapper::getFullColumnName('full'), $placeholder);
    }
}
