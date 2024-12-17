@props( [
	'label'       => '',
	'placeholder' => __( 'Select', 'qrk' ),
	'type'        => '',
] )

@php
	$classes = [ 'search-filters-bar__modal-open-button' ];

	// Add type class, if set.
	if ( ! empty( $type ) ) {
		$types = [ 'destinations', 'departures' ];

		if ( in_array( $type, $types, true ) ) {
			$classes[] = sprintf( 'search-filters-bar__modal-open-button-%s', $type );
		}
	}
@endphp

<div type="button" @class( $classes )>
	<div class="search-filters-bar__modal-open-button-content">
		<div class="search-filters-bar__modal-open-button-label body-small"><x-content :content="$label" /></div>
		<div class="search-filters-bar__modal-open-button-placeholder"><x-content :content="$placeholder" /></div>
	</div>
	<div class="search-filters-bar__modal-open-button-icon">
		<x-svg name="chevron-left" />
	</div>
</div>
