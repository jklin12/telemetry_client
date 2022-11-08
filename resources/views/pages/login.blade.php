@extends('layouts.empty', ['paceTop' => true])

@section('title', 'Login Page')

@section('content')
<!-- begin login-cover -->
<div class="login-cover">
    <div class="login-cover-image" style="background-image: url(/assets/img/login-bg-12.jpg)" data-id="login-cover-image"></div>
    <div class="login-cover-bg"></div>
</div>
<!-- end login-cover -->

<!-- begin login -->
<div class="login login-v2" data-pageload-addclass="animated fadeIn">
    <!-- begin brand -->
    <div class="login-header">
        <div class="brand">
            <span class="logo"></span> <b>Color</b> Admin
        </div>
        <div class="icon">
            <i class="fa fa-lock"></i>
        </div>
    </div>
    <!-- end brand -->



    <!-- begin login-content -->
    <div class="login-content">
        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{$errors->first('email')}}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
        <form method="POST" action="{{ route('login')}}" class="margin-bottom-0">
            @csrf
            <div class="form-group m-b-20">
                <input type="text" class="form-control form-control-lg" placeholder="Email Address" name="email" required />
            </div>
            <div class="form-group m-b-20">
                <input type="password" class="form-control form-control-lg" placeholder="Password" name="password" required />
            </div>
            <div class="login-buttons">
                <button type="submit" class="btn btn-indigo btn-block btn-lg">Sign me in</button>
            </div>

        </form>
    </div>
    <!-- end login-content -->
</div>
<!-- end login -->


@endsection

@push('scripts')

@endpush