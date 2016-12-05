<?php
/**
 * Plugin Name: Chef Filter Column
 * Plugin URI: http://chefduweb.nl/plugins/chef-filter-column
 * Description: Filter collections based on taxonomy
 * Version: 1.0.0
 * Author: Luc Princen
 * Author URI: http://www.chefduweb.nl/
 * License: GPLv2
 *
 * @package ChefSections
 * @category ColumnTypes
 * @author Chef du Web
 */

//Chaning the namespaces is the most important part,
//after that the bus pretty much drives itself.
namespace ChefFilterColumn;

use Cuisine\Wrappers\Script;
use Cuisine\Wrappers\Sass;
use Cuisine\Utilities\Url;


class ColumnIgniter{

	/**
	 * Static bootstrapped ChefFilterColumn\ColumnIgniter instance.
	 *
	 * @var \ChefFilterColumn\ColumnIgniter
	 */
	public static $instance = null;


	/**
	 * Init admin events & vars
	 */
	function __construct(){

		//register column:
		$this->register();

		//load the right files
		$this->load();

	}


	/**
	 * Register this column-type with Chef Sections
	 *
	 * @return void
	 */
	private function register(){


		add_filter( 'chef_sections_column_types', function( $types ){

			$base = Url::path( 'plugin', 'chef-filter-column', true );

			//change the $types[ key ] and the name value:
			$types['filter'] = array(
						'name'		=> 'Filter kolom',
						'class'		=> 'ChefFilterColumn\Column',
						'template'	=> $base.'Assets/template.php'
			);

			return $types;

		});

	}

	/**
	 * Load all includes for this plugin
	 *
	 * @return void
	 */
	private function load(){

		include( 'Classes/Column.php' );
		include( 'Classes/EventListeners.php' );

	}


	/*=============================================================*/
	/**             Getters & Setters                              */
	/*=============================================================*/


	/**
	 * Init the \ChefFilterColumn\ColumnIgniter Class
	 *
	 * @return \ChefFilterColumn\ColumnIgniter
	 */
	public static function getInstance(){

	    return static::$instance = new static();

	}


}


add_action('chef_sections_loaded', function(){

	\ChefFilterColumn\ColumnIgniter::getInstance();

});
