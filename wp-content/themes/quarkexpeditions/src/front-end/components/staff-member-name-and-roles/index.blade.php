@props( [
	'name'  => '',
	'roles' => '',
] )

@php
	if ( empty( $name ) ) {
		return;
	}
@endphp

<div class="staff-member-name-and-roles">
	<h1 class="staff-member-name-and-roles__title">
		{{ $name }}
	</h1>

	@if ( ! empty( $roles ) )
		<p class="staff-member-name-and-roles__roles h5">
			{{ $roles ?? '' }}
		</p>
	@endif
</div>
