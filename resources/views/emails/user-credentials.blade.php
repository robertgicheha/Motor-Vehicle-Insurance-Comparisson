@component('mail::message')
# Welcome to Motor Vehicle Insurance Portal

Your account has been created successfully. Here are your login credentials:

**Username:** {{ $user->email }}
**Password:** {{ $password }}

Please login at: {{ route('login') }}

For security reasons, please navigate to the Profile section after logging in to change your password.

If you have any questions, please contact our support team.

Thanks,<br>
Motor Vehicle Insurance Portal Team
@endcomponent
