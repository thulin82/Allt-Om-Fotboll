<?php
/**
 * Enable autoloaders.
 *
 */


/**
 * Default Anax autoloader, and the add specifics through a self invoking anonomous function.
 * Add autoloader for namespace Anax and a default directory for unknown vendor namespaces.
 */
require ANAX_INSTALL_PATH . 'src/Loader/CPsr4Autoloader.php';

call_user_func(function () {
    $loader = new \Anax\Loader\CPsr4Autoloader();
    $loader->addNamespace('Anax', ANAX_INSTALL_PATH . 'src')
           ->addNamespace('', ANAX_APP_PATH . 'src')
           ->register();
});



/**
 * Including composer autoloader if available.
 *
 * @link https://getcomposer.org/doc/01-basic-usage.md#autoloading
 */
if (is_file(ANAX_INSTALL_PATH . 'vendor/autoload.php')) {
    include ANAX_INSTALL_PATH . 'vendor/autoload.php';
}
