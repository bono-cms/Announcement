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
            AnnounceMapper::getFullColumnName('web_page_id', AnnounceMapper::getTranslationTable()),
            AnnounceMapper::getFullColumnName('lang_id', AnnounceMapper::getTranslationTable()),
            AnnounceMapper::getFullColumnName('title', AnnounceMapper::getTranslationTable()),
            AnnounceMapper::getFullColumnName('full', AnnounceMapper::getTranslationTable()) => 'content',
            AnnounceMapper::getFullColumnName('name', AnnounceMapper::getTranslationTable())
        );

        $queryBuilder->select($columns)
                     ->from(AnnounceMapper::getTableName())
                     // Translation relation
                     ->innerJoin(AnnounceMapper::getTranslationTable())
                     ->on()
                     ->equals(
                        AnnounceMapper::getFullColumnName('id'),
                        AnnounceMapper::getFullColumnName('id', AnnounceMapper::getTranslationTable())
                     )
                     // Constraints
                     ->whereEquals(AnnounceMapper::getFullColumnName('lang_id', AnnounceMapper::getTranslationTable()), "'{$this->getLangId()}'")
                     ->andWhereEquals(AnnounceMapper::getFullColumnName('published'), '1')
                     // Search
                     ->andWhereLike(AnnounceMapper::getFullColumnName('name', AnnounceMapper::getTranslationTable()), $placeholder)
                     ->orWhereLike(AnnounceMapper::getFullColumnName('full', AnnounceMapper::getTranslationTable()), $placeholder);
    }
}
