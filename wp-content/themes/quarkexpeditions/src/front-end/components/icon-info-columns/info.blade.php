@php
	if( empty( $slot ) ) {
		return;
	}
@endphp

<div class="icon-info-columns__info">
	<span class="icon-info-columns__info-icon">
		<x-svg name="info" />
	</span>
	<div class="icon-info-columns__tooltip">
		<x-content :content="$slot"/>
	</div>
</div>
