<x-mail::message>
Dear {{ $admin->name }},

A quotation **{{ $quotation->quotation_number }}** for vehicle **{{ $quotation->vehicle_description }}** has been generated successfully. The quotation details have been attached as an attachment for your view.

<x-mail::button :url="route('quotations.show', $quotation)">
View Quotation
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
