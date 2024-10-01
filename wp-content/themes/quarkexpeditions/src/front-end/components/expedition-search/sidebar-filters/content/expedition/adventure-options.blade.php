@props( [
	'adventure_options' => [],
] )

@php
	if ( empty( $adventure_options ) || ! is_array( $adventure_options ) ) {
		return;
	}
@endphp

<x-accordion.item>
	<x-accordion.item-handle title="{{ __( 'Adventure Options', 'qrk' ) }}" />
	<x-accordion.item-content>
	<quark-expedition-search-filter-adventure-options>
		<x-form.field-group>
			@foreach ( $adventure_options as $adventure_option )
				@if ( empty( $adventure_option['label'] ) || empty( $adventure_option['value'] ) || ! isset( $adventure_option['count'] ) )
					@continue
				@endif

				<x-expedition-search.sidebar-filters.checkbox name="adventure-options" :label="$adventure_option['label']" :value="$adventure_option['value']" :count="$adventure_option['count']" />
			@endforeach
		</x-form.field-group>
	</quark-expedition-search-filter-adventure-options>
	</x-accordion.item-content>
</x-accordion.item>
