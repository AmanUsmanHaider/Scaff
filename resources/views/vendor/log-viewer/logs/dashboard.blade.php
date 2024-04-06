@extends('log-viewer::logs._master')
@push('head-scripts')

@endpush
@section('content')
    <div class="card mt-3">
        <div class="card-body">
            {{-- Start: Page Content --}}
            {{-- Datatatble : Start --}}
            <div class="row">
                <div class="col-md-6 col-lg-3">
                    <canvas id="stats-doughnut-chart" height="300" class="mb-3"></canvas>
                </div>
                <div class="col-md-6 col-lg-9">
                    <div class="row">
                        @foreach($percents as $level => $item)
                            <div class="col-sm-6 col-md-12 col-lg-4 mb-3">
                                <div class="box level-{{ $level }} {{ $item['count'] === 0 ? 'empty' : '' }}">
                                    <div class="box-icon">
                                        {!! log_styler()->icon($level) !!}
                                    </div>

                                    <div class="box-content">
                                        <span class="box-text">{{ $item['name'] }}</span>
                                        <span class="box-number">
                                    {{ $item['count'] }} @lang('entries') - {!! $item['percent'] !!} %
                                </span>
                                        <div class="progress" style="height: 3px;">
                                            <div class="progress-bar" style="width: {{ $item['percent'] }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            {{-- Datatatble : End --}}
            {{-- End: Page Content --}}
        </div>
    </div>

@endsection
@push('footer-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>
    <script>
        $(function() {
            new Chart(document.getElementById("stats-doughnut-chart"), {
                type: 'doughnut',
                data: {!! $chartData !!},
                options: {
                    legend: {
                        position: 'bottom'
                    }
                }
            });
        });
    </script>
    {{-- Toastr : Script : End --}}
@endpush
