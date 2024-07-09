@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<tp-accordion-content class="accordion__content">
	<div class="accordion__content-inner">
		{!! $slot !!}
	</div>
</tp-accordion-content>
