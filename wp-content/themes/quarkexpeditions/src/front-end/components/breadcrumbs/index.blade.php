@props( [
	'breadcrumbs' => [],
	'appearance'  => 'light',
] )

@php
	if ( empty( $breadcrumbs ) ) {
		return;
	}

	$classes = [ 'breadcrumbs' ];

	if ( 'light' === $appearance ) {
		$classes[] = 'breadcrumbs--light';
	}

	if ( 'dark' === $appearance ) {
		$classes[] = 'breadcrumbs--dark';
	}
@endphp

<div @class( $classes )>
	@foreach ( $breadcrumbs as $breadcrumb )
		<div class="breadcrumbs__breadcrumb">
			<span class="breadcrumbs__breadcrumb-separator">
				<x-svg name="chevron-left" />
			</span>
			<a
				href="{!! esc_url( $breadcrumb['url'] ) !!}"
				class="breadcrumbs__breadcrumb-title"
			>
				<x-escape :content="$breadcrumb['title']" />
			</a>
		</div>
	@endforeach
</div>
