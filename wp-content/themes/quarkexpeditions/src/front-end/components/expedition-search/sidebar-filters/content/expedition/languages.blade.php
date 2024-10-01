@props( [
	'languages' => [],
] )

@php
	if ( empty( $languages ) || ! is_array( $languages ) ) {
		return;
	}
@endphp

<x-accordion.item>
	<x-accordion.item-handle title="{{ __( 'Languages', 'qrk' ) }}" />
	<x-accordion.item-content>
	<quark-expedition-search-filter-languages>
		<x-form.field-group>
			@foreach ( $languages as $language )
				@if ( empty( $language['label'] ) || empty( $language['value'] ) || ! isset( $language['count'] ) )
					@continue
				@endif

				<x-expedition-search.sidebar-filters.checkbox name="languages" :label="$language['label']" :value="$language['value']" :count="$language['count']" />
			@endforeach
		</x-form.field-group>
	</quark-expedition-search-filter-languages>
	</x-accordion.item-content>
</x-accordion.item>
