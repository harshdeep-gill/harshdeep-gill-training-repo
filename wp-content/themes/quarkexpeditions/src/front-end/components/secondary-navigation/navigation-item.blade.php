@props( [
	'href' => ''
] )

@php
	if ( empty( $slot ) || empty( $href ) ) {
		return;
	}
@endphp

<li class="secondary-navigation__navigation-item">
	<a
		href="{{ $href }}"
		class="secondary-navigation__navigation-item-link"
	>
		{!! $slot !!}
	</a>
</li>
