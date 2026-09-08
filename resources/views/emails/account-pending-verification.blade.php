<x-mail::message>
# Account Created — Pending Verification

Hello **{{ $user->name }}**,

Thank you for creating an account with the **{{ config('app.name') }}**. Your account has been created successfully, but it is currently **pending verification** by our team.

You will receive another email as soon as your account has been verified — after that you'll be able to log in, generate quotations, and request stickers.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
