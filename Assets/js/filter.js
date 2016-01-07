define([
	
	//define dependencies at the top:
	'jquery'


], function( $ ){

	$(document).ready(function(){
		
		if( $('.filter-items').length > 0 ){

			var _tax = $( '.filter-items').data('tax');
			var _col = $( '.filter-items').data('col');


			$( '.filter' ).on( 'click tap', function( e ){

				e.preventDefault();

				if( $( this ).hasClass( 'active' ) === false ){
 
					$( '.filter' ).removeClass( 'active' );
					$( this ).addClass( 'active' );

					var _collection = $( '#collection_'+_col );


					//remove autoload functionality
					if( $( this ).data( 'filter') === 'all' ){
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

					if( $( this ).data( 'filter' ) != 'all' ){
						data['filter_on'] = _tax;
						data['filter_val'] = $( this ).data( 'filter' );
					}


					$.post( Cuisine.ajax, data, function( response ){
						_collection.html( response );
					});

				}


			})

		}
 

	});
	
});