@props( [
	'title' => '',
] )

@php
	if ( empty( $title )  && empty( $slot )) {
		return;
	}
@endphp

<x-accordion.item-handle-container>
	<x-accordion.item-handle-button>
		<h3 class="h5 accordion__handle-btn-text body-text-large">
			@if ( ! empty( $title ) )
				<x-escape :content="$title" />
			@elseif ( ! empty( $slot ) )
				{!! $slot !!}
			@endif
		</h3>
		<x-accordion.item-handle-icon />
	</x-accordion.item-handle-button>
</x-accordion.item-handle-container>
