@props( [
	'title' => '',
	'url'   => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}
@endphp

<li class="expedition-details__starting-from-item">
	@if( ! empty( $url ) )
		<a href="{{ $url }}" class="expedition-details__starting-from-item-link">
			<x-escape :content="$title"/>
		</a>
	@else
		<x-escape :content="$title"/>
	@endif
</li>
