@extends('log-viewer::logs._master')
@push('head-scripts')

@endpush
@section('content')
    <div class="card mt-3">
        <div class="card-body">
            {{-- Start: Page Content --}}
            <div class="row">
                <div class="col-lg-2">
                    {{-- Log Menu --}}
                    <div class="card mb-4">
                        <div class="card-header"><i class="cil-flag-alt"></i> @lang('Levels')</div>
                        <div class="list-group list-group-flush log-menu">
                            @foreach($log->menu() as $levelKey => $item)
                                @if ($item['count'] === 0)
                                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center disabled">
                                        <span class="level-name">{!! $item['icon'] !!} {{ $item['name'] }}</span>
                                        <span class="badge empty">{{ $item['count'] }}</span>
                                    </a>
                                @else
                                    <a href="{{ $item['url'] }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center level-{{ $levelKey }}{{ $level === $levelKey ? ' active' : ''}}">
                                        <span class="level-name">{!! $item['icon'] !!} {{ $item['name'] }}</span>
                                        <span class="badge badge-level-{{ $levelKey }}">{{ $item['count'] }}</span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="col-lg-10">
                    {{-- Log Details --}}
                    <div class="card mb-4">
                        <div class="card-header">
                            <strong>
                                @lang('Log Info')
                            </strong>
                            <div class="btn-toolbar float-end">
                                @can('admin_user-management_log-download')
                                    <a href="{{ route('log-viewer::logs.download', [$log->date]) }}" class="btn btn-success">
                                        <i class="cil-cloud-download"></i>&nbsp;@lang('Download')
                                    </a>
                                @endcan
                                @can('admin_user-management_log-delete')
                                    <a href="#delete-log-modal" class="btn btn-danger ms-1" data-coreui-toggle="modal">
                                        <i class="cil-trash"></i>&nbsp;@lang('Delete')
                                    </a>
                                @endcan
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-condensed mb-0">
                                <tbody>
                                <tr>
                                    <td>File path :</td>
                                    <td colspan="7">{{ $log->getPath() }}</td>
                                </tr>
                                <tr>
                                    <td>Log entries : </td>
                                    <td>
                                        <span class="badge text-bg-primary">{{ $entries->total() }}</span>
                                    </td>
                                    <td>Size :</td>
                                    <td>
                                        <span class="badge text-bg-primary">{{ $log->size() }}</span>
                                    </td>
                                    <td>Created at :</td>
                                    <td>
                                        <span class="badge text-bg-primary">{{ $log->createdAt() }}</span>
                                    </td>
                                    <td>Updated at :</td>
                                    <td>
                                        <span class="badge text-bg-primary">{{ $log->updatedAt() }}</span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer">
                            {{-- Search --}}
                            <form action="{{ route('log-viewer::logs.search', [$log->date, $level]) }}" method="GET">
                                <div class=form-group">
                                    <div class="input-group">
                                        <input id="query" name="query" class="form-control" value="{!! request('query') !!}" placeholder="Type here to search">
                                        <div class="input-group-append">
                                            @if (request()->has('query'))
                                                <a href="{{ route('log-viewer::logs.show', [$log->date]) }}" class="btn bg-secondary">
                                                    <i class="cil-delete"></i>
                                                </a>
                                            @endif
                                            <button id="search-btn" class="btn btn-sm btn-primary">
                                                <span class="cil-search"></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Log Entries --}}
                    <div class="card mb-4">
                        @if ($entries->hasPages())
                            <div class="card-header">
                                <span class="badge badge-info float-end">
                                    Page {!! $entries->currentPage() !!} of {!! $entries->lastPage() !!}
                                </span>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table id="entries" class="table mb-0">
                                <thead>
                                <tr>
                                    <th>ENV</th>
                                    <th style="width: 120px;">Level</th>
                                    <th style="width: 65px;">Time</th>
                                    <th>Header</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($entries as $key => $entry)
                                    <tr>
                                        <td>
                                            <span class="badge badge-env">{{ $entry->env }}</span>
                                        </td>
                                        <td>
                                                <span class="badge badge-level-{{ $entry->level }}">
                                                    {!! $entry->level() !!}
                                                </span>
                                        </td>
                                        <td>
                                                <span class="badge bg-secondary">
                                                    {{ $entry->datetime->format('H:i:s') }}
                                                </span>
                                        </td>
                                        <td>
                                            {{ $entry->header }}
                                        </td>
                                        <td class="text-end">
                                            @if ($entry->hasStack())
                                                <a class="btn btn-sm btn-light" role="button" data-coreui-toggle="collapse" href="#log-stack-{{ $key }}" aria-expanded="false" aria-controls="log-stack-{{ $key }}">
                                                    <i class="fa fa-toggle-on"></i> Stack
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @if ($entry->hasStack())
                                        <tr>
                                            <td colspan="5" class="stack py-0">
                                                <div class="stack-content collapse" id="log-stack-{{ $key }}">
                                                    {!! $entry->stack() !!}
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            <span class="badge bg-secondary">{{ trans('log-viewer::general.empty-logs') }}</span>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {!! $entries->appends(compact('query'))->render('pagination::bootstrap-5') !!}
                </div>
            </div>
            {{-- End: Page Content --}}
        </div>
        {{-- DELETE MODAL --}}
        <div id="delete-log-modal" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <form id="delete-log-form" action="{{ route('log-viewer::logs.delete') }}" method="POST">
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="date" value="{{ $log->date }}">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">DELETE LOG FILE</h5>
                            <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to <span class="badge bg-danger">DELETE</span> this log file <span class="badge text-bg-warning">{{ $log->date }}</span> ?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-secondary mr-auto" data-coreui-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-sm btn-danger" data-loading-text="Loading&hellip;">DELETE FILE</button>
                        </div>
                    </div>
                </form>
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
                            location.replace("{{ route('log-viewer::logs.list') }}");
                        }
                        else {
                            alert('OOPS ! This is a lack of coffee exception !')
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

            @unless (empty(log_styler()->toHighlight()))
            @php
                $htmlHighlight = version_compare(PHP_VERSION, '7.4.0') >= 0
                    ? join('|', log_styler()->toHighlight())
                    : join(log_styler()->toHighlight(), '|');
            @endphp

            $('.stack-content').each(function() {
                var $this = $(this);
                var html = $this.html().trim()
                    .replace(/({!! $htmlHighlight !!})/gm, '<strong>$1</strong>');

                $this.html(html);
            });
            @endunless
        });
    </script>
@endpush
