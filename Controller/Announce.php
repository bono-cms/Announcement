<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Announcement\Controller;

use Site\Controller\AbstractController;

final class Announce extends AbstractController
{
    /**
     * Renders an announce by its associated id
     * 
     * @param string $id
     * @return string
     */
    public function indexAction($id)
    {
        $announce = $this->getModuleService('announceManager')->fetchById($id);

        if ($announce !== false) {
            // Load view plugins
            $this->loadSitePlugins();
            $this->view->getBreadcrumbBag()
                       ->addOne($announce->getName());

            return $this->view->render('announce', array(
                'page' => $announce,
                'announce' => $announce
            ));

        } else {
            return false;
        }
    }
}
