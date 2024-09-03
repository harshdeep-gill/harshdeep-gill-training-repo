@props( [
	'title' => '',
	'text'  => '',
] )

@php
	if ( empty( $text ) ) {
		return;
	}
@endphp

<div class="product-cards__price-content">
	@if ( ! empty( $title ) )
		<div class="product-cards__price-content-title">
			<x-escape :content="$title" />
		</div>
	@endif

	<div class="product-cards__price-content-text h4">
		<x-escape :content="$text" />
	</div>
</div>
