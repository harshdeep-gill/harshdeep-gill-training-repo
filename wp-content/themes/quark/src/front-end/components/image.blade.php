@php
	$attributes = ! empty( $attributes ) ? $attributes->getAttributes() : [];
	if ( empty( $attributes['image_id'] ) ) {
		return;
	}

	$attributes['args']['id']   = $attributes['image_id'];
	$attributes['args']['atts'] = [];
	foreach ( $attributes as $key => $value ) {
		if ( in_array( $key, [ 'image_id', 'args' ], true ) ) {
			continue;
		}

		$attributes['args']['atts'][ $key ] = $value;
	}

	quark_dynamic_image( $attributes['args'] );
@endphp
