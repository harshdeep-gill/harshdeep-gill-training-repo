@props( [
	'content' => '',
] )

{!! wp_kses_post( $content ) !!}
