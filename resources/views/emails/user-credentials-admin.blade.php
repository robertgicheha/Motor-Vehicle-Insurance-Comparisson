<x-mail::message>
Dear {{ $admin->name }},

A user with the following credentials has been created successfully:

**Name:** {{ $user->name }}<br>
**Email:** {{ $user->email }}<br>
**Password:** {{ $password }}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
