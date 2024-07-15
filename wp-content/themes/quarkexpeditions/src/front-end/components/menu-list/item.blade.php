@props( [
	'title'  => '',
	'url'    => '',
	'target' => '',
	'class'  => '',
] )

@php
	$class = $class ? $class : 'menu-list__item-link';
@endphp

<li class="menu-list__item">
	<x-maybe-link
		href="{{ $url }}"
		fallback_tag="div"
		class="{{ $class }}"
		target="{{ $target }}"
	>
		<x-escape :content="$title" />
	</x-maybe-link>
</li>
