@props( [
	'id'     => '',
	'title'  => '',
	'active' => false,
] )

@php
	if ( empty( $title ) || empty( $id ) ) {
		return;
	}
@endphp

<tp-tabs-nav-item class="tabs__nav-item" {!! $active ? "active='yes'" : '' !!}">
	<a class="tabs__nav-link body-text-ui-small" href="#{{ $id }}">
		<x-escape :content="$title" />
	</a>
</tp-tabs-nav-item>
