@props( [
	'type'    => '',
	'message' => ''
] )

@php
	$classes = [ 'toast' ];
	$icon = '';


	$valid_toast_type =  match( $type ) {
		'error' => 'error',
		default => 'success'
	};

	$classes[] = 'toast--' . $valid_toast_type;

	$icon = $valid_toast_type;
@endphp

<quark-toast @class( $classes )>
	<x-svg name="{{ $icon }}"/>
	<p><x-escape :content="$message"/></p>
	<button class="toast-dismiss">
		<x-svg name="cross-white"/>
	</button>
</quark-toast>
