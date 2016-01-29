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
     * @param \Krystal\Stdlib\VirtualEntity $announce
     * @param string $title
     * @return string
     */
    private function createForm(VirtualEntity $announce, $title)
    {
        // Load view plugins
        $this->view->getPluginBag()->load($this->getWysiwygPluginName())
                                   ->appendScript('@Announcement/admin/announce.form.js');

        // Append breadcrumbs
        $this->view->getBreadcrumbBag()->addOne('Announcement', 'Announcement:Admin:Browser@indexAction')
                                       ->addOne($title);

        return $this->view->render('announce.form', array(
            'title' => $title,
            'announce' => $announce,
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
        $announce->setSeo(true);
        $announce->setPublished(true);

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
        $announce = $this->getModuleService('announceManager')->fetchById($id);

        if ($announce !== false) {
            return $this->createForm($announce, 'Edit the announce');
        } else {
            return false;
        }
    }

    /**
     * Delete selected announces
     * 
     * @return string
     */
    public function deleteAction()
    {
        $announceManager = $this->getModuleService('announceManager');        

        // Batch removal
        if ($this->request->hasPost('toDelete')) {
            $ids = array_keys($this->request->getPost('toDelete'));

            $announceManager->deleteByIds($ids);
            $this->flashBag->set('success', 'Selected announces have been removed successfully');
        } else {
            $this->flashBag->set('warning', 'You should select at least one announce to remove');
        }

        // Single removal
        if ($this->request->hasPost('id')) {
            $id = $this->request->getPost('id');

            $announceManager->deleteById($id);
            $this->flashBag->set('success', 'The announces have been removed successfully');
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

        $formValidator = $this->validatorFactory->build(array(
            'input' => array(
                'source' => $input,
                'definition' => array(
                    'title' => new Pattern\Title(),
                    'name' => new Pattern\Name(),
                    'intro' => new Pattern\IntroText(),
                    'full' => new Pattern\FullText()
                )
            )
        ));

        if ($formValidator->isValid()) {
            $announceManager = $this->getModuleService('announceManager');

            if ($input['id']) {
                if ($announceManager->update($this->request->getPost('announce'))) {
                    $this->flashBag->set('success', 'The announce has been updated successfully');
                    return '1';
                }

            } else {
                if ($announceManager->add($this->request->getPost('announce'))) {
                    $this->flashBag->set('success', 'An announce has been created successfully');
                    return $announceManager->getLastId();
                }
            }

        } else {
            return $formValidator->getErrors();
        }
    }
}
