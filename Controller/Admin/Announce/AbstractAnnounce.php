<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Announcement\Controller\Admin\Announce;

use Announcement\Controller\Admin\AbstractAdminController;
use Krystal\Validate\Pattern;

abstract class AbstractAnnounce extends AbstractAdminController
{
    /**
     * Returns shared form validator
     * 
     * @param array $input Raw input data
     * @return \Krystal\Validate\ValidatorChain
     */
    final protected function getValidator(array $input)
    {
        return $this->validatorFactory->build(array(
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
    }

    /**
     * Returns shared variables
     * 
     * @param array $overrides
     * @return array
     */
    final protected function getWithSharedVars(array $overrides)
    {
        $this->view->getBreadcrumbBag()->add(array(
            array(
                'link' => 'Announcement:Admin:Browser@indexAction',
                'name' => 'Announcement'
            ),
            array(
                'link' => '#',
                'name' => $overrides['title']
            )
        ));

        $vars = array(
            'categories' => $this->getModuleService('categoryManager')->fetchList()
        );

        return array_replace_recursive($vars, $overrides);
    }

    /**
     * Returns template path
     * 
     * @return string
     */
    final protected function getTemplatePath()
    {
        return 'announce.form';
    }

    /**
     * Loads shared plugins
     * 
     * @return void
     */
    final protected function loadSharedPlugins()
    {
        $this->view->getPluginBag()->load($this->getWysiwygPluginName())
                                   ->appendScript($this->getWithAssetPath('/admin/announce.form.js'));
    }
}
