@props( [
	'jump_to_navigation' => false,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<quark-secondary-navigation>
	<nav class="secondary-navigation__navigation">
		<ul class="secondary-navigation__navigation-items">
			{!! $slot !!}

			@if ( ! empty( $jump_to_navigation ) && true === boolval( $jump_to_navigation ) )
				<li class="secondary-navigation__navigation-item secondary-navigation__navigation-item--dropdown">
					<x-button class="secondary-navigation__navigation-button">
						<span>{{ __( 'Jump to', 'qrk' ) }}</span>
						<x-svg name="arrow-down" />
					</x-button>
					<ul class="secondary-navigation__navigation-dropdown"></ul>
				</li>
			@endif
		</ul>
	</nav>
</quark-secondary-navigation>
