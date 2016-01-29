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
use Krystal\Validate\Pattern;
use Krystal\Stdlib\VirtualEntity;

final class Category extends AbstractController
{
    /**
     * Returns a form
     * 
     * @param \Krystal\Stdlib\VirtualEntity $category
     * @param string $title
     * @return string
     */
    private function createForm(VirtualEntity $category, $title)
    {
        // Append a breadcrumb
        $this->view->getBreadcrumbBag()->addOne('Announcement', 'Announcement:Admin:Browser@indexAction')
                                       ->addOne($title);

        return $this->view->render('category.form', array(
            'title' => $title,
            'category' => $category
        ));
    }

    /**
     * Renders empty form
     * 
     * @return string
     */
    public function addAction()
    {
        return $this->createForm(new VirtualEntity(), 'Add a category');
    }

    /**
     * Renders edit form
     * 
     * @param string $id
     * @return string
     */
    public function editAction($id)
    {
        $category = $this->getModuleService('categoryManager')->fetchById($id);

        if ($category !== false) {
            return $this->createForm($category, 'Edit the category');
        } else {
            return false;
        }
    }

    /**
     * Deletes a category by its associated id
     * 
     * @return string
     */
    public function deleteAction()
    {
        if ($this->request->hasPost('id')) {
            $id = $this->request->getPost('id');

            $categoryManager = $this->getModuleService('categoryManager');
            $categoryManager->deleteById($id);

            $this->flashBag->set('success', 'Selected category has been removed successfully');
            return '1';
        }
    }

    /**
     * Persists a category
     * 
     * @return string
     */
    public function saveAction()
    {
        $input = $this->request->getPost('category');

        $formValidator = $this->validatorFactory->build(array(
            'input' => array(
                'source' => $input,
                'definition' => array(
                    'name' => new Pattern\Name()
                )
            )
        ));

        if ($formValidator->isValid()) {
            $categoryManager = $this->getModuleService('categoryManager');

            if ($input['id']) {
                if ($categoryManager->update($input)) {
                    $this->flashBag->set('success', 'Category has been updated successfully');
                    return '1';
                }
            } else {
                if ($categoryManager->add($input)) {
                    $this->flashBag->set('success', 'A category has been created successfully');
                    return $categoryManager->getLastId();
                }
            }

        } else {
            return $formValidator->getErrors();
        }
    }
}
