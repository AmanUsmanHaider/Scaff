@extends('layouts.guest')
@section('page_title')
    {{(!empty($page_title) && isset($page_title)) ? $page_title : ''}}
@endsection
@push('head-scripts')

@endpush
@section('content')
    <div class="bg-light min-vh-100 d-flex flex-row align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-4">
                    <div class="card-group d-block d-md-flex row">
                        <div class="card col-md-5 p-4 mb-0">
                            <div class="card-body">
                                <form method="{{$method}}" action="{{$action}}" enctype="{{$enctype}}">
                                    @csrf
                                    <input type="hidden" name="token" value="{{ $request->route('token') }}">
                                    <h1>{{(!empty($title) && isset($title)) ? $title : ''}}</h1>
                                    <p class="text-medium-emphasis">{{(!empty($description) && isset($description)) ? $description : ''}}</p>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">
                                            <i class="icon cil-envelope-closed"></i>
                                        </span>
                                        <input class="form-control @error('email') is-invalid @enderror" type="email" placeholder="Email" name="email" value="{{old('email', $request->email)}}">
                                    </div>
                                    @error('email')
                                    <strong>{{ $message }}</strong>
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">
                                       <i class="icon cil-lock-locked"></i>
                                        </span>
                                        <input class="form-control @error('password') is-invalid @enderror" type="password" name="password" placeholder="Password">
                                    </div>
                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">
                                          <i class="icon cil-lock-locked"></i>
                                        </span>
                                        <input class="form-control @error('password_confirmation') is-invalid @enderror" type="password" name="password_confirmation" placeholder="Confirm Password">
                                    </div>
                                    @error('password_confirmation')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="d-flex justify-content-end">
                                                <button class="btn btn-dark px-4" type="submit">Password Reset</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('footer-scripts')

@endpush

