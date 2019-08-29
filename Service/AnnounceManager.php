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
use Cms\Service\WebPageManagerInterface;
use Announcement\Storage\AnnounceMapperInterface;
use Krystal\Stdlib\VirtualEntity;

final class AnnounceManager extends AbstractManager
{
    /**
     * Any compliant announce mapper
     * 
     * @var \Announcement\Storage\AnnounceMapperInterface
     */
    private $announceMapper;

    /**
     * Web page manager for dealing with slugs
     * 
     * @var \Cms\Service\WebPageManagerInterface
     */
    private $webPageManager;

    /**
     * State initialization
     * 
     * @param \Announcement\Storage\AnnounceMapperInterface $announceMapper Any mapper which implements AnnounceMapperInterface
     * @param \Cms\Service\WebPageManagerInterface $webPageManager
     * @return void
     */
    public function __construct(AnnounceMapperInterface $announceMapper, WebPageManagerInterface $webPageManager)
    {
        $this->announceMapper = $announceMapper;
        $this->webPageManager = $webPageManager;
    }

    /**
     * {@inheritDoc}
     */
    protected function toEntity(array $announce, $full = false)
    {
        $entity = new VirtualEntity();
        $entity->setId($announce['id'], VirtualEntity::FILTER_INT)
            ->setCategoryId($announce['category_id'], VirtualEntity::FILTER_INT)
            ->setLangId($announce['lang_id'], VirtualEntity::FILTER_INT)
            ->setWebPageId($announce['web_page_id'], VirtualEntity::FILTER_INT)
            ->setCategoryName(isset($announce['category']) ? $announce['category'] : null, VirtualEntity::FILTER_HTML)
            ->setName($announce['name'], VirtualEntity::FILTER_HTML)
            ->setPublished($announce['published'], VirtualEntity::FILTER_BOOL)
            ->setOrder($announce['order'], VirtualEntity::FILTER_INT)
            ->setSeo($announce['seo'], VirtualEntity::FILTER_BOOL)
            ->setSlug($announce['slug'], VirtualEntity::FILTER_HTML)
            ->setIcon($announce['icon'], VirtualEntity::FILTER_HTML)
            ->setIntro($announce['intro'], VirtualEntity::FILTER_SAFE_TAGS)
            ->setUrl($this->webPageManager->surround($entity->getSlug(), $entity->getLangId()))
            ->setChangeFreq($announce['changefreq'])
            ->setPriority($announce['priority']);

        if ($full === true) {
            $entity->setTitle($announce['title'], VirtualEntity::FILTER_HTML)
                   ->setFull($announce['full'], VirtualEntity::FILTER_SAFE_TAGS)
                   ->setKeywords($announce['keywords'], VirtualEntity::FILTER_HTML)
                   ->setMetaDescription($announce['meta_description'], VirtualEntity::FILTER_HTML)
                   ->setPermanentUrl('/module/announcement/'.$entity->getId());
        }

        return $entity;
    }

    /**
     * Update settings
     * 
     * @param array $settings
     * @return boolean
     */
    public function updateSettings(array $settings)
    {
        return $this->announceMapper->updateSettings($settings);
    }

    /**
     * Saves an announce
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function save(array $input)
    {
        $input['announce']['order'] = (int) $input['announce']['order'];
        return $this->announceMapper->savePage('Announcement', 'Announcement:Announce@indexAction', $input['announce'], $input['translation']);
    }

    /**
     * Deletes an announce by its associated id
     * 
     * @param string|array $id Announce id
     * @return boolean
     */
    public function delete($id)
    {
        return $this->announceMapper->deletePage($id);
    }

    /**
     * Returns last announce id
     * 
     * @return integer
     */
    public function getLastId()
    {
        return $this->announceMapper->getLastId();
    }

    /**
     * Returns prepared paginator's instance
     * 
     * @return \Krystal\Paginate\Paginator
     */
    public function getPaginator()
    {
        return $this->announceMapper->getPaginator();
    }

    /**
     * Returns a collection of switching URLs
     * 
     * @param string $id Announce ID
     * @return array
     */
    public function getSwitchUrls($id)
    {
        return $this->announceMapper->createSwitchUrls($id, 'Announcement', 'Announcement:Announce@indexAction');
    }

    /**
     * Fetches announce's entity by its associated id
     * 
     * @param string $id
     * @param boolean $withTranslations Whether to fetch all translations or not
     * @return \Krystal\Stdlib\VirtualEntity|boolean
     */
    public function fetchById($id, $withTranslations)
    {
        if ($withTranslations === true) {
            return $this->prepareResults($this->announceMapper->fetchById($id, true), true);
        } else {
            return $this->prepareResult($this->announceMapper->fetchById($id, false), true);
        }
    }

    /**
     * Fetches all announce entities
     * 
     * @param integer $page Current page number
     * @param integer $itemsPerPage Per page count
     * @param boolean $published Whether to fetch only published announces
     * @param integer $categoryId Optional category ID filter
     * @return array
     */
    public function fetchAll($page, $itemsPerPage, $published, $categoryId = null)
    {
        return $this->prepareResults($this->announceMapper->fetchAll($page, $itemsPerPage, $published, $categoryId), false);
    }
}
