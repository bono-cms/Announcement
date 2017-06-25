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
use Cms\Service\HistoryManagerInterface;
use Announcement\Storage\AnnounceMapperInterface;
use Announcement\Storage\CategoryMapperInterface;
use Menu\Contract\MenuAwareManager;
use Krystal\Stdlib\VirtualEntity;
use Krystal\Stdlib\ArrayUtils;
use Krystal\Security\Filter;

final class AnnounceManager extends AbstractManager implements AnnounceManagerInterface, MenuAwareManager
{
    /**
     * Any compliant announce mapper
     * 
     * @var \Announcement\Storage\AnnounceMapperInterface
     */
    private $announceMapper;

    /**
     * Any compliant category mapper
     * 
     * @var \Announcement\Storage\CategoryMapperInterface
     */
    private $categoryMapper;

    /**
     * Web page manager for dealing with slugs
     * 
     * @var \Cms\Service\WebPageManagerInterface
     */
    private $webPageManager;

    /**
     * History manager to keep track
     * 
     * @var \Cms\Service\HistoryManagerInterface
     */
    private $historyManager;

    /**
     * State initialization
     * 
     * @param \Announcement\Storage\AnnounceMapperInterface $announceMapper Any mapper which implements AnnounceMapperInterface
     * @param \Announcement\Storage\CategoryMapperInterface $categoryMapper Any mapper which implements CategoryMapperInterface
     * @param \Cms\Service\WebPageManagerInterface $webPageManager
     * @param \Cms\Service\HistoryManagerInterface $historyManager
     * @return void
     */
    public function __construct(
        AnnounceMapperInterface $announceMapper, 
        CategoryMapperInterface $categoryMapper, 
        WebPageManagerInterface $webPageManager,
        HistoryManagerInterface $historyManager
    ){
        $this->announceMapper = $announceMapper;
        $this->categoryMapper = $categoryMapper;
        $this->webPageManager = $webPageManager;
        $this->historyManager = $historyManager;
    }

    /**
     * Tracks activity
     * 
     * @param string $message
     * @param string $placeholder
     * @return boolean
     */
    private function track($message, $placeholder)
    {
        return $this->historyManager->write('Announcement', $message, $placeholder);
    }

    /**
     * {@inheritDoc}
     */
    public function fetchNameByWebPageId($webPageId)
    {
        return $this->announceMapper->fetchNameByWebPageId($webPageId);
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
     * {@inheritDoc}
     */
    protected function toEntity(array $announce, $full = false)
    {
        $entity = new VirtualEntity();
        $entity->setId($announce['id'], VirtualEntity::FILTER_INT)
            ->setCategoryId($announce['category_id'], VirtualEntity::FILTER_INT)
            ->setLangId($announce['lang_id'], VirtualEntity::FILTER_INT)
            ->setWebPageId($announce['web_page_id'], VirtualEntity::FILTER_INT)
            ->setCategoryName($this->categoryMapper->fetchNameById($announce['category_id']), VirtualEntity::FILTER_HTML)
            ->setName($announce['name'], VirtualEntity::FILTER_HTML)
            ->setPublished($announce['published'], VirtualEntity::FILTER_BOOL)
            ->setOrder($announce['order'], VirtualEntity::FILTER_INT)
            ->setSeo($announce['seo'], VirtualEntity::FILTER_BOOL)
            ->setSlug($announce['slug'], VirtualEntity::FILTER_HTML)
            ->setUrl($this->webPageManager->surround($entity->getSlug(), $entity->getLangId()));

        if ($full === true) {
            $entity->setTitle($announce['title'], VirtualEntity::FILTER_HTML)
                   ->setIntro($announce['intro'], VirtualEntity::FILTER_SAFE_TAGS)
                   ->setFull($announce['full'], VirtualEntity::FILTER_SAFE_TAGS)
                   ->setIcon($announce['icon'], VirtualEntity::FILTER_HTML)
                   ->setKeywords($announce['keywords'], VirtualEntity::FILTER_HTML)
                   ->setMetaDescription($announce['meta_description'], VirtualEntity::FILTER_HTML)
                   ->setPermanentUrl('/module/announcement/'.$entity->getId());
        }

        return $entity;
    }

    /**
     * Saves a page
     * 
     * @param array $input
     * @return boolean
     */
    private function savePage(array $input)
    {
        $input['announce']['order'] = (int) $input['announce']['order'];

        return $this->announceMapper->savePage('Announcement', 'Announcement:Announce@indexAction', $input['announce'], $input['translation']);
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
     * Adds an announce
     * 
     * @param array $input Raw input data
     * @return boolean
     */
    public function add(array $input)
    {
        $this->savePage($input);

        #$this->track('Announce "%s" has been added', $input['name']);
        return true;
    }

    /**
     * Updates an announce
     * 
     * @param array $input Raw form data
     * @return boolean
     */
    public function update(array $input)
    {
        $this->savePage($input);

        #$this->track('Announce "%s" has been updated', $input['name']);
        return true;
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

    /**
     * Deletes an announce by its associated id
     * 
     * @param string $id Announce id
     * @return boolean
     */
    public function deleteById($id)
    {
        // Grab announce's title before we remove it
        #$title = Filter::escape($this->announceMapper->fetchTitleById($id));

        if ($this->announceMapper->deletePage($id)) {
            #$this->track('Announce "%s" has been removed', $title);
            return true;

        } else {
            return false;
        }
    }

    /**
     * Delete announces by their associated ids
     * 
     * @param array $ids Array of announce ids
     * @return boolean
     */
    public function deleteByIds(array $ids)
    {
        $this->announceMapper->deletePage($ids);

        $this->track('Batch removal of %s announces', count($ids));
        return true;
    }
}
