@props( [
	'form_id'   => '',
	'class'     => '',
	'countries' => [],
	'states'    => [],
	'links'     => [],
] )

@php
	$classes = [ 'form-newsletter' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp


<x-section @class( $classes )>
	<quark-form-newsletter>
		<x-form
			salesforce_object="WebForm_Newsletter_Sign_up__c"
			id="{{ $form_id }}"
		>
			<div class="form-newsletter__content">
				<h1 class="form-newsletter__title"> {{ __( 'Subscribe To Our Newsletter', 'qrk' ) }}</h1>
				<div class="form-newsletter__description">
					<h3 class="form-newsletter__subtitle"> {{ __( 'Sign up to receive a regular dose of polar inspiration in your inbox!', 'qrk' ) }}</h3>
					<p>
						{{
							__( 'You\'ll get insider details on new Arctic and Antarctic itineraries
							and product launches, the heads-up on special discounts, advanced
							notice on expert-led webinars, plus early announcements of new
							curated videos. In short, you\'ll receive the latest polar
							inspiration before anybody else!' , 'qrk' )
						}}
					</p>
				</div>
				<div class="form-newsletter__fields">
					<x-form.row>
						<x-form.field :validation="[ 'required', 'no-empty-spaces' ]">
							<x-form.input
								type="text"
								label="{{ __('First Name', 'qrk') }}"
								placeholder="{{ __('Enter First Name', 'qrk') }}"
								name="fields[First_Name__c]"
							/>
						</x-form.field>
						<x-form.field :validation="[ 'required', 'no-empty-spaces' ]">
							<x-form.input
								type="text"
								label="{{ __('Last Name', 'qrk') }}"
								placeholder="{{ __('Enter Last Name', 'qrk') }}"
								name="fields[Last_Name__c]"
							/>
						</x-form.field>
					</x-form.row>
					<x-form.row>
						<x-form.field :validation="[ 'required', 'email' ]">
							<x-form.input
								type="email"
								label="{{ __('Email Address', 'qrk') }}"
								placeholder="{{ __('Enter Email Address', 'qrk') }}"
								name="fields[Email__c]"
							/>
						</x-form.field>
						<x-country-selector :countries="$countries" :states="$states" />
					</x-form.row>
					<x-form.row class="form-newsletter__agent-check">
						<x-form.field-group>
							<x-form.checkbox name="fields[Travel_Agent__c]" label="{{ __('I am a Travel Agent', 'qrk') }}" />
						</x-form.field-group>
					</x-form.row>
					<p class="form-newsletter__privacy">
						{{ __( 'We respect your privacy. You may unsubscribe from our communications at any time. Please refer to our privacy policy for full detail.', 'qrk' ) }}
					</p>
				</div>
				<x-form.buttons>
					<x-form.submit size="big">{{ __( 'Submit', 'qrk' ) }}</x-form.submit>
				</x-form.buttons>
			</div>
			<div class="form-newsletter__success">
				<h1 class="form-newsletter__success-title"> {{ __( 'You\'ve Been Subscribed', 'qrk' ) }} </h1>
				<div class="form-newsletter__success-info">
					<p>{{ __( 'Thanks for signing up for our monthly newsletter.', 'qrk' ) }}
					<p>{{ __( 'We also invite you to join the conversation, see photos, videos and read some comments from our past travelers or share your favorite travel moment with us.', 'qrk' ) }}</p>
					@if ( ! empty( $links ) )
						<p class="form-newsletter__social-cta">{{ __( 'Join the Quark Expeditions Community:', 'qrk' ) }}</p>
						<x-social-links :links="$links" class="form-newsletter__social-links" />
					@endif
				</div>
			</div>
		</x-form>
	</quark-form-newsletter>
</x-section>

