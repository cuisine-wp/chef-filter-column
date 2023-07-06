<?php

	//again, change this namespace:
	namespace ChefFilterColumn;

	use Cuisine\Utilities\Url;
	use Cuisine\Wrappers\Field;
	use Cuisine\Wrappers\Script;
	use Cuisine\Utilities\Session;
	use ChefSections\Columns\DefaultColumn;
	use ChefSections\Collections\SectionCollection;


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
			
            $title = $this->getField( 'title' );

			if( $title && is_array( $title ) ){
				$title = $title['text'];
			}
			
		}


		/**
		 * Build the contents of the lightbox for this column
		 *
		 * @return string ( html, echoed )
		 */
		public function buildLightbox(){

			//get all fields for this column
			$fields = $this->getFields();
			$sideFields = $this->getSideFields();

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

				foreach( $sideFields as $sideField ){

					$sideField->render();

					if( method_exists( $field, 'renderTemplate' ) ){
						echo $field->renderTemplate();
					}

				}


				$this->saveButton();

			echo '</div>';
		}


		/**
		 * Get the fields for this column
		 *
		 * @return [type] [description]
		 */
		public function getFields(){

			$columns = $this->getCollectionColumns();
			$taxonomies = $this->getTaxonomies();

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

				)

			);

			$fields = apply_filters( 'chef_filter_column_main_fields', $fields, $this );
			return $fields;

		}


		/**
		 * Get the fields in the sidebar for this column
		 *
		 * @return [type] [description]
		 */
		private function getSideFields(){

			$sorton = array( 'date' => 'Datum', 'name' => 'Alfabetisch' );

			$fields = array(
				Field::select(
					'sort_on',
					'Sorteer op',
					$sorton,
					array(
						'defaultValue'	=> $this->getField( 'sort_on', 'name' )
					)
				),

				Field::checkbox(
					'show_search',
					'Laat zoekveld zien',
					array(
						'defaultValue' => $this->getField( 'show_search', 'false' )
					)
				)
			);


			$fields = apply_filters( 'chef_filter_column_side_fields', $fields, $this );
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

			$args = array(
				'hide_empty' 		=> apply_filters( 'chef_filter_hide_empty', false ),
				'orderby'			=> $this->getField( 'sort_on', 'name' )
			);

			$args = apply_filters( 'chef_filter_column_term_query', $args );
			$terms = get_terms( $this->getField( 'taxonomy' ), $args );

			$terms = $this->makeHierarchical( $terms );
			$terms = apply_filters( 'chef_filter_column_terms', $terms );
			return $terms;
		}

		/**
		 * Add the hierarchy in the array we push to the template
		 *
		 * @return array
		 */
		public function makeHierarchical( $terms )
		{

			$_response = array();
			if( !empty( $terms ) ){

				foreach( $terms as $term ){

					if( $term->parent == 0 ){

						$term->children = array();

						foreach( $terms as $child ){

							if( $child->parent == $term->term_id ){
								$term->children[] = $child;
							}
						}

						$_response[] = $term;
					}
				}
			}

			return $_response;
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

			$sections = ( new SectionCollection( Session::postId() ) );
			
			$columns = array();
			$allowed = apply_filters( 'chef_filter_column_types', array( 'collection' ) );

			foreach( $sections->all() as $section ){

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
