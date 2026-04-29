<x-mail::message>
# Warning the server is short of RAM!

@if($connected_ips)
    The connected servers are:<br><br>
    @foreach($connected_ips as $connected_ip)
        {{ $connected_ip->connnected_server }}
    @endforeach
@else
    <br><br>Please investigate!
@endif

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
