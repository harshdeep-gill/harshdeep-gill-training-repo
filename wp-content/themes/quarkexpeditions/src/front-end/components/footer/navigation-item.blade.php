@props( [
	'title'  => '',
	'url'    => '',
	'target' => '',
] )

<li class="footer__navigation-item">
	<x-maybe-link
		href="{{ $url }}"
		class="footer__navigation-item-link"
		target="{{ $target }}"
	>
		<x-escape :content="$title" />
	</x-maybe-link>
</li>
