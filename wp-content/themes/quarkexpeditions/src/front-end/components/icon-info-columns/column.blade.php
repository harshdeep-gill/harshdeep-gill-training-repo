@php
	if( empty( $slot ) ) {
		return;
	}
@endphp

<div class="icon-info-columns__column" tabindex="-1">
	{!! $slot !!}
</div>
<div class="icon-info-columns__separator"></div>
