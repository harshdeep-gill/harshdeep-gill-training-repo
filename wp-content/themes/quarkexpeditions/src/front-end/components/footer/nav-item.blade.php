@props( [
	'title'  => '',
	'url'    => '',
	'target' => '',
] )

<li class="footer__nav-item">
	<x-maybe-link
		href="{{ $url }}"
		class="footer__nav-item-link"
		target="{{ $target }}"
	>
		<x-escape :content="$title" />
	</x-maybe-link>
</li>
