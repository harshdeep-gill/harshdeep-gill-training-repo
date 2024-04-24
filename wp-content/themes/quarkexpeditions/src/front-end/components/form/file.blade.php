@props( [
	'id'                 => '',
	'label'              => '',
	'name'               => '',
	'allowed_file_types' => [],
] )

@php
	if ( ! empty( $id ) ) {
		$dom_id = $id;
	} else {
		$dom_id = quark_generate_unique_dom_id();
	}

	if ( ! empty( $allowed_file_types ) ) {
		$allowed_file_types = implode( ',', $allowed_file_types );
	} else {
		$allowed_file_types = '';
	}
@endphp

<quark-file-input class="quark-file-input">
	<label
		for="{{ $dom_id }}"
		{{ $attributes->filter( fn ( $value, $key ) => ! in_array( $key, [ 'label', 'id', 'name', 'allow' ] ) ) }}
	>
		<div role="button" class="quark-file-input__btn btn btn--outline btn--size-big">
			{{ $label }}
		</div>

		<div class="quark-file-input__preview">
			<div class="quark-file-input__mime-type">
				<p></p>
			</div>
			<div class="quark-file-input__preview-body">
				<div class="quark-file-input__file-info">
					<p class="quark-file-input__file-name"></p>
					<p class="quark-file-input__file-size"></p>
				</div>

				<button class="quark-file-input__discard">
					<x-svg name="cross" />
				</button>
			</div>
		</div>
	</label>

	<input
		type="file"
		name="{{ $name }}"
		id="{{ $dom_id }}"
		accept="{{ $allowed_file_types }}"
	>
</quark-file-input>
