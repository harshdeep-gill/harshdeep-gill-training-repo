@php
	if( empty( $slot ) ) {
		return;
	}
@endphp

<div class="icon-info">
	<div class="icon-info__indicator"></div>
	<div class="icon-info__tooltip">
		<div class="icon-info__tooltip__wrapper">
			{!! $slot !!}
		</div>
	</div>
</div>
