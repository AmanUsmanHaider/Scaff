@extends('log-viewer::logs._master')
@push('head-scripts')

@endpush
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-8">
                    <h4 class="card-title mb-0">
                        Logs by Date
                        <small class="text-muted">List </small>
                    </h4>
                    <div class="small text-muted">
                        Log Viewer Module
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                            <tr>
                                @foreach($headers as $key => $header)
                                    <th scope="col" class="{{ $key == 'date' ? 'text-left' : 'text-center' }}">
                                        @if ($key == 'date')
                                            {{ $header }}
                                        @else
                                            <span class="badge badge-level-{{ $key }}">
                                        {!! log_styler()->icon($key) . ' ' . $header !!}
                                    </span>
                                        @endif
                                    </th>
                                @endforeach
                                <th scope="col" class="text-end">Actions</th>
                            </tr>
                            </thead>
                            @can('admin_user-management_log-show')
                                <tbody>
                                @if ($rows->count() > 0)
                                    @foreach($rows as $date => $row)
                                        <tr>
                                            @foreach($row as $key => $value)
                                                <td class="{{ $key == 'date' ? 'text-left' : 'text-center' }}">
                                                    @if ($key == 'date')
                                                        <a href="{{ route('log-viewer::logs.show', [$date]) }}" class="btn btn-info">
                                                            {{ $value }}
                                                        </a>
                                                        <span class="badge badge-primary"></span>
                                                    @elseif ($value == 0)
                                                        <span class="badge empty">{{ $value }}</span>
                                                    @else
                                                        <a href="{{ route('log-viewer::logs.filter', [$date, $key]) }}">
                                                            <span class="badge badge-level-{{ $key }}">{{ $value }}</span>
                                                        </a>
                                                    @endif
                                                </td>
                                            @endforeach
                                            <td class="text-end">
                                                @can('admin_user-management_log-show')
                                                    <a href="{{ route('log-viewer::logs.show', [$date]) }}" class="btn btn-sm btn-info">
                                                        <i class="cil-search"></i>
                                                    </a>
                                                @endcan
                                                @can('admin_user-management_log-download')
                                                    <a href="{{ route('log-viewer::logs.download', [$date]) }}" class="btn btn-sm btn-success">
                                                        <i class="cil-cloud-download"></i>
                                                    </a>
                                                @endcan
                                                @can('admin_user-management_log-delete')
                                                    <a href="#delete-log-modal" class="btn btn-sm btn-danger" data-log-date="{{ $date }}">
                                                        <i class="cil-trash"></i>
                                                    </a>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="11" class="text-center">
                                            <span class="badge badge-secondary">{{ trans('log-viewer::general.empty-logs') }}</span>
                                        </td>
                                    </tr>
                                @endif
                                </tbody>
                            @endcan
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-7">
                    <div class="float-left">
                        @lang('Total') {!! $rows->total() !!}
                    </div>
                </div>
                <div class="col-5">
                    <div class="float-end">
                        {!! $rows->render() !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="del-model-wrapper">
            <div id="delete-log-modal" class="modal fade" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="btn-close shadow-none" data-coreui-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="font-weight-bold mb-2"> Are you sure you wanna delete this ?</p>
                            <p class="text-muted "> This item will be deleted immediately. You can't undo this action.</p>
                        </div>
                        <div class="modal-footer">
                            <form id="delete-log-form" action="{{ route('log-viewer::logs.delete') }}" method="POST">
                                @csrf
                                {{method_field('DELETE')}}
                                <input type="hidden" name="date" value="">
                                <button type="button" class="btn btn-light" data-coreui-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-sm btn-danger" data-loading-text="Loading">Delete</button>
                            </form>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('footer-scripts')
    <script>
        $(function () {
            var deleteLogModal = $('div#delete-log-modal'),
                deleteLogForm  = $('form#delete-log-form'),
                submitBtn      = deleteLogForm.find('button[type=submit]');

            $("a[href='#delete-log-modal']").on('click', function(event) {
                event.preventDefault();
                var date    = $(this).data('log-date'),
                    message = "{{ __('Are you sure you want to delete this log file: :date ?') }}";

                deleteLogForm.find('input[name=date]').val(date);
                deleteLogModal.find('.modal-body p').html(message.replace(':date', date));

                deleteLogModal.modal('show');
            });

            deleteLogForm.on('submit', function(event) {
                event.preventDefault();
                submitBtn.button('loading');

                $.ajax({
                    url:      $(this).attr('action'),
                    type:     $(this).attr('method'),
                    dataType: 'json',
                    data:     $(this).serialize(),
                    success: function(data) {
                        submitBtn.button('reset');
                        if (data.result === 'success') {
                            deleteLogModal.modal('hide');
                            location.reload();
                        }
                        else {
                            alert('AJAX ERROR ! Check the console !');
                            console.error(data);
                        }
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        alert('AJAX ERROR ! Check the console !');
                        console.error(errorThrown);
                        submitBtn.button('reset');
                    }
                });

                return false;
            });

            deleteLogModal.on('hidden.bs.modal', function() {
                deleteLogForm.find('input[name=date]').val('');
                deleteLogModal.find('.modal-body p').html('');
            });
        });
    </script>
    {{-- Toastr : Script : End --}}
@endpush
