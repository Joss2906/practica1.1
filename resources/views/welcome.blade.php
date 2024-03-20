@extends('base')

@section('title', 'Welcome')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard (b)</div>

                <div class="card-body">
                        <div class="alert alert-success" role="alert">
                            Bienvenido 
                            @if (session('success'))
                                {{ session('success') }}
                            @endif
                            
                        </div>
                    

                    @if (session('role') == 1)
                        You are logged in as admin!
                    @elseif (session('role') == 2)
                        You are logged in as a user!
                    @endif

                    <!-- <form action="{{ route('logout') }}" method="POST">

                    </form> -->

                    <a type="button" class="btn btn-outline-danger" href="/logout">Log Out</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
