define([
	
	//define dependencies at the top:
	'jquery'


], function( $ ){

	$(document).ready(function(){
		
		if( $('.filter-items').length > 0 ){

			$( '.filter' ).on( 'click tap', function( e ){

				e.preventDefault();

				if( $( this ).hasClass( 'active' ) === false ){
 
					$( '.filter' ).removeClass( 'active' );
					$( this ).addClass( 'active' );

					filterCollectionItems();

				}
			});

			$( '#submit-search' ).on( 'click tap', function( e ){

				e.preventDefault();
				filterCollectionItems();
			
			});

			$( '#search-query' ).on( 'keyup', function( e ){

				if( e.which == 13 ){
					e.preventDefault();
					filterCollectionItems();
				}
			
			});

		}
 

	});


	function filterCollectionItems(){

		var _tax = $( '.filter-items').data('tax');
		var _col = $( '.filter-items').data('col');

		var _collection = $( '#collection_'+_col );
		var _activeFilter = $( '.filter.active' );
		
		_collection.addClass( 'loading' );

		//remove autoload functionality
		if( _activeFilter.data( 'filter') === 'all' ){
			_collection.removeClass( 'hold-autoload' );
			_collection.data( 'page', 1 );
		}else{
			_collection.addClass( 'hold-autoload' );

		}


		var data = {

			action: 'autoload',
			page: 1,
			section: _collection.data('section_id'),
			column: _collection.data('id'),
			post_id: _collection.data('post'),
			message: '',
			wrap: true

		}

		if( _activeFilter.data( 'filter' ) != 'all' ){
			data['filter_on'] = _tax;
			data['filter_val'] = _activeFilter.data( 'filter' );
		}

		if( $( '.search #search-query' ).val() != undefined && $( '.search #search-query' ).val() != '' )
			data['search'] = $( '.search #search-query' ).val();


		$.post( Cuisine.ajax, data, function( response ){
			
			_collection.removeClass( 'loading' );
			
			if( response === 'message' ){

				_collection.html( _collection.data('msg') );

			}else{
				_collection.html( response );
			}

		});
	}
	
});