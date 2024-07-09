@props( [
	'image_id' => 0,
	'title'    => '',
	'duration' => 0,
] )

<x-post-author-info>
	<x-post-author-info.image :image_id="$image_id" />
	<x-post-author-info.info>
		<x-post-author-info.name :title="$title" />
		<x-post-author-info.read-time :duration="$duration" />
	</x-post-author-info.info>
</x-post-author-info>
