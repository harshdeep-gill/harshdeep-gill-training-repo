@props( [
	'title'  => '',
	'icon'   => '',
	'url'    => '',
	'target' => '',
] )

<li class="header__nav-item">
	<x-maybe-link
		href="{{ $url }}"
		fallback_tag="div"
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
</li>
