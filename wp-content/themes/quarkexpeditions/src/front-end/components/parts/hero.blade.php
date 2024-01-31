@props( [
	'image_id'  => 0,
	'title'     => '',
	'sub_title' => '',
] )

@php
	if ( empty( $image_id ) ) {
		return;
	}
@endphp

<x-hero>
	<x-hero.image :image_id="$image_id" />
	<x-hero.content>
		@if ( ! empty( $title ) )
			<x-hero.title :title="$title" />
		@endif
		@if ( ! empty( $sub_title ) )
			<x-hero.sub-title :title="$sub_title" />
		@endif
	</x-hero.content>
	<x-hero.form
		form_id="inquiry-form"
		title="Almost there!"
		subtitle="We just need a bit more info to help personalize your itinerary."
		salesforce_object="Webform_Landing_Page__c"
	/>
</x-hero>
