@props( [
	'class' => '',
	'title' => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}

	$classes = [ 'link-detail-cards__title' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<div class="link-detail-cards__title-container">
	<h4 @class( $classes )>
		<x-escape :content="$title" />
	</h4>
	<span class="link-detail-cards__chevron">
		<x-svg name="chevron-left" />
	</span>
</div>
