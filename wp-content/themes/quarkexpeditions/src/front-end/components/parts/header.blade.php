@props( [
	'primary_nav'   => [],
	'secondary_nav' => [],
	'cta_buttons'   => [],
] )

@php
	if ( empty( $primary_nav ) ) {
		return;
	}
@endphp

<x-header>
	<x-header.site-logo />

	@if ( ! empty( $primary_nav['items'] ) )
		<x-header.primary-nav>
			@foreach ( $primary_nav['items'] as $item )
				<x-header.nav-item :title="$item['title'] ?? ''" icon="" url="">
					@if ( ! empty( $item['contents'] ) )
						<x-header.nav-item-dropdown-content>
							@foreach ( $item['contents'] as $content_item )
								@if ( 'featured-section' === $content_item['type'] )
									<x-header.nav-item-dropdown-content-column>
										<x-header.nav-item-featured image_id="34">
											<x-header.nav-item-featured-title title="Explore Polar Regions" />
											<x-header.nav-item-featured-subtitle subtitle="Incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis." />
											<x-button href="#" size="big">Explore Polar Regions</x-button>
										</x-header.nav-item-featured>
									</x-header.nav-item-dropdown-content-column>
								@elseif ( 'slot' === $content_item['type'] )
									<x-header.nav-item-dropdown-content-column>
										{!! $content_item['slot'] !!}
									</x-header.nav-item-dropdown-content-column>
								@endif
							@endforeach
						</x-header.nav-item-dropdown-content>
					@endif
				</x-header.nav-item>
			@endforeach
		</x-header.primary-nav>
	@endif
</x-header>