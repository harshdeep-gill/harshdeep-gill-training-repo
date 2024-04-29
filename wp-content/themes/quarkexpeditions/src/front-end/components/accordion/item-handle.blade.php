@props( [
	'title' => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}
@endphp

<x-accordion.item-handle-container>
	<x-accordion.item-handle-button>
		<h5 class="accordion__handle-btn-text body-text-large">
			<x-escape :content="$title" />
		</h5>
		<x-accordion.item-handle-icon />
	</x-accordion.item-handle-button>
</x-accordion.item-handle-container>
