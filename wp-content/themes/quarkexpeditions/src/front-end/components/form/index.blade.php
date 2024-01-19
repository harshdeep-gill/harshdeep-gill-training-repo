@props( [
	'form_name'  => '',
	'name'       => '',
	'class'      => '',
	'style'      => '',
	'method'     => 'post',
	'action'     => '',
	'page_title' => '',
	'permalink'  => '',
] )

<quark-form
	class="form {{ $class }}"
	data-action="{{ $action }}"
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
			{{ $slot }}
		</form>
	</tp-form>
</quark-form>
