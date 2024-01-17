@props( [
	'label' => '',
] )

<div class="form-input-group">
	@if ( ! empty( $label ) )
		<p class="form-input-group__label">
			<x-content :content="$label"/>
		</p>
	@endif

	<div class="form-input-group__inputs">
		{{ $slot }}
	</div>
</div>
