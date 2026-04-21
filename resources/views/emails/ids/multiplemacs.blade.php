<x-mail::message>
# Warning! Multiple MAC addresses detected!

This is to let you know that a user associated with multiple MAC addresses is logged into the Recruit server.

<x-mail::button :url="'https://test.heimdall.ulster.ac.uk'">
Review Server
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
