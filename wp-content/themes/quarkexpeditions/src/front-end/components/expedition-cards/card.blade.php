@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<article class="expedition-cards__card">
	<quark-expedition-card>
		{!! $slot !!}
	</quark-expedition-card>
</article>
