@props( [
	'destinations' => [],
] )

@php
	if ( empty( $destinations ) || ! is_array( $destinations ) ) {
		return;
	}
@endphp

<quark-expedition-search-filter-destinations>
	<x-accordion.item>
		<x-accordion.item-handle>
			<x-escape :content=" __( 'Destinations', 'qrk' ) " /> <span class="expedition-search__filter-count"></span>
		</x-accordion-item.handle>
		<x-accordion.item-content>
			<x-form.field-group>
				@foreach ( $destinations as $destination )
					@if ( empty( $destination['label'] ) || empty( $destination['value'] ) || ! isset( $destination['count'] ) )
						@continue
					@endif

					@php
						$image_args = [
							'size' => [
								'height' => 72,
								'width'  => 72,
							],
							'transform' => [
								'crop'    => 'fill',
								'gravity' => 'auto',
								'quality' => 90,
							]
						];

						$image_url = '';

						if ( ! empty( $destination['image_id'] ) ) {
							$image_url = quark_dynamic_image_url( array_merge( [ 'id' => $destination[ 'image_id' ] ], $image_args ) );
						}
					@endphp

					@if ( empty( $destination['children'] ) || ! is_array( $destination['children'] ) )
						<x-expedition-search.sidebar-filters.checkbox
							name="destinations"
							:label="$destination['label']"
							:value="$destination['value']"
							:count="$destination['count']"
							:image_url="$image_url"
						/>
					@else
						<div class="expedition-search__sidebar-filters-with-children">
							<x-expedition-search.sidebar-filters.checkbox
								name="destinations"
								:label="$destination['label']"
								:value="$destination['value']"
								:count="$destination['count']"
								:image_url="$image_url"
							/>
							<x-form.field-group class="expedition-search__sidebar-filters-children">
								@foreach ( $destination['children'] as $child )
									@if ( empty( $child['label'] ) || empty( $child['value'] ) || ! isset( $child['count'] ) || empty( $child['parent_id'] ) )
										@continue
									@endif
									@php
										$child_image_url = '';

										if ( ! empty( $child['image_id'] ) ) {
											$child_image_url = quark_dynamic_image_url( array_merge( [ 'id' => $child['image_id'] ], $image_args ) );
										}
									@endphp
									<x-expedition-search.sidebar-filters.checkbox
										name="destinations"
										:label="$child['label']"
										:value="$child['value']"
										:count="$child['count']"
										:parent="$child['parent_id']"
										:image_url="$child_image_url"
									/>

								@endforeach
							</x-form.field-group>
						</div>
					@endif
				@endforeach
			</x-form.field-group>
		</x-accordion.item-content>
	</x-accordion.item>
</quark-expedition-search-filter-destinations>
