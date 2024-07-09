@props( [
	'title' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<quark-footer-accordion class="footer__accordion">
	@if ( ! empty( $title ) )
		<p class="footer__navigation-title" role="button">
			<x-escape :content="$title" />
		</p>
	@endif

	<ul class="footer__navigation">
		{!! $slot !!}
	</ul>
</quark-footer-accordion>
