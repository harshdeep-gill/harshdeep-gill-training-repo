@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="options-button__options">
	<x-button size="big" class="options-button__options-button">
		<x-svg name="arrow-down" />
	</x-button>

	<div class="options-button__options-dropdown-wrap">
		<ul class="options-button__options-dropdown">
			{!! $slot !!}
		</ul>
		</div>
</div>
