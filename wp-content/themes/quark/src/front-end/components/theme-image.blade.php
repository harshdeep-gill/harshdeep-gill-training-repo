@php
	$attributes = ! empty( $attributes ) ? $attributes->getAttributes() : [];
	if ( empty( $attributes['src'] ) ) {
		return;
	}

	if ( empty( $attributes['loading'] ) ) {
		$attributes['loading'] = 'lazy';
	}

	$path = $attributes['src'];
	$args = [];

	foreach ( $attributes as $key => $value ) {
		if ( in_array( $key, [ 'src' ], true ) ) {
			continue;
		}

		$args[ $key ] = $value;
	}
	quark_theme_image( $path, $args );
@endphp
