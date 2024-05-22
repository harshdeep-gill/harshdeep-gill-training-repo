@props( [
	'title'            => '',
	'mobile_accordion' => true,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="footer__column">

	@if ( true === $mobile_accordion )
		<quark-footer-accordion class="footer__accordion">
	@endif

	@if ( ! empty( $title ) )
		<h4 class="footer__column-title">
			<x-escape :content="$title" />
		</h4>
	@endif

	{!! $slot !!}

	@if ( true === $mobile_accordion )
		</quark-footer-accordion>
	@endif
</div>
