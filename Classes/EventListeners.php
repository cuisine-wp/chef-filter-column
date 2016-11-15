<?php

	namespace ChefFilterColumn;


	class EventListeners {

		/**
		 * Static bootstrapped instance.
		 *
		 * @var \ChefFilterColumn\EventListeners
		 */
		public static $instance = null;


		/**
		 * Init the Assets Class
		 *
		 * @return \ChefFilterColumn\EventListeners
		 */
		public static function getInstance(){

		    return static::$instance = new static();

		}

		/**
		 * Init admin events & vars
		 */
		public function __construct(){

			$this->listen();

		}

		/**
		 * Listen to Chef Sections collection query;
		 *
		 * @return void
		 */
		private function listen(){


			add_filter( 'chef_sections_collection_query', function( $query, $column ){

				if( isset( $_POST['filter_on'] ) && isset( $_POST['filter_val'] ) ){

					$taxQuery = array(
						'taxonomy'  => $_POST['filter_on'],
						'field'		=> 'term_id',
						'terms'		=> $_POST['filter_val']
					);

					$query[ 'posts_per_page' ] = '-1';
					$query[ 'paged' ] = '1';

					if( !isset( $query['tax_query'] ) )
						$query['tax_query'] = array();

					array_push( $query['tax_query'], $taxQuery );

				}


				if( isset( $_POST['search'] ) )
					$query['s'] = $_POST['search'];


				return $query;
			}, 100, 2 );

		}

	}

	\ChefFilterColumn\EventListeners::getInstance();
