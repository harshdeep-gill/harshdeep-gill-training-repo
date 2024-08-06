@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<article class="departure-cards__card">
	<quark-departure-card>
		{!! $slot !!}
	</quark-departure-card>
</article>
