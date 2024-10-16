@props( [
	'title' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="dates-rates__item-table">
	<p class="dates-rates__item-table-title overline">
		<x-escape :content="$title" />
	</p>

	<quark-dates-rates-table class="travelopia-table">
		<table class="dates-rates__table">
			{!! $slot !!}
		</table>
	</quark-dates-rates-table>
</div>
