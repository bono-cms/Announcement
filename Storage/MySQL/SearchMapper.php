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
            AnnounceMapper::column('id'),
            AnnounceTranslationMapper::column('web_page_id'),
            AnnounceTranslationMapper::column('lang_id'),
            AnnounceTranslationMapper::column('title'),
            AnnounceTranslationMapper::column('full') => 'content',
            AnnounceTranslationMapper::column('name')
        );

        $queryBuilder->select($columns)
                     ->from(AnnounceMapper::getTableName())
                     // Translation relation
                     ->innerJoin(AnnounceTranslationMapper::getTableName(), array(
                        AnnounceMapper::column('id') => AnnounceTranslationMapper::getRawColumn('id')
                     ))
                     // Constraints
                     ->whereEquals(AnnounceTranslationMapper::column('lang_id'), "'{$this->getLangId()}'")
                     ->andWhereEquals(AnnounceMapper::column('published'), '1')
                     // Search
                     ->andWhereLike(AnnounceTranslationMapper::column('name'), $placeholder)
                     ->orWhereLike(AnnounceTranslationMapper::column('full'), $placeholder);
    }
}
