@props( [
	'type' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'dates-rates__item-table-heading' ];

	if ( ! empty( $type ) && in_array( $type, [ 'premium', 'standard' ] ) ) {
		$classes[] = 'dates-rates__item-table-heading--' . $type;
	}
@endphp

<th @class( $classes )>
	{!! $slot !!}
</th>
