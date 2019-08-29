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
        // CMS configuration object
        $config = $this->getService('Cms', 'configManager')->getEntity();

        $announce = new VirtualEntity();
        $announce->setSeo(true)
                 ->setPublished(true)
                 ->setChangeFreq($config->getSitemapFrequency())
                 ->setPriority($config->getSitemapPriority());

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
            $name = $this->getCurrentProperty($announce, 'name');
            return $this->createForm($announce, $this->translator->translate('Edit the announce "%s"', $name));
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
        $historyService = $this->getService('Cms', 'historyManager');
        $service = $this->getModuleService('announceManager');

        // Batch removal
        if ($this->request->hasPost('batch')) {
            $ids = array_keys($this->request->getPost('batch'));

            $service->delete($ids);
            $this->flashBag->set('success', 'Selected elements have been removed successfully');

            // Save in the history
            $historyService->write('Announcement', 'Batch removal of %s announces', count($ids));

        } else {
            $this->flashBag->set('warning', 'You should select at least one element to remove');
        }

        // Single removal
        if (!empty($id)) {

            $announce = $this->getModuleService('announceManager')->fetchById($id, false);
            // Save in the history
            $historyService->write('Announcement', 'Announce "%s" has been removed', $announce->getName());

            $service->delete($id);
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
            $historyService = $this->getService('Cms', 'historyManager');

            // Current announce name
            $name = $this->getCurrentProperty($this->request->getPost('translation'), 'name');

            // Save an announce
            $service->save($this->request->getPost());

            if (!empty($input['id'])) {
                $this->flashBag->set('success', 'The element has been updated successfully');

                $historyService->write('Announcement', 'Announce "%s" has been updated', $name);
                return '1';

            } else {
                $this->flashBag->set('success', 'The element has been created successfully');

                $historyService->write('Announcement', 'Announce "%s" has been added', $name);
                return $service->getLastId();
            }

        } else {
            return $formValidator->getErrors();
        }
    }
}
