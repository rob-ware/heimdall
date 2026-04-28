<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="refresh" content="20">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="//code.jquery.com/jquery-3.3.1.min.js"></script>
</head>

<body>
    <div style="padding-bottom: 40px;" class="container">
        <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-6 col-xs-6">
                    <a href="/code"><i class="fa fa-code" style="font-size:24px;color:blue">></i></i></a>
                </div>
                <div style="text-align: right" class="col-sm-6 col-md-6 col-lg-16 col-xs-6">
                    <a href="{{ env('APP_URL') }}"><i class="fa fa-sign-out" style="font-size:24px;color:red"></i></a>
                </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-sm-6 col-md-6 col-lg-6 col-xs-6">
                <div id="container1">
                    <script>
                        Highcharts.chart('container1', {
                            chart: {
                                type: 'column'
                            },
                            title: {
                                text: 'CPU Usage'
                            },
                            subtitle: {
                                text:
                                    '3 minute timeseries at 30 second intervals'
                            },
                            xAxis: {
                                categories: ['Snapshot #1', 'Snapshot #2', 'Snapshot #3', 'Snapshot #4', 'Snapshot #5', 'Snapshot #6'],
                                crosshair: true,
                                accessibility: {
                                    description: 'CPU Snapshots'
                                }
                            },
                            yAxis: {
                                min: 0,
                                title: {
                                    text: 'Percentage of CPU used'
                                }
                            },
                            tooltip: {
                                valueSuffix: ' %CPU'
                            },
                            plotOptions: {
                                column: {
                                    pointPadding: 0.2,
                                    borderWidth: 0
                                }
                            },
                            series: [
                                {
                                    name: '% CPU Use',
                                    data: [{!! $cpu_text !!}]
                                }
                            ]
                        });

                    </script>
                </div>
            </div>
            <div class="col-sm-6 col-md-6 col-lg-6 col-xs-6">
                <div id="container2">
                    <script>
                        Highcharts.chart('container2', {
                            chart: {
                                type: 'column'
                            },
                            title: {
                                text: 'RAM Usage'
                            },
                            subtitle: {
                                text:
                                    '3 minute timeseries at 30 second intervals'
                            },
                            xAxis: {
                                categories: ['Snapshot #1', 'Snapshot #2', 'Snapshot #3', 'Snapshot #4', 'Snapshot #5', 'Snapshot #6'],
                                crosshair: true,
                                accessibility: {
                                    description: 'RAM Snapshots'
                                }
                            },
                            yAxis: {
                                min: 0,
                                title: {
                                    text: 'Percentage of RAM used'
                                }
                            },
                            tooltip: {
                                valueSuffix: ' %RAM'
                            },
                            plotOptions: {
                                column: {
                                    pointPadding: 0.2,
                                    borderWidth: 0,
                                    color: '#FF0000'
                                }
                            },
                            series: [
                                {
                                    name: 'Max RAM = {!! $installed_ram !!}',
                                    data: [{!! $ram_text !!}]
                                }
                            ]
                        });

                    </script>
                </div>
            </div>
        </div>
    <div class="container">
        <div class="row">
            <div class="col-sm-6 col-md-6 col-lg-6 col-xs-6">
                <h8><strong>Server Logins</strong></h8><br><br>
                @if($visitors)
                    @foreach($visitors as $visitor)
                        <div class="row">
                            <div class="col-sm-2 col-md-2 col-lg-2 col-xs-2">
                                <img src="{{ $visitor->image }}" alt="{{ $visitor->name }}" width="50"">
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2 col-xs-2">
                                @if($visitor->authorised == 'no')
                                    <span style="color: red;" >
                                        {{ $visitor->login_time }}
                                    </span>
                                @else
                                    {{ $visitor->login_time }}
                                @endif
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2 col-xs-2">
                                @if($visitor->authorised == 'no')
                                    <span style="color: red;" >
                                        {{ $visitor->name }}
                                    </span>
                                @else
                                    {{ $visitor->name }}
                                @endif
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2 col-xs-2">
                                @if($visitor->authorised == 'no')
                                    <span style="color: red;" >
                                        {{ $visitor->ip_address }}
                                    </span>
                                @else
                                    {{ $visitor->ip_address }}
                                @endif
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2 col-xs-2">
                                @if($visitor->authorised == 'no')
                                    <span style="color: red;" >
                                        {{ $visitor->mac_address }}
                                    </span>
                                @else
                                    {{ $visitor->mac_address }}
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xs-12">
                            There are no current users on the server
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-sm-6 col-md-6 col-lg-6 col-xs-6">
                <h8><strong>Actions against Recruit .env</strong></h8>
                @if($env_actions)
                    @foreach($env_actions as $env_action)
                        <div class="row">
                            <div class="col-sm-4 col-md-4 col-lg-4 col-xs-4">
                                {{ $env_action->timestamp }}
                            </div>
                            <div class="col-sm-8 col-md-8 col-lg-8 col-xs-8">
                                {{ $env_action->action}}
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xs-12">
                            There are no current action against the Recruit .env file
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div style="padding-top:20px;" class="row">
            <div class="col-sm-6 col-md-6 col-lg-6 col-xs-6">
                <h8><strong>Failed Login Attemps</strong></h8><br><br>
                @if($failed_logins)
                    @foreach($failed_logins as $failed_login)
                        <div class="row">
                            <div class="col-sm-2 col-md-2 col-lg-2 col-xs-2">
                                <span style="color: red;" >
                                     {{ $failed_login->user }}
                                </span>
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2 col-xs-2">
                                {{ $failed_login->login_time }}
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2 col-xs-2">
                                <div class="col-sm-2 col-md-2 col-lg-2 col-xs-2">
                                    {{ $failed_login->protocol }}
                                </div>
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2 col-xs-2">
                                {{ $failed_login->ip_address }}
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xs-12">
                            There are no current failed logins on the server
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-sm-6 col-md-6 col-lg-6 col-xs-6">
                <h8><strong>Root Access</strong></h8>
                @if($sudo_events)
                    @foreach($sudo_events as $sudo_event)
                        <div class="row">
                            <div class="col-sm-2 col-md-2 col-lg-2 col-xs-2">
                                <span style="color: red;" >
                                     {{ $sudo_event->user }}
                                </span>
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2 col-xs-2">
                                {{ $sudo_event->timestamp }}
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xs-12">
                            There has been no recent access to the root user account.
                        </div>
                    </div>
                @endif
            </div>
        </div>


    </div>

</body>
</html>

