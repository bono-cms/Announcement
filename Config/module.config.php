<?php

/**
 * Module configuration container
 */

return array(
    'caption'  => 'Announcement',
    'description' => 'Announcement module allows you to show different marketing announces on your site',
    'menu' => array(
        'name'  => 'Announcement',
        'icon' => 'fas fa-scroll',
        'items' => array(
            array(
                'route' => 'Announcement:Admin:Browser@indexAction',
                'name' => 'View all announces',
            ),
            array(
                'route' => 'Announcement:Admin:Announce@addAction',
                'name' => 'Add new announce'
            ),
            array(
                'route' => 'Announcement:Admin:Category@addAction',
                'name' => 'Add a category'
            )
        )
    )
);