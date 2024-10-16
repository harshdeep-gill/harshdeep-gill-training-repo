@props( [
	'title' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="dates-rates__adventure-options-column">
	<p class="dates-rates__adventure-options-column-title overline">
		<x-escape :content="$title" />
	</p>

	<ul class="dates-rates__adventure-options-column-list">
		{!! $slot !!}
	</ul>
</div>
