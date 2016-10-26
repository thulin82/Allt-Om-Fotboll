<?php
/**
 * Config-file for navigation bar.
 *
 */
return [

    // Use for styling the menu
    'class' => 'navbar',
 
    // Here comes the menu strcture
    'items' => [

        // This is a menu item
        'home'  => [
            'text'  => '<i class="fa fa-home fa-fw"></i>Home',
            'url'   => $this->di->get('url')->create(''),
            'title' => 'Home'
        ],

        // This is a menu item
        'questions'  => [
            'text'  => '<i class="fa fa-question-circle fa-fw"></i>Questions',
            'url'   => $this->di->get('url')->create('questions'),
            'title' => 'Questions'
        ],
        
        // This is a menu item
        'users' => [
            'text'  =>'<i class="fa fa-users fa-fw"></i>Users',
            'url'   => $this->di->get('url')->create('users'),
            'title' => 'Users',
            
            // Here we add the submenu, with some menu items, as part of a existing menu item
            'submenu' => [

                'items' => [
                    
                    // This is a menu item of the submenu
                    'item 0'  => [
                        'text'  => 'Add user',
                        'url'   => $this->di->get('url')->create('users/add'),
                        'title' => 'Add user'
                    ],

                    // This is a menu item of the submenu
                    'item 1'  => [
                        'text'  => 'Login user',
                        'url'   => $this->di->get('url')->create('users/login'),
                        'title' => 'Login user'
                    ],

                    // This is a menu item of the submenu
                    'item 2'  => [
                        'text'  => 'Logout user',
                        'url'   => $this->di->get('url')->create('users/logout'),
                        'title' => 'Logout user'
                    ],
                ],
            ],
        ],

        // This is a menu item
        'tags'  => [
            'text'  => '<i class="fa fa-tags fa-fw"></i>Tags',
            'url'   => $this->di->get('url')->create('tags'),
            'title' => 'Tags'
        ],

        // This is a menu item
        'about'  => [
            'text'  => '<i class="fa fa-info fa-fw"></i>About',
            'url'   => $this->di->get('url')->create('about'),
            'title' => 'About'
        ],
    ],


    /**
     * Callback tracing the current selected menu item base on scriptname
     *
     */
    'callback' => function ($url) {
        if ($url == $this->di->get('request')->getCurrentUrl(false)) {
            return true;
        }
    },



    /**
     * Callback to check if current page is a decendant of the menuitem, this check applies for those
     * menuitems that has the setting 'mark-if-parent' set to true.
     *
     */
    'is_parent' => function ($parent) {
        $route = $this->di->get('request')->getRoute();
        return !substr_compare($parent, $route, 0, strlen($parent));
    },



   /**
     * Callback to create the url, if needed, else comment out.
     *
     */
   /*
    'create_url' => function ($url) {
        return $this->di->get('url')->create($url);
    },
    */
];
