@props( [
	'title'  => '',
	'url'    => '',
	'target' => '',
] )

<li class="menu-list__item">
	<x-maybe-link
		href="{{ $url }}"
		fallback_tag="div"
		class="menu-list__item-link"
		target="{{ $target }}"
	>
		<x-escape :content="$title" />
	</x-maybe-link>
</li>
