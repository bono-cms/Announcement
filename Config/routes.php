<?php

/**
 * This file is part of the Bono CMS
 * 
 * Copyright (c) No Global State Lab
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

return array(
    '/module/announcement/(:var)' => array(
        'controller' => 'Announce@indexAction'
    ),
    
    '/%s/module/announcement' => array(
        'controller' => 'Admin:Browser@indexAction',
    ),
    
    '/%s/module/announcement/category/add' => array(
        'controller' => 'Admin:Category@addAction'
    ),
    
    '/%s/module/announcement/category/edit/(:var)' => array(
        'controller' => 'Admin:Category@editAction'
    ),
    
    '/%s/module/announcement/category/save' => array(
        'controller' => 'Admin:Category@saveAction',
        'disallow' => array('guest')
    ),
    
    '/%s/module/announcement/category/delete/(:var)' => array(
        'controller' => 'Admin:Category@deleteAction',
        'disallow' => array('guest')
    ),
    
    '/%s/module/announcement/category/view/(:var)' => array(
        'controller' => 'Admin:Browser@categoryAction'
    ),
    
    '/%s/module/announcement/category/view/(:var)/page/(:var)' => array(
        'controller' => 'Admin:Browser@categoryAction'
    ),
    
    '/%s/module/announcement/page/(:var)' => array(
        'controller' => 'Admin:Browser@indexAction'
    ),
    
    '/%s/module/announcement/announce/tweak.ajax' => array(
        'controller' => 'Admin:Browser@tweakAction',
        'disallow' => array('guest')
    ),
    
    '/%s/module/announcement/announce/delete/(:var)' => array(
        'controller' => 'Admin:Announce@deleteAction',
        'disallow' => array('guest')
    ),
    
    '/%s/module/announcement/announce/add' => array(
        'controller' => 'Admin:Announce@addAction'
    ),
    
    '/%s/module/announcement/announce/edit/(:var)' => array(
        'controller' => 'Admin:Announce@editAction'
    ),
    
    '/%s/module/announcement/announce/save' => array(
        'controller'    => 'Admin:Announce@saveAction',
        'disallow' => array('guest')
    )
);
