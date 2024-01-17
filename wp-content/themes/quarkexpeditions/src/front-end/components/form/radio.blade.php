@props( [
	'label' => '',
] )

<x-form.label class="radio">
	<input type="radio" {{ $attributes->filter( fn ( $value, $key ) => $key !== 'label' ) }}>
	<x-escape :content="$label"/>
</x-form.label>
