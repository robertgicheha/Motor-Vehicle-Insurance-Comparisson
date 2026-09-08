<x-mail::message>
# Your Account Has Been Verified

Hello **{{ $user->name }}**,

Great news — your account has been verified successfully. You can now log in to the dashboard, generate quotations, and request stickers.

<x-mail::button :url="route('login')">
Login to Portal
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
