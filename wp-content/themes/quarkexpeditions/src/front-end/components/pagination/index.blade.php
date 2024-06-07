@php
	if ( empty( $slot ) ) {
	    return;
	}
@endphp

<x-section no_border="true">
	<div class="pagination">
		{!! $slot !!}
	</div>
</x-section>
