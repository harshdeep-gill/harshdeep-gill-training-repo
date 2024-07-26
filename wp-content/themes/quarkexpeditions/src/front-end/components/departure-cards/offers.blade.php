@props( [
	'title' => '',
	'items' => [],
] )

@php
	if ( empty( $items ) ) {
		return;
	}
@endphp

<div class="departure-cards__offers">
	<p class="departure-cards__offers-title"><x-escape :content="$title" /></p>

	<ul class="departure-cards__offers-list">
		@foreach ( $items as $item )
			<li class="departure-cards__offer">
				<x-escape :content="$item" />
			</li>
		@endforeach
	</ul>
</div>
