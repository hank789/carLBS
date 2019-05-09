@extends('backend.layouts.app')

@section('title', app_display_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="callout callout-success">
                                        <small class="text-muted">车辆数</small>
                                        <br>
                                        <strong class="h4">{{ $carsCount }}</strong>
                                        <div class="chart-wrapper">
                                            <canvas id="sparkline-chart-1" width="100" height="30"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="callout callout-info">
                                        <small class="text-muted">行程总数</small>
                                        <br>
                                        <strong class="h4">{{ $mainCount }}</strong>
                                        <div class="chart-wrapper">
                                            <canvas id="sparkline-chart-1" width="100" height="30"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="callout callout-success">
                                        <small class="text-muted">已完成行程数</small>
                                        <br>
                                        <strong class="h4">{{ $mainFinishedCount }}</strong>
                                        <div class="chart-wrapper">
                                            <canvas id="sparkline-chart-1" width="100" height="30"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="callout callout-danger">
                                        <small class="text-muted">未完成行程数</small>
                                        <br>
                                        <strong class="h4">{{ $mainUnFinishedCount }}</strong>
                                        <div class="chart-wrapper">
                                            <canvas id="sparkline-chart-1" width="100" height="30"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="mt-0">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box box-default">
                                <div class="box-header with-border">
                                    <h6 class="box-title">近30天数据报告</h6>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="chart-wrapper">
                                                <canvas id="transport_chart" height="400"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div><!--card-body-->
            </div><!--card-->

@endsection
@section('script')
    {!! script(('js/plugins/chartjs/Chart.min.js'),[],config('app.use_ssl')) !!}
    <script type="text/javascript">
        $(document).ready(function() {
            var transportChart = new Chart($("#transport_chart"), {
                type: 'line',
                data: {
                    labels: [{!! implode(",",$transportChart['labels']) !!}],
                    datasets: [
                        {
                            label: '行程总数',
                            backgroundColor: "#17a2b8",
                            borderColor: "#17a2b8",
                            fill: false,
                            data: [{{ implode(",",$transportChart['totalRange']) }}]
                        },
                        {
                            fill: false,
                            backgroundColor: "#00a65a",
                            borderColor: "#00a65a",
                            label: '已完成行程数',
                            data: [{{ implode(",",$transportChart['finishedRange']) }}]
                        },
                        {
                            fill: false,
                            backgroundColor: "#f5302e",
                            borderColor: "#f5302e",
                            label: '未完成行程数',
                            data: [{{ implode(",",$transportChart['unFinishedRange']) }}]
                        },
                    ]
                },
                options: {
                    tooltips: {
                        mode: 'index',
                        intersect: false,
                    },
                    hover: {
                        mode: 'nearest',
                        intersect: true
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero:true
                            }
                        }]
                    }
                }
            });
        });
    </script>
@endsection