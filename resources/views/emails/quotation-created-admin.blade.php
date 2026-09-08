<x-mail::message>
Dear {{ $admin->name }},

@php $__customerFullName = trim(($quotation->customer_first_name ?? '') . ' ' . ($quotation->customer_last_name ?? '')) ?: $quotation->customer_name; @endphp
**{{ $__customerFullName }}** has generated a quotation **{{ $quotation->quotation_number }}** successfully. The quotation details have been attached as an attachment for your view.

<x-mail::button :url="route('quotations.show', $quotation)">
View Quotation
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
