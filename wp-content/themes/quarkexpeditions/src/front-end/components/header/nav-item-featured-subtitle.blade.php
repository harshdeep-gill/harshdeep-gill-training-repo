@props( [
	'subtitle' => '',
] )

@php
	if ( empty( $subtitle ) ) {
		return;
	}
@endphp

<div class="header__nav-item-featured-subtitle">
	<x-escape :content="$subtitle" />
</div>
