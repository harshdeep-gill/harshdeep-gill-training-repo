@props( [
	'form_name'  => '',
	'name'       => '',
	'class'      => '',
	'style'      => '',
	'method'     => 'post',
	'action'     => quark_get_template_data( 'action', '' ),
	'page_title' => '',
	'permalink'  => '',
] )

<quark-form class="form {{ $class }}" data-action="{{ $action }}">
	<tp-form prevent-submit="yes">
		<form
			action="#"
			data-action="{{ $action }}"
			data-name="{{ $name }}"
			method="{{ $method }}"
			novalidate
			@if( ! empty( $style ) )
				style="{{ $style }}"
			@endif
		>
			{{ $slot }}
		</form>
	</tp-form>
</quark-form>
