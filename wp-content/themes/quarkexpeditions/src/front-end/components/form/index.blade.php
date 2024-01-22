@props( [
	'form_name'  => '',
	'name'       => '',
	'class'      => '',
	'style'      => '',
	'method'     => 'post',
	'action'     => quark_get_template_data( 'leads_api_endpoint', '#' ),
	'page_title' => '',
	'permalink'  => '',
	'recaptcha'  => true,
] )

<quark-form
	class="form {{ $class }}"
	@if( ! empty( $style ) )
		style="{{ $style }}"
	@endif
>
	<tp-form prevent-submit="yes">
		<form
			action="#"
			data-action="{{ $action }}"
			data-name="{{ $name }}"
			method="{{ $method }}"
			novalidate
		>
			@if ( true === $recaptcha )
				<input type="hidden" name="recaptcha_token" value="" />
			@endif

			{{ $slot }}
		</form>
	</tp-form>
</quark-form>
