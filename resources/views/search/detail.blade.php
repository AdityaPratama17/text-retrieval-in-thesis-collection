@extends('base')

@section('container')
    <!-- Content Header (Page header) -->   
    <div class="content-header">
        <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0">Detail</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active">Detail</li>
            </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row bg-white border p-3">
                <div class="col-12">
                    <h4 class="font-weight-bold">{{ $doc->judul }}</h4>
                    <p class="text-justify mt-3">{{ $doc->doc }}</p>
                </div>
            </div>
            <br>
        </div>
    </section>
    
@endsection