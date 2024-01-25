@php
	if( empty( $slot ) ) {
		return;
	}
@endphp

<div class="icon-info">
	<x-svg name="info"/>
	<div class="icon-info__tooltip">
		<x-content :content="$slot">
	</div>
</div>
