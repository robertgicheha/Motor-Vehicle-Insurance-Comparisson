<x-mail::message>
# Password Changed

Hello **{{ $user->name }}**,

This is a confirmation that the password for your account ({{ $user->email }}) was changed successfully on {{ now()->format('d M Y, h:i A') }}.

If you did not make this change, please contact support immediately.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
