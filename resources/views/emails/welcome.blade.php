<x-mail::message>
# Welcome to Motor Vehicle Insurance Portal

Hello **{{ $user->name }}**,

Thank you for creating an account with us. You can now:
- Generate insurance quotations
- Compare rates from multiple insurers
- Download certificates and stickers
- Track your quotation status

<x-mail::button :url="route('dashboard')">
Go to Dashboard
</x-mail::button>

If you signed up with Google, please complete your profile to unlock all features.

<x-mail::button :url="route('profile.edit')">
Complete Profile
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
