@props( [
	'options' => [],
] )

@php
	if ( empty( $options ) ) {
		return;
	}

	$options_string = implode( ', ', $options ?? '' )
@endphp

<div class="departure-cards__options">
	{{ $options_string }}

	<x-tooltip icon="info">
		<ul class="departure-cards__options-list">
			@foreach ( $options as $option )
				<li class="departure-cards__option">
					<x-escape :content="$option" />
				</li>
			@endforeach
		</ul>
	</x-tooltip>
</div>
