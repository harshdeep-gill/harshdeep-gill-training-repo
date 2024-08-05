@props( [
	'title' => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}
@endphp

<div class="link-detail-cards__title">
	<h3 class="h4">
		<x-escape :content="$title" />
	</h3>
	<span class="link-detail-cards__chevron">
		<x-svg name="chevron-left" />
	</span>
</div>
