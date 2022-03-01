<?php defined('SYSPATH') or die('No direct script access.');

// -- Environment setup --------------------------------------------------------

// Load the core Kohana class
require SYSPATH.'classes/Kohana/Core'.EXT;

if (is_file(APPPATH.'classes/Kohana'.EXT))
{
    // Application extends the core
    require APPPATH.'classes/Kohana'.EXT;
}
else
{
    // Load empty core extension
    require SYSPATH.'classes/Kohana'.EXT;
}

/**
 * Autoload composer libraries
 *
 */
require_once APPPATH . 'vendor/autoload' . EXT;

/**
 * Set the default time zone.
 *
 * @link http://kohanaframework.org/guide/using.configuration
 * @link http://www.php.net/manual/timezones
 */
date_default_timezone_set('Europe/Moscow');

/**
 * Set the default locale.
 *
 * @link http://kohanaframework.org/guide/using.configuration
 * @link http://www.php.net/manual/function.setlocale
 */
setlocale(LC_ALL, 'en_US.utf-8');

/**
 * Enable the Kohana auto-loader.
 *
 * @link http://kohanaframework.org/guide/using.autoloading
 * @link http://www.php.net/manual/function.spl-autoload-register
 */
spl_autoload_register(array('Kohana', 'auto_load'));

/**
 * Optionally, you can enable a compatibility auto-loader for use with
 * older modules that have not been updated for PSR-0.
 *
 * It is recommended to not enable this unless absolutely necessary.
 */
//spl_autoload_register(array('Kohana', 'auto_load_lowercase'));

/**
 * Enable the Kohana auto-loader for unserialization.
 *
 * @link http://www.php.net/manual/function.spl-autoload-call
 * @link http://www.php.net/manual/var.configuration#unserialize-callback-func
 */
ini_set('unserialize_callback_func', 'spl_autoload_call');

/**
 * Disable cache blade view
 */
if(Kohana::DEVELOPMENT === Kohana::$environment){
    foreach(glob(APPPATH.'cache/blade/*') as $view){
        unlink($view);
    }
}

/**
 * Set the mb_substitute_character to "none"
 *
 * @link http://www.php.net/manual/function.mb-substitute-character.php
 */
mb_substitute_character('none');

// -- Configuration and initialization -----------------------------------------

/**
 * Set the default language
 */
I18n::lang('en-us');

if (isset($_SERVER['SERVER_PROTOCOL'])) {
    // Replace the default protocol.
    HTTP::$protocol = $_SERVER['SERVER_PROTOCOL'];
}

/**
 * Set Kohana::$environment if a 'KOHANA_ENV' environment variable has been supplied.
 *
 * Note: If you supply an invalid environment name, a PHP warning will be thrown
 * saying "Couldn't find constant Kohana::<INVALID_ENV_NAME>"
 */
if ('cli' == PHP_SAPI && ! isset($_SERVER['KOHANA_ENV'])) {
    $KOHANA_ENV = shell_exec('. /etc/environment; echo -n $KOHANA_ENV');
    if ($KOHANA_ENV) {
        $_SERVER['KOHANA_ENV'] = $KOHANA_ENV;
    }
} unset($KOHANA_ENV);

if (isset($_SERVER['KOHANA_ENV'])) {
    Kohana::$environment = constant('Kohana::'.strtoupper($_SERVER['KOHANA_ENV']));
}

/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - integer  cache_life  lifetime, in seconds, of items cached              60
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 * - boolean  expose      set the X-Powered-By header                        FALSE
 */
Kohana::init([
    'base_url'   => '/',
    'index_file' => FALSE,
    'profile'    => Kohana::DEVELOPMENT === Kohana::$environment,
]);

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Kohana::$log->attach(new Log_File(APPPATH.'logs'));

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Config_File);

Session::$default = Kohana::$config->load('auth.session_type');
Cookie::$salt = Kohana::$config->load('session.cookie.salt');

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules([
    'auth'       => MODPATH.'auth',       // Basic authentication
    // 'cache'      => MODPATH.'cache',      // Caching with multiple backends
    // 'codebench'  => MODPATH.'codebench',  // Benchmarking tool
    'database'   => MODPATH.'database',   // Database access
    // 'image'      => MODPATH.'image',      // Image manipulation
    'minion'     => MODPATH.'minion',     // CLI Tasks
    'orm'        => MODPATH.'orm',        // Object Relationship Mapping
    'pagination' => MODPATH.'pagination',
    // 'unittest'   => MODPATH.'unittest',   // Unit testing
    // 'userguide'  => MODPATH.'userguide',  // User guide and API documentation
    'migrations' => MODPATH.'migrations',
    'purifier'   => MODPATH.'purifier',
    'blade' => APPPATH.'vendor/pridemon/kohana-blade',
]);

// Helper function from Laravel
function dd()
{
    die(call_user_func_array(['Debug', 'vars'], func_get_args()));
}

// External routes
require_once APPPATH . 'routes' . EXT;
