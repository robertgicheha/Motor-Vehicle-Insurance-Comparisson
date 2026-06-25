<x-mail::message>
# Email Change Request

Hi {{ $user->first_name ?: $user->name }},

You recently requested to change your email address to **{{ $user->pending_email }}**.

Please click the button below to verify this new email address. This link will expire in 60 minutes.

<x-mail::button :url="$verificationUrl">
Verify New Email
</x-mail::button>

If you did not request this change, you can safely ignore this email — your email address will remain unchanged.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
