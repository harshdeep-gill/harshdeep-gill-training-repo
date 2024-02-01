@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<x-section class="lp-footer" :full_width="true" :seamless="true">
	<div class="lp-footer__wrap">
		{!! $slot !!}
	</div>
</x-section>
