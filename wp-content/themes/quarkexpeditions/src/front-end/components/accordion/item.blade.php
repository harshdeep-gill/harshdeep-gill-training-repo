@props( [
	'open' => false,
	'id'   => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<tp-accordion-item
	class="accordion__item"
	@if ( ! empty( $id ) )
		id="{{ $id }}"
	@endif
	@if ( true === $open )
		open-by-default="yes"
	@endif
>
	{!! $slot !!}
</tp-accordion-item>
