@props( [
	'options' => [],
] )

@php
	if ( empty( $options ) ) {
		return;
	}
@endphp

<ul class="departure-cards__options-list">
	@foreach ( $options as $index => $option )
		<li class="departure-cards__option">
			<x-escape :content="$option" />
		</li>
	@endforeach

	<li class="departure-cards__option departure-cards__options-count-wrap">
		<span>&hellip; +<span class="departure-cards__options-count"></span> {{ __( 'more', 'qrk' ) }}</span>
		<x-tooltip icon="info">
			<ul class="departure-cards__tooltip-options-list">
				@foreach ( $options as $option )
					<li class="departure-cards__tooltip-option">
						<x-escape :content="$option" />
					</li>
				@endforeach
			</ul>
		</x-tooltip>
	</li>
</ul>
