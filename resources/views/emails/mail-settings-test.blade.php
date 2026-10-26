<x-mail::message>
# {{ __('messages.mail_test_subject') }}

{{ __('messages.mail_test_body') }}

**Mailer:** {{ config('mail.default') }}  
**From:** {{ config('mail.from.address') }}
</x-mail::message>
