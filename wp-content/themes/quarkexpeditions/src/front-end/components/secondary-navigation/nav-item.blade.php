@props( [
	'active' => false,
	'href'   => '',
	'target' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'secondary-navigation__navigation-item' ];

	if ( ! empty( $active ) && true === boolval( $active ) ) {
		$classes[] = 'secondary-navigation__navigation-item--active';
	}
@endphp

<li @class( $classes ) data-anchor="#{{ $href }}">
	<a
		class="secondary-navigation__navigation-item-link"
		href="#{{ $href }}"
		@if( ! empty( $target ) )
			target="{{ $target }}"
		@endif
	>
		<x-content :content="$slot" />
	</a>
</li>
