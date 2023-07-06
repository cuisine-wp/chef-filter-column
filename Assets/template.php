<?php
	
	$terms = $column->getTerms();



	echo '<div class="column filter-column">';

	if( $column->getField( 'show_search', 'false' ) == 'true' ){
		echo '<span class="search">';
			echo '<input type="text" name="s" id="search-query" placeholder="ZOEKEN">';
			echo '<button class="submit" type="submit" id="submit-search"></button>';
		echo '</span>';
	}

	
	if( !empty( $terms ) ){
		
		if( $column->getField( 'title' ) )
			echo '<h2>'.$column->getField( 'title' ).'</h2>';
	
		
		echo '<div class="filter-items" data-tax="'.$column->getField( 'taxonomy' ).'" data-col="'.$column->getField( 'column_id' ).'">';

			//all btn
			echo '<span class="filter active" data-filter="all">';
				echo 'Alles';
			echo '</span>';		
		
		foreach( $terms as $term ){
			
			echo '<span class="filter" data-filter="'.$term->term_id.'">';

				echo $term->name;

			echo '</span>';
		
		}

		echo '</div>';
	
	}
	

	echo '</div>';