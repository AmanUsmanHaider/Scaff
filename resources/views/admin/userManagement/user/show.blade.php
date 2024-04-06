@extends('admin.layouts.app')
@section('page_title')
    {{(!empty($page_title) && isset($page_title)) ? $page_title : ''}}
@endsection
@push('head-scripts')
    <link rel="stylesheet" href="{{ asset('admin/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('admin/select2/dist/css/select2-bootstrap5.min.css') }}" rel="stylesheet" />
@endpush
@section('content')
    <div class="card mt-3">
        <div class="card-body">
            {{-- Start: Page Content --}}
            <div class="d-flex justify-content-between">
                <div>
                    <h4 class="card-title mb-0">{{(!empty($p_title) && isset($p_title)) ? $p_title : ''}}</h4>
                    <div class="small text-medium-emphasis">{{(!empty($p_summary) && isset($p_summary)) ? $p_summary : ''}}</div>
                </div>
                <div class="btn-toolbar d-none d-md-block" role="toolbar" aria-label="Toolbar with buttons">
                    @can('admin_user-management_user-list')
                        <a href="{{(!empty($url) && isset($url)) ? $url : ''}}" class="btn btn-sm btn-primary">{{(!empty($url_text) && isset($url_text)) ? $url_text : ''}}</a>
                    @endcan
                </div>
            </div>
            <hr>
            {{-- Start: Form --}}
            <form method="{{$method}}" action="{{$action}}" enctype="{{$enctype}}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label" for="name">Name</label>
                    <input type="text" disabled class="form-control @error('name') is-invalid @enderror" name="name" id="name" placeholder="Name" value="{{(!empty($data->name) && isset($data->name)) ? $data->name : old('name')}}">
                    @error('name')
                    <strong class="text-danger">{{ $message }}</strong>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" disabled class="form-control @error('email') is-invalid @enderror" name="email" id="email" placeholder="Email" value="{{(!empty($data->email) && isset($data->email)) ? $data->email : old('email')}}">
                    @error('email')
                    <strong class="text-danger">{{ $message }}</strong>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="status">Status</label>
                    <select disabled class="select2-options-status form-control @error('status') is-invalid @enderror" name="status">
                        <option value="">Please Select</option>
                        <option value="1" {{(isset($data->status)) && $data->status == 1  ? 'selected' : ''}}>Active</option>
                        <option value="0" {{(isset($data->status)) && $data->status == 0  ? 'selected' : ''}}>In-Active</option>
                    </select>
                    @error('status')
                    <strong class="text-danger">{{ $message }}</strong>
                    @enderror
                </div>
                <div class="mb-3">
                    <fieldset class="reset-this redo-fieldset">
                        <legend class="reset-this redo-legend">Roles</legend>
                        @if(!empty($roles))
                            @foreach ($roles as $role)
                                <div class="form-check form-check-inline">
                                    <input disabled class="form-check-input" type="checkbox" name="roles_arr[]" value="{{$role}}" @if(in_array($role, $userRoles)) checked @endif>
                                    <label class="form-check-label" for="roles_arr[]">{{$role}}</label>
                                </div>
                            @endforeach
                        @endif
                    </fieldset>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="module">Module</label>
                    <select disabled class="select2-options-module-id form-control @error('module') is-invalid @enderror" name="module"></select>
                    @error('module')
                    <strong class="text-danger">{{ $message }}</strong>
                    @enderror
                </div>
            </form>
            {{-- End: Form --}}
            {{-- Page Description : Start --}}
            @if(!empty($p_description) && isset($p_description))
                <div class="card-footer">
                    <div class="row">
                        <div class="col-12 mb-sm-2 mb-0">
                            <p>{{(!empty($p_description) && isset($p_description)) ? $p_description : ''}}</p>
                        </div>
                    </div>
                </div>
            @endif
            {{-- Page Description : End --}}
            {{-- End: Page Content --}}
        </div>
    </div>
@endsection
@push('footer-scripts')
    <script src="{{ asset('admin/select2/dist/js/select2.js') }}"></script>
    <script>
        $(document).ready(function() {
            //Get Module
            let module=[{
                id: "{{$data['module_id']}}",
                text: "{{$data['module_name']}}",
            }];
            $(".select2-options-module-id").select2({
                data: module,
                theme: "bootstrap5",
                placeholder: 'Select Module',
            });
            //Select Module
            $('.select2-options-module-id').select2({
                theme: "bootstrap5",
                placeholder: 'Select Module',
                ajax: {
                    url: '{{route('admin.get.module-select')}}',
                    dataType: 'json',
                    delay: 250,
                    type: 'GET',
                    data: function (params){
                        var query = {
                            q: params.term,
                            type: 'public',
                            _token: '{{csrf_token()}}'
                        }
                        return query;
                    },
                    processResults: function (data) {
                        return {
                            results:  $.map(data, function (item) {
                                return {
                                    id: item.id,
                                    text: item.name
                                }
                            })
                        };
                    },
                    cache: true
                }
            }).trigger('change.select2')
            //Select Status
            $('.select2-options-status').select2({
                theme: "bootstrap5",
                placeholder: 'Select Status',
            });
            //Auto Focus
            $(document).on('select2:open', () => {
                document.querySelector('.select2-search__field').focus();
            });
        });
    </script>
    {{-- Toastr : Script : Start --}}
    @if(Session::has('messages'))
        <script>
            noti({!! json_encode((Session::get('messages'))) !!});
        </script>
    @endif
    {{-- Toastr : Script : End --}}
@endpush
