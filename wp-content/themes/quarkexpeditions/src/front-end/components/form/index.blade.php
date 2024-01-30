@props( [
	'form_name'         => '',
	'name'              => '',
	'id'                => '',
	'class'             => '',
	'style'             => '',
	'method'            => 'post',
	'action'            => quark_get_template_data( 'leads_api_endpoint', '#' ),
	'salesforce_object' => '',
	'recaptcha'         => true,
	'permalink'         => quark_get_template_data( 'current_url', '#' ),
] )

<quark-form
	class="form {{ $class }}"
	@if ( ! empty( $style ) )
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
			@if ( ! empty( $id ) )
				id="{{ $id }}"
			@endif
		>
			<input type="hidden" name="salesforce_object" value="{{ $salesforce_object }}">
			<input type="hidden" name="fields[Webform_URL__c]" value="{{ $permalink }}">

			@if ( true === $recaptcha )
				<input type="hidden" name="recaptcha_token" value="" />
			@endif

			{{ $slot }}
		</form>
	</tp-form>
</quark-form>
