@props( [
	'aop_drawer_id' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<article class="expedition-cards__card">
	<quark-expedition-card aop-drawer-id="{{ $aop_drawer_id }}">
		{!! $slot !!}
	</quark-expedition-card>
</article>
