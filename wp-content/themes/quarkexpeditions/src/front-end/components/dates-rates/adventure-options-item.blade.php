@props( [
	'name' => '',
	'icon'  => '',
] )

@php
	if ( empty( $icon ) || empty( $name ) ) {
		return;
	}
@endphp

<li class="dates-rates__adventure-options-item">
	<span class="dates-rates__adventure-options-item-icon">
		<x-svg name="{{ $icon }}" />
	</span>

	<span class="dates-rates__adventure-options-item-name">
		<x-escape :content="$name" />
	</span>

	{!! $slot !!}
</li>
