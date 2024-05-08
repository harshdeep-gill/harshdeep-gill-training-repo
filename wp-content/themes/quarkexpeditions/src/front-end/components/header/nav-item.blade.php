@props( [
	'title'  => '',
	'icon'   => '',
	'url'    => '',
	'target' => '',
] )

<li class="header__nav-item">
	<quark-header-nav-menu-link>
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
	</quark-header-nav-menu-link>

	@if ( empty( $url ) )
		{!! $slot !!}
	@endif
</li>
