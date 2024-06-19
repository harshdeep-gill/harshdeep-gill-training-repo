@props( [
	'column' => [
		'type' => '',
	],
] )

@if ( ! empty( $column['type'] ) && 'column' === $column['type'] )
	<x-footer.column :url="$column['attributes']['url']">
		@foreach ($column['content'] as $column_inner_content)
			@switch ( $column_inner_content['type'] )
				@case ( 'title' )
					<x-footer.column-title :title="$column_inner_content['attributes']['title']" />
				@break

				@case ( 'icon' )
					<x-footer.icon :name="$column_inner_content['attributes']['name']" />
					@break

				@case ( 'social-links' )
					<x-footer.social-links :social_links="$column_inner_content['attributes']['social_links']" />
					@break

				@case ( 'payment-options' )
					<x-footer.payment-options>
						@foreach ( $column_inner_content['content'] as $payment_option )
							<x-footer.payment-option :type="$payment_option['attributes']['type']" />
						@endforeach
					</x-footer.payment-options>
				@break

				@case ( 'logo' )
					<x-footer.logo />
				@break

				@default
					{!! $column_inner_content['content'] !!}

			@endswitch
		@endforeach
	</x-footer.column>
@endif
