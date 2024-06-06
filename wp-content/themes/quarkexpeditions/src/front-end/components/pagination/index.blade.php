@props( [
	'current_page'        => '',
	'total_pages'         => '',
	'show_items_per_page' => true,
] )

@php
	if ( empty( $slot ) ) {
	    return;
	}
@endphp

<x-section no_border="true">
	<div class="pagination">
		@if ( true === $show_items_per_page )
			<div class="pagination__items-per-page">
				<span class="pagination__items-per-page-text">{{ __( 'Items per page', 'qrk' ) }}</span>

				<x-form>
					<x-form.field>
						<select class="pagination__items-per-page-select">
							<option value="10" label="10">10</option>
							<option value="20" label="20">20</option>
							<option value="30" label="30">30</option>
						</select>
					</x-form.field>
				</x-form>
			</div>
		@endif

		@if ( ! empty( $current_page ) || ! empty( $total_pages ) )
			<div class="pagination__total-pages">
				{{ __( 'Page', 'qrk' ) }} {{ $current_page }} {{ __( 'of', 'qrk' ) }} {{ $total_pages }}
			</div>
		@endif

		<div class="pagination__container">
			<a href="#" class="pagination__first-page">{{ __( 'First', 'qrk' ) }}</a>

			{!! $slot !!}

			<a href="#" class="pagination__last-page">{{ __( 'Last', 'qrk' ) }}</a>
		</div>
	</div>
</x-section>
