@props( [
	'title'  => '',
	'icon'   => '',
	'url'    => '',
	'class'  => '',
	'target' => '',
] )

<li @class( [ 'header__nav-item', $class ] )>
	@if ( empty( $url ) )
		<quark-header-nav-menu-dropdown>
	@endif

		<x-maybe-link
			href="{{ $url }}"
			fallback_tag="button"
			class="header__nav-item-link"
			target="{{ $target }}"
		>
			@if ( ! empty( $icon ) )
				<x-svg name="{{ $icon }}" />
			@endif

			@if ( empty( $icon ) )
				<x-escape :content="$title" />
			@endif
		</x-maybe-link>

		@if ( empty( $url ) )
			{!! $slot !!}
		@endif

	@if ( empty( $url ) )
		</quark-header-nav-menu-dropdown>
	@endif
</li>
