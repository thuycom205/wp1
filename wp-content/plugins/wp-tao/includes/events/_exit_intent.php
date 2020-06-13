<?php

/*
 * Fire exit intent event
 */

add_action( 'wp_footer', 'wptao_exit_intent_event' );

function wptao_exit_intent_event() {
	?>
	<script>
		setTimeout( function () {
			var interval = 5,
					canFire = true;
			
			document.addEventListener( "mouseleave", function ( e ) {
				
				if(!canFire){
					return false;
				}
					
				var x = setTimeout( function () {
						canFire = true;
				}, 5000 );

				if ( e.clientY < 0 )
				{	
					canFire = false;
					wptaoEvent( 'exit_intent' );
				}


			}, 100);

		}, false );
	</script>

	<?php

}
