<x-mail::message>
# A large number of HTTPS connections have been noted!

The connected servers are:<br><br>
@foreach($connected_ips as $connected_ip)
    {{ $connected_ip->connnected_server }}
@endforeach
<br><br>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
