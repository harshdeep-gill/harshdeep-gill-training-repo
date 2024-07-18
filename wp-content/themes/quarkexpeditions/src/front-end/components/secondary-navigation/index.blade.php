@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<x-section :no_border="true" class="secondary-navigation" :seamless="true" :full_width="true">
	<div class="secondary-navigation__wrap wrap">
		{!! $slot !!}
	</div>
</x-section>
