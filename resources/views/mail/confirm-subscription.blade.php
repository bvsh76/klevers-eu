<x-mail::message>
# {{ __('site.confirm_mail_title') }}

{{ __('site.confirm_mail_text') }}

<x-mail::button :url="$confirmUrl">
{{ __('site.confirm_mail_button') }}
</x-mail::button>

{{ __('site.confirm_mail_ignore') }}

[{{ __('site.unsubscribe_link') }}]({{ $unsubscribeUrl }})
</x-mail::message>