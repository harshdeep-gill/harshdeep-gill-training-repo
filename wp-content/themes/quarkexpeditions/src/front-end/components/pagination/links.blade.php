@php
	if ( empty( $slot ) ) {
	    return;
	}
@endphp

<div class="pagination__container">
	<a href="#" class="pagination__first-page">{{ __( 'First', 'qrk' ) }}</a>

	{!! $slot !!}

	<a href="#" class="pagination__last-page">{{ __( 'Last', 'qrk' ) }}</a>
</div>
