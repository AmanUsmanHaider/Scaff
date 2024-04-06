@extends('layouts.guest')
@section('page_title')
    {{(!empty($page_title) && isset($page_title)) ? $page_title : 'Email Verification'}}
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
                                @if (session('status') == 'verification-link-sent')
                                    <div class="text-success">
                                        A new verification link has been sent to the email address you provided during registration.
                                    </div>
                                @endif
                                <form method="post" action="{{ route('verification.send') }}" enctype="application/x-www-form-urlencoded">
                                    @csrf
                                    <h1>Email Verify</h1>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="d-flex justify-content-end">
                                                <button class="btn btn-dark px-4 mx-2" type="submit">Resend Email</button>
                                                <form method="POST" action="{{ route('logout') }}">
                                                    @csrf
                                                    <button class="btn btn-danger px-4" type="submit">Logout</button>
                                                </form>
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

