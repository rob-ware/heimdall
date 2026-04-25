<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
                <a href="/server"><i class="fa fa-server" style="font-size:24px;color:blue">></i></i></a>
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
                                text: 'SQL Injection Risks'
                            },
                            subtitle: {
                                text:
                                    'Recruit directories implementing raw SQL'
                            },
                            xAxis: {
                                categories: [{!! $sql_category_text !!}],
                                crosshair: true,
                                accessibility: {
                                    description: 'Recruit Directories'
                                }
                            },
                            yAxis: {
                                min: 0,
                                title: {
                                    text: '#Files to check'
                                }
                            },
                            tooltip: {
                                valueSuffix: ''
                            },
                            plotOptions: {
                                column: {
                                    pointPadding: 0.2,
                                    borderWidth: 0
                                }
                            },
                            series: [
                                {
                                    name: '#Files to check',
                                    data: [{!! $sql_category_values !!}]
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
                                text: 'Script Injection Risks'
                            },
                            subtitle: {
                                text:
                                    'Recruit view directories exposed to script injection'
                            },
                            xAxis: {
                                categories: [{!! $script_category_text !!}],
                                crosshair: true,
                                accessibility: {
                                    description: 'Script Injection Risks'
                                }
                            },
                            yAxis: {
                                min: 0,
                                title: {
                                    text: '#Files to check'
                                }
                            },
                            tooltip: {
                                valueSuffix: ''
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
                                    name: '#Files to check',
                                    data: [{!! $script_category_values !!}]
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
                    @if($sql_files)
                        <h8><strong>Files to check for SQL Injection</strong></h8><br><br>
                        <div style="padding-bottom: 20px;" class="row">
                            <div class="col-sm-4 col-md-4 col-lg-4 col-xs-4">
                                Directory/File
                            </div>
                            <div class="col-sm-8 col-md-8 col-lg-8 col-xs-8">
                                Filename
                            </div>
                        </div>
                        @foreach($sql_files as $sql_file)
                            <div class="row">
                                <div class="col-sm-4 col-md-4 col-lg-4 col-xs-4">
                                    {{ $sql_file->head_directory }}
                                </div>
                                <div class="col-sm-8 col-md-8 col-lg-8 col-xs-8">
                                    {{ $sql_file->file_name }}
                                </div>
                            </div>
                        @endforeach
                    @else
                        No files require SQL revision.
                    @endif
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6 col-xs-6">
                    @if($script_files)
                        <h8><strong>Views to check for Script Injection</strong></h8><br><br>
                        <div style="padding-bottom: 20px;" class="row">
                            <div class="col-sm-4 col-md-4 col-lg-4 col-xs-4">
                                Directory/File
                            </div>
                            <div class="col-sm-8 col-md-8 col-lg-8 col-xs-8">
                                Filename
                            </div>
                        </div>
                        @foreach($script_files as $script_file)
                            <div class="row">
                                <div class="col-sm-4 col-md-4 col-lg-4 col-xs-4">
                                    {{ $script_file->head_directory }}
                                </div>
                                <div class="col-sm-8 col-md-8 col-lg-8 col-xs-8">
                                    {{ $script_file->file_name }}
                                </div>
                            </div>
                        @endforeach
                    @else
                        No files require XXS revision.
                    @endif
                </div>
            </div>
        </div>
</body>
