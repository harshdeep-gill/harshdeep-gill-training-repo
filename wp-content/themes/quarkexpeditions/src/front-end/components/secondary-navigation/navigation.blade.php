@props( [
	'contents' => [],
] )

@php
	if ( empty( $contents ) ) {
		return;
	}
@endphp

<quark-secondary-navigation>
	<nav class="secondary-navigation__navigation">
		<ul class="secondary-navigation__navigation-items">
			@foreach ( $contents as $content_item )
				<li
					@class( [
						'secondary-navigation__navigation-item',
						'secondary-navigation__navigation-item--active' => $loop->first,
					] )
					data-anchor="#{{ $content_item['anchor'] }}"
				>
					<a class="secondary-navigation__navigation-item-link" href="#{{ $content_item['anchor'] }}">
						<x-escape :content="$content_item['title']"/>
					</a>
				</li>
			@endforeach

			<li class="secondary-navigation__navigation-item secondary-navigation__navigation-item--dropdown">
				<x-button class="secondary-navigation__navigation-button">
					<span>{{ __( 'Jump to', 'qrk' ) }}</span>
					<x-svg name="arrow-down" />
				</x-button>
				<ul class="secondary-navigation__navigation-dropdown"></ul>
			</li>
		</ul>
	</nav>
</quark-secondary-navigation>
