<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Announcement\Controller\Admin;

use Cms\Controller\Admin\AbstractController;

final class Browser extends AbstractController
{
    /**
     * Creates a grid
     * 
     * @param array $vars
     * @return string
     */
    private function createGrid(array $vars)
    {
        // Append a breadcrumb
        $this->view->getBreadcrumbBag()->addOne('Announcement');

        $defaults = array(
            'categories' => $this->getModuleService('categoryManager')->fetchAll(),
        );

        $vars = array_replace_recursive($defaults, $vars);
        return $this->view->render('browser', $vars);
    }

    /**
     * Shows a table
     * 
     * @param integer $page Current page
     * @return string
     */
    public function indexAction($page = 1)
    {
        $paginator = $this->getModuleService('announceManager')->getPaginator();
        $paginator->setUrl($this->createUrl('Announcement:Admin:Browser@indexAction', array(), 1));

        return $this->createGrid(array(
            'announces' => $this->getModuleService('announceManager')->fetchAll($page, $this->getSharedPerPageCount(), false),
            'paginator' => $paginator,
            'categoryId' => null
        ));
    }

    /**
     * Filters by category id
     * 
     * @param string $id Category id
     * @param integer $page Current page
     * @return string
     */
    public function categoryAction($id, $page = 1)
    {
        $paginator = $this->getModuleService('announceManager')->getPaginator();
        $paginator->setUrl($this->createUrl('Announcement:Admin:Browser@categoryAction', array($id), 1));

        return $this->createGrid(array(
            'categoryId' => $id,
            'announces' => $this->getModuleService('announceManager')->fetchAll($page, $this->getSharedPerPageCount(), false, $id),
            'paginator' => $paginator,
        ));
    }

    /**
     * Save settings from the grid
     * 
     * @return string
     */
    public function tweakAction()
    {
        if ($this->request->isPost()) {
            // Grab a service
            $announceManager = $this->getModuleService('announceManager');
            $announceManager->updateSettings($this->request->getPost());

            $this->flashBag->set('success', 'Announce settings have been updated successfully');
            return '1';
        }
    }
}
