@props( [
	'aop_drawer_id' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<article class="departure-cards__card">
	<quark-departure-card aop-drawer-id="{{ $aop_drawer_id }}">
		{!! $slot !!}
	</quark-departure-card>
</article>
