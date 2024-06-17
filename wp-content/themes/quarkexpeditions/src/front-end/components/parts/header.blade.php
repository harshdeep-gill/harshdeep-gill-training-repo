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
				<x-header.nav-item :title="$item['title'] ?? ''">
					@if ( ! empty( $item['contents'] ) )
						<x-header.nav-item-dropdown-content>
							@foreach ( $item['contents'] as $content_item )
								@if ( 'featured-section' === $content_item['type'] )
									<x-header.nav-item-dropdown-content-column>
										<x-header.nav-item-featured :image_id="$content_item['image_id'] ?? 0">
											<x-header.nav-item-featured-title :title="$content_item['title'] ?? ''" />
											<x-header.nav-item-featured-subtitle :subtitle="$content_item['subtitle'] ?? ''" />
											<x-button href="{{ $content_item['url'] ?? '' }}" size="big">
												{!! $content_item['cta_text'] ?? '' !!}
											</x-button>
										</x-header.nav-item-featured>
									</x-header.nav-item-dropdown-content-column>
								@elseif ( 'slot' === $content_item['type'] )
									<x-header.nav-item-dropdown-content-column>
										{!! $content_item['slot'] ?? '' !!}
									</x-header.nav-item-dropdown-content-column>
								@endif
							@endforeach
						</x-header.nav-item-dropdown-content>
					@endif
				</x-header.nav-item>
			@endforeach
		</x-header.primary-nav>
	@endif
	@if ( ! empty( $secondary_nav['items'] ) )
		<x-header.secondary-nav>
			@foreach ( $secondary_nav['items'] as $item )
				<x-header.nav-item
					:title="$item['title'] ?? ''"
					:icon="$item['icon'] ?? ''"
					url="{{ $item['url'] ?? '' }}"
				/>
			@endforeach
		</x-header.secondary-nav>
	@endif
	@if ( ! empty( $cta_buttons['slot'] ) )
		<x-header.cta-buttons>
			{!! $cta_buttons['slot'] !!}
		</x-header.cta-buttons>
	@endif

	<x-drawer.drawer-open drawer_id="header-drawer" class="color-context--dark header__hamburger-menu">
		<x-button type="button" size="big" color="black">
			<x-svg name="hamburger" />
		</x-button>
	</x-drawer.drawer-open>

	<x-drawer id="header-drawer" class="header__drawer">
		<x-drawer.header>
			<x-header.site-logo />
		</x-drawer.header>

		{{-- TODO: Update drawer body components. --}}
		<x-drawer.body>
			@if ( ! empty( $primary_nav['items'] ) )
			<x-accordion>
				@foreach ( $primary_nav['items'] as $item )
					<x-accordion.item>
						<x-accordion.item-handle :title="$item['title'] ?? ''" />
						@if ( ! empty( $item['contents'] ) )
							<x-accordion.item-content>
								@foreach ( $item['contents'] as $content_item )
									@if ( 'featured-section' === $content_item['type'] )
										<x-header.nav-item-featured :image_id="$content_item['image_id'] ?? 0">
											<x-header.nav-item-featured-title :title="$content_item['title'] ?? ''" />
											<x-header.nav-item-featured-subtitle :subtitle="$content_item['subtitle'] ?? ''" />
											<x-button href="{{ $content_item['url'] ?? '' }}" size="big">
												{!! $content_item['cta_text'] ?? '' !!}
											</x-button>
										</x-header.nav-item-featured>
									@elseif ( 'slot' === $content_item['type'] )
										<x-header.nav-item-dropdown-content-column>
											{!! $content_item['slot'] ?? '' !!}
										</x-header.nav-item-dropdown-content-column>
									@endif
								@endforeach
							</x-accordion.item-content>
						@endif
					</x-accordion.item>
					@endforeach
				</x-accordion>
			@endif
			<x-accordion title="Quark Expeditions takes you places no one else can!">
				<x-accordion.item>
					<x-accordion.item-handle title="Destinations" />
					<x-accordion.item-content>
						<x-header.nav-item-featured image_id="32" size="small">
							<x-header.nav-item-featured-title title="Explore Polar Regions" />
							<x-header.nav-item-featured-subtitle subtitle="Incididunt ut labore et dolore magna aliqua." />
							<x-button size="big">Explore Polar Regions</x-button>
						</x-header.nav-item-featured>

						<x-menu-list title="Antarctic Regions">
							<x-menu-list.item title="Antarctic Peninsula" url="#" />
							<x-menu-list.item title="Falkland Islands" url="#" />
							<x-menu-list.item title="Patagonia" url="#" />
							<x-menu-list.item title="South Georgia" url="#" />
							<x-menu-list.item title="Snow Hill Island" url="#" />
						</x-menu-list>

						<x-menu-list title="Arctic Regions">
							<x-menu-list.item title="Canadian High Arctic" url="#" />
							<x-menu-list.item title="Greenland" url="#" />
							<x-menu-list.item title="Svalbard" url="#" />
						</x-menu-list>
					</x-accordion.item-content>
				</x-accordion.item>
			</x-accordion>

			<ul class="header__drawer-quick-links">
				<li><a href="#">Dates & Rates</a></li>
				<li><a href="#">Travel Advisors</a></li>
				<li><a href="tel:+1-877-585-1235">Call Now to Book : +1 (866) 253-3145</a></li>
			</ul>

			<x-button class="header__drawer-request-quote-btn" size="big">Request a Quote</x-button>
		</x-drawer.body>
	</x-drawer>
</x-header>