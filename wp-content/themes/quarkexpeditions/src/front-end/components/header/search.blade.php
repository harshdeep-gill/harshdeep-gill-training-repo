@props( [
	'icon'     => 'search',
	'class'    => '',
	'modal_id' => 'search-filters-bar-modal',
] )

@php
	// Return if the modal id is empty.
	if ( empty( $modal_id ) ) {
		return;
	}

	// Classes.
	$classes = [ 'header__nav-item header__search-item' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<li @class( $classes )>
	<x-modal.modal-open modal_id="{{ $modal_id }}">
		<button class="header__nav-item-link" type="button">
			<x-svg name="{{ $icon }}" />
		</button>
	</x-modal.modal-open>

	<x-parts.search-filters-bar />
</li>
