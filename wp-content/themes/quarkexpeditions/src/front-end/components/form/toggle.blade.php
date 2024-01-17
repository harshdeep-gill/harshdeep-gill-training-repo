@props( [
	'label' => '',
] )

<x-form.label class="toggle">
	<input type="checkbox" {{ $attributes->filter( fn ( $value, $key ) => $key !== 'label' ) }}>
	<span class="toggle__slider"></span>
	<x-escape :content="$label"/>
</x-form.label>
