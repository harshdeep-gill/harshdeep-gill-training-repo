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
				<x-header.nav-item :title="$item['title'] ?? ''" url="{{ $item['url'] ?? '' }}" target="{{ $item['target'] ?? '' }}">
					@if ( ! empty( $item['contents'] ) )
						<x-header.nav-item-dropdown-content>
							@foreach ( $item['contents'] as $content_item )
								@if ( 'featured-section' === $content_item['type'] )
									<x-header.nav-item-dropdown-content-column>
										<x-header.nav-item-featured :image_id="$content_item['image_id'] ?? 0">
											<x-header.nav-item-featured-title :title="$content_item['title'] ?? ''" />
											<x-header.nav-item-featured-subtitle :subtitle="$content_item['subtitle'] ?? ''" />
											@if ( ! empty( $content_item['cta_text'] ) )
												<x-button href="{{ $content_item['url'] ?? '' }}" size="big" target="{{ $content_item['target'] ?? '' }}">
													{!! $content_item['cta_text'] !!}
												</x-button>
											@endif
										</x-header.nav-item-featured>
									</x-header.nav-item-dropdown-content-column>
								@elseif ( 'slot' === $content_item['type'] && ! empty( $content_item['slot'] ) )
									<x-header.nav-item-dropdown-content-column>
										{!! $content_item['slot'] ?? '' !!}
									</x-header.nav-item-dropdown-content-column>
								@endif
							@endforeach
						</x-header.nav-item-dropdown-content>
					@endif
				</x-header.nav-item>
			@endforeach
			@if ( $primary_nav['has_more'] )
				@php
					// Get the last item in the primary nav.
					$last_array_key = array_key_last( $primary_nav['items'] );
					$last_menu_item = $primary_nav['items'][$last_array_key];
				@endphp
				{{-- More menu item. --}}
				<x-header.nav-item :title="__( 'More', 'qrk' )" class="header__more-menu-item">
					@if ( ! empty( $item['contents'] ) )
						<x-header.nav-item-dropdown-content>
							@foreach ( $item['contents'] as $content_item )
								@if ( 'featured-section' === $content_item['type'] )
									<x-header.nav-item-dropdown-content-column>
										<x-header.nav-item-featured :image_id="$content_item['image_id'] ?? 0">
											<x-header.nav-item-featured-title :title="$content_item['title'] ?? ''" />
											<x-header.nav-item-featured-subtitle :subtitle="$content_item['subtitle'] ?? ''" />
											@if ( ! empty( $content_item['cta_text'] ) )
												<x-button href="{{ $content_item['url'] ?? '' }}" size="big" target="{{ $content_item['target'] ?? '' }}">
													{!! $content_item['cta_text'] !!}
												</x-button>
											@endif
										</x-header.nav-item-featured>
									</x-header.nav-item-dropdown-content-column>
								@elseif ( 'menu-items' === $content_item['type'] && ! empty( $content_item['items'][0]['menu_list_items'] ) )
									<x-header.nav-item-dropdown-content-column>
										<x-two-columns>
												<x-two-columns.column>
													<x-menu-list :title="__( 'More', 'qrk' )">
														@foreach ( $content_item['items'][0]['menu_list_items']['items'] as $menu_item )
															<x-menu-list.item
																:title="$menu_item['title'] ?? ''"
																:url="$menu_item['url'] ?? ''"
																:target="$menu_item['target'] ?? ''"
															/>
														@endforeach

														{{-- More items from Secondary Nav. --}}
														@if ( ! empty( $secondary_nav['items'] ) )
															@foreach ( $secondary_nav['items'] as $nav_item )
																@if ( ! empty( $nav_item['url'] ) )
																	<x-menu-list.item
																		:title="$nav_item['title'] ?? ''"
																		:url="$nav_item['url'] ?? ''"
																		:target="$nav_item['target'] ?? ''"
																	/>
																@endif
															@endforeach
														@endif
													</x-menu-list>
												</x-two-columns.column>
										</x-two-columns>
									</x-header.nav-item-dropdown-content-column>
								@elseif ( 'slot' === $content_item['type'] && ! empty( $content_item['slot'] ) )
									<x-header.nav-item-dropdown-content-column>
										{!! $content_item['slot'] !!}
									</x-header.nav-item-dropdown-content-column>
								@endif
							@endforeach
						</x-header.nav-item-dropdown-content>
					@endif
				</x-header.nav-item>
			@endif
		</x-header.primary-nav>
	@endif
	@if ( ! empty( $secondary_nav['items'] ) )
		<x-header.secondary-nav>
			@foreach ( $secondary_nav['items'] as $item )
				@if ( 'search-item' === $item['type'] )
					<x-header.search />
					@continue
				@endif

				<x-header.nav-item
					:title="$item['title'] ?? ''"
					:icon="$item['icon'] ?? ''"
					url="{{ $item['url'] ?? '' }}"
					target="{{ $item['target'] ?? '' }}"
				/>
			@endforeach
		</x-header.secondary-nav>
	@endif
	@if ( ! empty( $cta_buttons ) )
		<x-header.cta-buttons>
			@foreach ( $cta_buttons as $cta_button )
				@if ( 'contact' === $cta_button['type'] )
					<x-button
						class="{{ $cta_button['class'] ?? '' }}"
						href="{{ $cta_button['url'] ?? '' }}"
						size="big"
						:color="$cta_button['color'] ?? ''"
						:appearance="$cta_button['appearance'] ?? ''"
						icon="phone"
					>
						{!! $cta_button['text'] !!}
					</x-button>

				@elseif( 'raq' === $cta_button['type'] )
					<x-button
						class="{{ $cta_button['class'] ?? '' }}"
						href="{{ $cta_button['url'] ?? '' }}"
						target="{{ $cta_button['target'] ?? '' }}"
						size="big"
						:color="$cta_button['color'] ?? ''"
						:appearance="$cta_button['appearance'] ?? ''"
					>
						{!! $cta_button['text'] !!}
					</x-button>
				@endif
			@endforeach
		</x-header.cta-buttons>
	@endif

	<x-drawer.drawer-open drawer_id="header-drawer" class="color-context--dark header__hamburger-menu">
		<x-button type="button" size="big" color="black">
			<x-svg name="hamburger" />
		</x-button>
	</x-drawer.drawer-open>

	<x-drawer id="header-drawer" class="header__drawer" close_on_desktop="true">
		<x-drawer.header>
			<x-header.site-logo />
		</x-drawer.header>

		<x-drawer.body>
			@if ( ! empty( $primary_nav['items'] ) )
			<x-accordion>
				@foreach ( $primary_nav['items'] as $item )
					<x-accordion.item>
						<x-accordion.item-handle :title="$item['title'] ?? ''" />
						@if ( ! empty( $item['contents'] ) )
							<x-accordion.item-content>
								{{-- Loop through all content items in menu dropdown --}}
								@foreach ( $item['contents'] as $content_item )
									{{-- Featured Section. --}}
									@if ( 'featured-section' === $content_item['type'] )
										<x-header.nav-item-featured
											:image_id="$content_item['image_id'] ?? 0"
											url="{{ $content_item['url'] ?? '' }}"
											target="{{ $content_item['target'] ?? '' }}"
											size="small"
										>
											<x-header.nav-item-featured-title :title="$content_item['title'] ?? ''" />
										</x-header.nav-item-featured>
									@elseif ( 'menu-items' === $content_item['type'] )
										{{-- Loop thorugh menu list items for each column --}}
										@foreach ( $content_item['items'] as $column_items )
											{{-- Menu List Items. --}}
											@if ( ! empty( $column_items['menu_list_items'] ) )
												<x-menu-list :title="$column_items['menu_list_items']['title'] ?? ''">
													@foreach ( $column_items['menu_list_items']['items'] as $menu_list_item)
														{{-- Loop through inner menu list items --}}
														<x-menu-list.item
															:title="$menu_list_item['title'] ?? ''"
															:url="$menu_list_item['url'] ?? ''"
															:target="$menu_list_item['target'] ?? ''"
														/>
													@endforeach
												</x-menu-list>
											@endif

											{{-- Thumbnail Card Link Items --}}
											@if ( ! empty( $column_items['thumbnail_card_items'] ) )
												<x-menu-list>
													@foreach ( $column_items['thumbnail_card_items'] as $thumbnail_item )
														<x-menu-list.item
															:title="$thumbnail_item['title'] ?? ''"
															:url="$thumbnail_item['url'] ?? ''"
															:target="$thumbnail_item['target'] ?? ''"
															class="header__drawer-thumbnail-card-link"
														/>
													@endforeach
												</x-menu-list>
											@endif
										@endforeach
									@endif
								@endforeach
							</x-accordion.item-content>
						@endif
					</x-accordion.item>
					@endforeach
				</x-accordion>
			@endif

			{{-- Secondary Nav Quick Links. --}}
			@if ( ! empty( $secondary_nav['items'] ) )
				<ul class="header__drawer-quick-links">
					@foreach ( $secondary_nav['items'] as $nav_item )
						@if ( ! empty( $nav_item['url'] ) )
							<li>
								<a href="{{ $nav_item['url'] ?? '' }}" target="{{ $nav_item['target'] ?? '' }}">{!! $nav_item['title'] ?? '' !!}</a>
							</li>
						@endif
					@endforeach

					{{-- CTA Buttons. --}}
					@if ( ! empty( $cta_buttons ) )
						@foreach ( $cta_buttons as $button )
							@if ( 'contact' === $button['type'] )
								<li>
									<a href="{{ $button['url'] ?? '' }}" target="{{ $button['target'] ?? '' }}">
										{!! $button['drawer_text'] ?? '' !!}
										{!! $button['text'] ?? '' !!}
									</a>
								</li>
							@endif
						@endforeach
					@endif
				</ul>
			@endif

			@if ( ! empty( $cta_buttons ) )
				@foreach ( $cta_buttons as $button )
					@if ( 'raq' === $button['type'] )
						<x-button
							:href="$button['url'] ?? ''"
							:target="$button['target'] ?? ''"
							class="header__drawer-request-quote-btn"
							size="big"
						>
							{!! $button['text'] ?? '' !!}
						</x-button>
					@endif
				@endforeach
			@endif
		</x-drawer.body>
	</x-drawer>
</x-header>
