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

        // Load view plugins
        $this->view->getPluginBag()
                   ->appendScript('@Announcement/admin/browser.js');

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
        $paginator->setUrl('/admin/module/announcement/page/(:var)');

        return $this->createGrid(array(
            'announces' => $this->getModuleService('announceManager')->fetchAllByPage($page, $this->getSharedPerPageCount()),
            'paginator' => $paginator,
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
        $paginator->setUrl('/admin/module/announcement/category/view/'.$id.'/page/(:var)');

        return $this->createGrid(array(
            'categoryId' => $id,
            'announces' => $this->getModuleService('announceManager')->fetchAllByCategoryIdAndPage($id, $page, $this->getSharedPerPageCount()),
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
        if ($this->request->hasPost('seo', 'published', 'order')) {
            $published = $this->request->getPost('published');
            $seo = $this->request->getPost('seo');
            $orders = $this->request->getPost('order');

            // Grab a service
            $announceManager = $this->getModuleService('announceManager');

            $announceManager->updatePublished($published);
            $announceManager->updateSeo($seo);
            $announceManager->updateOrders($orders);

            $this->flashBag->set('success', 'Announce settings have been updated successfully');
            return '1';
        }
    }
}
