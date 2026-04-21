<x-mail::message>
# Warning! An unauthorised user is on the Recruit server!!

This is to warn you that an unauthorised user has accessed the Recruit server!

<x-mail::button :url="'https://test.heimdall.ulster.ac.uk'">
Review Server
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
