@props( [
	'form_id'    => '',
] )


@if ( ! empty( $form_id ) )
	<footer class="modal__footer">
		<x-form.buttons>
			<x-form.submit form="{{ $form_id }}">Request a Quote</x-form.submit>
		</x-form.buttons>
	</footer>
@endif
