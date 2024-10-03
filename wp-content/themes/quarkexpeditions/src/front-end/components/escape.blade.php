@props( [
	'content' => '',
	'type'    => 'html',
] )

@php
	if ( ! isset( $content ) || '' === $content || empty( $type ) ) {
	    return;
	}

	switch ( $type ) {
		case 'textarea':
			$content = esc_textarea( $content );
			break;
		default:
			$content = wp_strip_all_tags( $content );
			$content = esc_html( $content );
			break;
	}
@endphp

{!! $content !!}
