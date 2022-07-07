@extends('base')

@section('header')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/AdminLTE/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/AdminLTE/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/AdminLTE/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endsection

@section('container')    
    <!-- Main content -->
    <section class="content py-3">
        <div class="container-fluid">
            {{-- Search --}}
            <h2 class="text-center display-4">Search</h2>
            <div class="row">
                <div class="col-md-10 offset-md-1">
                    <form action="/" method="POST">
                        @csrf
                        <div class="input-group">
                            <input type="search" class="form-control form-control-lg" placeholder="Type your keywords here" name="search" value="{{ $old }}">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-lg btn-default">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- After Search --}}
            <div class="row mt-2">
                <div class="col-md-10 offset-md-1">
                    <button type="button" class="btn btn-default btn-sm float-right" data-toggle="modal" data-target="#modal-xl">
                        Lihat proses
                    </button>
                    <p class="text-muted pl-1">{{ $jum_relevan }} hasil dari {{ $jum_doc }} dokumen ({{ number_format($execution_time,3) }} detik)</p>
                </div>
            </div>

            {{-- List Of Documents --}}
            <div class="row mt-3">
                <div class="col-md-10 offset-md-1">
                    <div class="list-group">
                        @foreach ($rank as $item)
                            @if ($item['cosine'] >= 0.9)
                                <div class="list-group-item mb-3">
                                    <div class="row">
                                        <div class="col px-4">
                                            <div>
                                                <div class="float-right mt-1 text-muted">similarity : <b>{{ number_format($item['cosine'],3) }}</b></div>
                                                <h5><a href="{{ route('detail', $item['id']) }}">{{ Illuminate\Support\Str::words($item['judul'],7) }}</a></h5>
                                                <p class="mb-0 text-justify">{{ $item['doc'] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- MODAL --}}
    <div class="modal fade" id="modal-xl">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Proses</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{-- table Frequency--}}
                    <div class="row">
                        <div class="col-12">
                            <div class="card card-info card-outline">
                                <div class="card-header">
                                <h3 class="card-title"><b>TF-IDF</b></h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="tabel-frequency" class="table table-bordered table-hover">
                                            <thead>
                                            <tr>
                                            <th scope="col">Term</th>
                                            <th scope="col" class="text-center">Q</th>
                                            @foreach ($docs as $item)
                                                <th scope="col" class="text-center">D{{ $loop->iteration }}</th>
                                            @endforeach
                                            <th scope="col" class="text-center">DF</th>
                                            <th scope="col" class="text-center">IDF</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($index as $term => $value)
                                                    <tr>
                                                        <th>{{ $term }}</th>
                                                        <td class="text-center">{{ isset($value['query']) ? $value['query'] : '0' }}</td>
                                                        @foreach ($docs as $doc)
                                                            @if (isset($value[$doc->id]))
                                                                <td class="text-center">{{ $value[$doc->id] }}</td>
                                                            @else
                                                                <td class="text-center">0</td>
                                                            @endif
                                                        @endforeach
                                                        <td class="text-center">{{ $df[$term] }}</td>
                                                        <td class="text-center">{{ $idf[$term] }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col -->
                    </div>
                    
                    {{-- table Weight--}}
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card card-info card-outline">
                                <div class="card-header">
                                <h3 class="card-title"><b>Weight</b></h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="tabel-weight" class="table table-bordered table-hover">
                                            <thead>
                                            <tr>
                                            <th scope="col">Term</th>
                                            <th scope="col" class="text-center">Q</th>
                                            @foreach ($docs as $item)
                                                <th scope="col" class="text-center">D{{ $loop->iteration }}</th>
                                            @endforeach
                                            </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($weight as $term => $value)
                                                    <tr>
                                                        <th>{{ $term }}</th>
                                                        <td class="text-center">{{ $value['query'] }}</td>
                                                        @foreach ($docs as $doc)
                                                            <td class="text-center">{{ $value[$doc->id] }}</td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col -->
                    </div>

                    {{-- table GVSM--}}
                    <div class="row mt-3">
                        <div class="col">
                            <div class="card card-info card-outline">
                                <div class="card-header">
                                    <h3 class="card-title"><b>Hasil Perhitungan Generalized Vector Space Model</b></h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="table-gvsm" class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th scope="col" class="text-center">Doc</th>
                                                    @foreach ($docs as $doc)
                                                        <th scope="col" class="text-center">D{{ $doc->id }}</th>
                                                    @endforeach
                                                    <th scope="col" class="text-center">Query</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($docs as $doc1)
                                                    <tr>
                                                        <td>{{ $doc1->id }}</td>
                                                        @foreach ($docs as $doc2)
                                                            <td>{{ $vectorDoc[$doc2->id][$doc1->id] }}</td>
                                                        @endforeach
                                                        <td>{{ $vectorDoc['query'][$doc1->id] }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- table Cosine--}}
                    <div class="row mt-3">
                        <div class="col">
                            <div class="card card-info card-outline">
                                <div class="card-header">
                                <h3 class="card-title"><b>Cosine Similarity</b></h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                <table id="table-cosine" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                    <th scope="col" class="text-center">ID</th>
                                    <th scope="col" class="text-center">Judul Skripsi</th>
                                    <th scope="col" class="text-center">Similarity</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($rank as $key => $value)
                                            <tr>
                                                <td>{{ $value['id'] }}</td>
                                                <td><a href="{{ route('detail', $value['id']) }}">{{ $value['judul'] }}</a></td>
                                                @if ($value['cosine'] >= 0.9)
                                                    <td class="text-center"><b>{{ $value['cosine'] }}<b></td>
                                                @else
                                                    <td class="text-center">{{ $value['cosine'] }}</td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col -->
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('assets/AdminLTE/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/AdminLTE/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/AdminLTE/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/AdminLTE/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/AdminLTE/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/AdminLTE/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/AdminLTE/plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/AdminLTE/plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/AdminLTE/plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/AdminLTE/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/AdminLTE/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/AdminLTE/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    <script>
        $(function () {    
            $('#tabel-frequency').DataTable({
                "paging": false,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": false,
                "autoWidth": false,
                "responsive": false,
            });
            
            $('#tabel-weight').DataTable({
                "paging": false,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": false,
                "autoWidth": false,
                "responsive": false,
            });
            
            $('#table-gvsm').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });

            $('#table-cosine').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
        });
    </script>
@endsection