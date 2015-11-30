<?php

	//again, change this namespace:
	namespace ChefFilterColumn;
	
	use ChefSections\Columns\DefaultColumn;
	use ChefSections\Wrappers\SectionsBuilder;
	use Cuisine\Wrappers\Field;
	use Cuisine\Utilities\Url;
	use Cuisine\Wrappers\Script;
	
	
	class Column extends DefaultColumn{
	
		/**
		 * The type of column
		 * 
		 * @var String
		 */
		public $type = 'filter';
	
	
		/*=============================================================*/
		/**             Template                                       */
		/*=============================================================*/
	
	
		/**
		 * Start the column template
		 * 
		 * @return string ( html, echoed )
		 */
		public function beforeTemplate(){
	
			//runs before Assets/template.php is presented
		
		}
	
	
	
		/**
		 * Add javascripts to the footer, before the template
		 * and close the div wrapper
		 * 
		 * @return string ( html, echoed )
		 */
		public function afterTemplate(){
	
			//runs after Assets/template.php is presented
			$url = Url::plugin( 'chef-filter-column', true ).'Assets/js/';			
			Script::register( 'filter', $url.'filter', true );
	
		}
	
	
		/*=============================================================*/
		/**             Backend                                        */
		/*=============================================================*/
	
		
	
		/**
		 * Create the preview for this column
		 * 
		 * @return string (html,echoed)
		 */
		public function buildPreview(){
	
			echo '<strong>'.$this->getField( 'title' ).'</strong>';
	
		}
	
	
		/**
		 * Build the contents of the lightbox for this column
		 * 
		 * @return string ( html, echoed )
		 */
		public function buildLightbox(){
	
			//get all fields for this column
			$fields = $this->getFields();
				
			echo '<div class="main-content">';
			
				foreach( $fields as $field ){
	
					$field->render();
	
					//if a field has a JS-template, we need to render it:
					if( method_exists( $field, 'renderTemplate' ) ){
						echo $field->renderTemplate();
					}
	
				}
	
			echo '</div>';
			echo '<div class="side-content">';
				
				//optional: side fields
	
				$this->saveButton();
	
			echo '</div>';
		}
	
	
		/**
		 * Get the fields for this column
		 * 
		 * @return [type] [description]
		 */
		private function getFields(){

			$columns = $this->getCollectionColumns();
			$taxonomies = $this->getTaxonomies();
			$sorton = array( 'date' => 'Datum', 'name' => 'Alfabetisch' );

			$fields = array(

				Field::text( 
					'title', 				//id
					'Filter titel',			//label
					array(
						'defaultValue'	=> $this->getField( 'title' ),
					)
				),

				Field::select(
					'column_id',
					'Filter de column',
					$columns,
					array(
						'defaultValue' => $this->getField('column_id')
					)
				),

				Field::select(
					'taxonomy',
					'Filter op',
					$taxonomies,
					array(
						'defaultValue' => $this->getField( 'taxonomy' )
					)

				),

				Field::select(
					'sort_on',
					'Sorteer op',
					$sorton,
					array(
						'defaultValue'	=> $this->getField( 'sort_on', 'name' )
					)
				)
				
			);
	
			return $fields;
	
		}


		/*=============================================================*/
		/**             Getters & Setters                              */
		/*=============================================================*/


		/**
		 * Get all available terms in this taxonomy, for use in template
		 * 
		 * @return array of Term objects
		 */
		public function getTerms(){

			return get_terms( $this->getField( 'taxonomy' ), 
						array( 
							'hide_empty' 		=> apply_filters( 'chef_filter_hide_empty', true ),
							'orderby'			=> $this->getField( 'sort_on', 'name' )
						) 
			);

		}


		/**
		 * Returns an array of id => names of active taxonomies
		 * 
		 * @return array
		 */
		private function getTaxonomies(){

			$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );
			$array = array();
			$notAllowed = array();


			foreach( $taxonomies as $key => $tax ){

				$array[ $key ] = $tax->labels->name;

			}

			return $array;

		}


		/**
		 * Returns an array of id => names of collection columns.
		 * 
		 * @return array
		 */
		private function getCollectionColumns(){

			$sections = SectionsBuilder::getSections();
			$columns = array();
			$allowed = apply_filters( 'chef_filter_column_types', array( 'collection' ) ); 

			foreach( $sections as $section ){

				if( !empty( $section->columns ) ){
					foreach( $section->columns as $column ){

						if( in_array( $column->type, $allowed ) ){

							$columns[ $column->fullId ] = $section->title.' - '.$column->id;

						}

					}
				}
			}

			return $columns;

		}

	
	}
	