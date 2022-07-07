@extends('base')

@section('container')    
    <!-- Main content -->
    <section class="content pt-3">
        <div class="container-fluid">
            <h2 class="text-center display-4">Search</h2>
            <div class="row">
                <div class="col-md-10 offset-md-1">
                    <form action="/" method="POST">
                        @csrf
                        <div class="input-group">
                            <input type="search" class="form-control form-control-lg" placeholder="Type your keywords here" name="search">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-lg btn-default">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    
@endsection