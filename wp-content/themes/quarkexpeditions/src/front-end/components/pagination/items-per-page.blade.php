@props( [
	'title'            => __( 'Items per page', 'qrk' ),
	'pagination_steps' => [],
	'select_first'     => true,
] )

@php
	if ( empty( $slot ) ) {
	    return;
	}

	$default_pagination_steps = [ 10, 20, 30 ];

	if ( empty( $pagination_steps ) || ! is_array( $pagination_steps ) ) {
		$pagination_steps = $default_pagination_steps;
	}
@endphp

<div class="pagination__items-per-page">
	<span class="pagination__items-per-page-text"><x-escape :content="$title" /></span>
	<x-form.select class="pagination__items-per-page-select">
		@foreach ( $pagination_steps as $step )
			@if ( $loop->first && $select_first )
				<x-form.option
					value="{{ $step }}"
					label="{{ $step }}"
					selected="yes"
				>
					<x-escape :content="$step" />
				</x-form.option>
			@else
				<x-form.option
					value="{{ $step }}"
					label="{{ $step }}"
				>
					<x-escape :content="$step" />
				</x-form.option>
			@endif
		@endforeach
	</x-form.select>
</div>
