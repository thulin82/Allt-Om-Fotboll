<?php
/**
 * Config-file for Anax, theme related settings, return it all as array.
 *
 */
return [

    /**
     * Settings for Which theme to use, theme directory is found by path and name.
     *
     * path: where is the base path to the theme directory, end with a slash.
     * name: name of the theme is mapped to a directory right below the path.
     */
    'settings' => [
        'path' => ANAX_APP_PATH,
        'name' => 'theme',
    ],

    
    /**
     * Add default views.
     */
    'views' => [
        [
            'region'   => 'header', 
            'template' => 'me/header', 
            'data'     => [
                'siteTitle' => "Allt Om Fotboll",
                'siteTagline' => "a product by WGTOTW",
                'siteLogo' => "football.png",
                'siteLogoAlt' => "Fotboll",
            ], 
            'sort'     => -1
        ],
        [   'region' => 'footer',
            'template' => 'me/footer',
            'data' => [],
            'sort' => -1
        ],
        [
            'region' => 'navbar', 
            'template' => [
                'callback' => function () {
                    return $this->di->navbar->create();
                },
            ], 
            'data' => [], 
            'sort' => -1
        ],
    ],


    /**
     * Data to extract and send as variables to the main template file.
     */
    'data' => [

        // Language for this page.
        'lang' => 'sv',

        // Append this value to each <title>
        'title_append' => ' | Allt Om Fotboll',

        // Stylesheets
        'stylesheets' => ['css/style.css', 'css/navbar.css', 'css/form.css', 'css/comment.css'],

        // Inline style
        'style' => null,

        // Favicon
        'favicon' => 'favicon.ico',

        // Path to modernizr or null to disable
        'modernizr' => null,

        // Path to jquery or null to disable
        'jquery' => null,

        // Array with javscript-files to include
        'javascript_include' => [],

        // Use google analytics for tracking, set key or null to disable
        'google_analytics' => null,
    ],
];
