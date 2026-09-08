<x-mail::message>
# Email Address Changed

Hello **{{ $user->name }}**,

This is a confirmation that your account's email address was changed from **{{ $previousEmail }}** to **{{ $user->email }}** successfully on {{ now()->format('d M Y, h:i A') }}.

If you did not make this change, please contact support immediately.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
