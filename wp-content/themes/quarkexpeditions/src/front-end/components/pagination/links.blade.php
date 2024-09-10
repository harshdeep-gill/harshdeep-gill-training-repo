@php
	if ( empty( $slot ) ) {
	    return;
	}
@endphp

<div class="pagination__container">
	{!! $slot !!}
</div>
