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
                                    <h1>{{(!empty($title) && isset($title)) ? $title : ''}}</h1>
                                    <p class="text-medium-emphasis">{{(!empty($description) && isset($description)) ? $description : ''}}</p>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">
                                            <i class="icon cil-user"></i>
                                        </span>
                                        <input class="form-control @error('email') is-invalid @enderror" type="email" placeholder="Email" name="email" value="{{old('email')}}">
                                    </div>
                                    @error('email')
                                        <strong class="text-danger">{{ $message }}</strong>
                                    @enderror
                                    <div class="input-group mb-4">
                                        <span class="input-group-text">
                                             <i class="icon cil-lock-locked"></i>
                                        </span>
                                        <input class="form-control @error('password') is-invalid @enderror" type="password" name="password" placeholder="Password">
                                    </div>
                                    @error('password')
                                        <strong class="text-danger">{{ $message }}</strong>
                                    @enderror
                                    <div class="input-group mb-4">
                                        <div class="block">
                                            <label for="remember_me" class="inline-flex items-center">
                                                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                                                <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="d-flex justify-content-end">
                                                @if (Route::has('password.request'))
                                                    <a href="{{$reset_password}}" class="btn btn-link px-3" type="button">Forgot password?</a>
                                                @endif
                                                <button class="btn btn-dark px-4" type="submit">Login</button>
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

