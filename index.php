<?php

/**
 * /$$$$$$$                                          /$$
 * | $$__  $$                                        | $$
 * | $$  \ $$  /$$$$$$   /$$$$$$$  /$$$$$$$  /$$$$$$ | $$  /$$$$$$   /$$$$$$
 * | $$  | $$ /$$__  $$ /$$_____/ /$$_____/ /$$__  $$| $$ |____  $$ /$$__  $$
 * | $$  | $$| $$$$$$$$|  $$$$$$ | $$      | $$  \ $$| $$  /$$$$$$$| $$  \__/
 * | $$  | $$| $$_____/ \____  $$| $$      | $$  | $$| $$ /$$__  $$| $$
 * | $$$$$$$/|  $$$$$$$ /$$$$$$$/|  $$$$$$$|  $$$$$$/| $$|  $$$$$$$| $$
 * |_______/  \_______/|_______/  \_______/ \______/ |__/ \_______/|__/ .fr
 */

/**
 * This is the main file of the project, it is responsible for starting the application.
 * Please do not change this file.
 *
 * @author Descolar Team <development@descolar.fr>
 */

use Descolar\App;


/**
 * The autoloader of the project
 */
require __DIR__ . '/vendor/autoload.php';

/**
 * Define constants
 */
const DIR_ROOT = __DIR__;

/**
 * Load Adapters
 */
App::loadAdapters();


App::run();


