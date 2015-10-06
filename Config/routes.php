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
	
	'/admin/module/announcement' => array(
		'controller' => 'Admin:Browser@indexAction',
	),
	
	'/admin/module/announcement/category/add' => array(
		'controller' => 'Admin:Category:Add@indexAction'
	),
	
	'/admin/module/announcement/category/add.ajax' => array(
		'controller' => 'Admin:Category:Add@addAction',
		'disallow' => array('guest')
	),
	
	'/admin/module/announcement/category/edit/(:var)' => array(
		'controller' => 'Admin:Category:Edit@indexAction'
	),
	
	'/admin/module/announcement/category/edit.ajax' => array(
		'controller' => 'Admin:Category:Edit@updateAction',
		'disallow' => array('guest')
	),
	
	'/admin/module/announcement/category/delete.ajax' => array(
		'controller' => 'Admin:Browser@deleteCategoryAction',
		'disallow' => array('guest')
	),
	
	'/admin/module/announcement/category/view/(:var)' => array(
		'controller' => 'Admin:Browser@categoryAction'
	),
	
	'/admin/module/announcement/category/view/(:var)/page/(:var)' => array(
		'controller' => 'Admin:Browser@categoryAction'
	),
	
	'/admin/module/announcement/page/(:var)' => array(
		'controller' => 'Admin:Browser@indexAction'
	),
	
	'/admin/module/announcement/announce/save.ajax' => array(
		'controller' => 'Admin:Browser@saveAction',
		'disallow' => array('guest')
	),
	
	'/admin/module/announcement/announce/delete.ajax' => array(
		'controller' => 'Admin:Browser@deleteAction',
		'disallow' => array('guest')
	),
	
	'/admin/module/announcement/announce/delete-selected.ajax'	=>	array(
		'controller' => 'Admin:Browser@deleteSelectedAction',
		'disallow' => array('guest')
	),
	
	'/admin/module/announcement/announce/add' => array(
		'controller' => 'Admin:Announce:Add@indexAction'
	),
	
	'/admin/module/announcement/announce/add.ajax' => array(
		'controller'	=> 'Admin:Announce:Add@addAction',
		'disallow' => array('guest')
	),
	
	'/admin/module/announcement/announce/edit/(:var)' => array(
		'controller' => 'Admin:Announce:Edit@indexAction'
	),
	
	'/admin/module/announcement/announce/edit.ajax' => array(
		'controller'	=> 'Admin:Announce:Edit@updateAction',
		'disallow' => array('guest')
	)
);
