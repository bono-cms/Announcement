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

final class Announce extends AbstractController
{
    /**
     * Creates a form
     * 
     * @param Krystal\Stdlib\VirtualEntity|array $announce
     * @param string $title
     * @return string
     */
    private function createForm($announce, $title)
    {
        // Load view plugins
        $this->view->getPluginBag()
                   ->load($this->getWysiwygPluginName());

        // Append breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Announcement', 'Announcement:Admin:Browser@indexAction')
                                       ->addOne($title);

        return $this->view->render('announce.form', array(
            'announce' => $announce,
            'new' => is_object($announce),
            'categories' => $this->getModuleService('categoryManager')->fetchList()
        ));
    }

    /**
     * Renders empty form
     * 
     * @return string
     */
    public function addAction()
    {
        $announce = new VirtualEntity();
        $announce->setSeo(true)
                 ->setPublished(true);

        return $this->createForm($announce, 'Add new announce');
    }

    /**
     * Renders edit form
     * 
     * @param string $id
     * @return string
     */
    public function editAction($id)
    {
        $announce = $this->getModuleService('announceManager')->fetchById($id, true);

        if (!empty($announce)) {
            return $this->createForm($announce, 'Edit the announce');
        } else {
            return false;
        }
    }

    /**
     * Delete selected announces
     * 
     * @param string $id
     * @return string
     */
    public function deleteAction($id)
    {
        $service = $this->getModuleService('announceManager');

        // Batch removal
        if ($this->request->hasPost('toDelete')) {
            $ids = array_keys($this->request->getPost('toDelete'));

            $service->deleteByIds($ids);
            $this->flashBag->set('success', 'Selected elements have been removed successfully');

        } else {
            $this->flashBag->set('warning', 'You should select at least one element to remove');
        }

        // Single removal
        if (!empty($id)) {
            $service->deleteById($id);
            $this->flashBag->set('success', 'Selected element has been removed successfully');
        }

        return '1';
    }

    /**
     * Persists an announce
     * 
     * @return string
     */
    public function saveAction()
    {
        $input = $this->request->getPost('announce');

        $formValidator = $this->createValidator(array(
            'input' => array(
                'source' => $input,
                'definition' => array(
                    'title' => new Pattern\Title(array('required' => false)),
                    'name' => new Pattern\Name(),
                    'intro' => new Pattern\IntroText(),
                    'full' => new Pattern\FullText()
                )
            )
        ));

        if (1) {
            $service = $this->getModuleService('announceManager');

            if (!empty($input['id'])) {
                if ($service->update($this->request->getPost())) {
                    $this->flashBag->set('success', 'The element has been updated successfully');
                    return '1';
                }

            } else {
                if ($service->add($this->request->getPost())) {
                    $this->flashBag->set('success', 'The element has been created successfully');
                    return $service->getLastId();
                }
            }

        } else {
            return $formValidator->getErrors();
        }
    }
}
