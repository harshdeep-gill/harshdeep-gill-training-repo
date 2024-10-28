@props( [
	'id' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div
	class="expedition-cards__more-details"
	@if ( ! empty( $id ) )
		id="{{ $id }}"
	@endif
>
	{!! $slot !!}
</div>
