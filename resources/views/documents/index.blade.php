@extends('base')

@section('header')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/AdminLTE/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/AdminLTE/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/AdminLTE/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endsection

@section('container')
    <!-- Content Header (Page header) -->   
    <div class="content-header">
        <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0">Documents</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active">Documents</li>
            </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        {{-- Tabel Document --}}
        <div class="row">
          <div class="col-12">
            <div class="card card-primary card-outline">
              <div class="card-header">
                <h3 class="card-title"><b>Documents</b></h3>
              </div>
              <!-- /.card-header -->              
              <div class="card-body">
                <table id="table1" class="table table-bordered table-hover">
                  <thead>
                  <tr>
                    <th></th>
                    <th>Judul</th>
                    <th>Abstrak</th>
                  </tr>
                  </thead>
                  <tbody>
                    @foreach ($docs as $key => $doc)
                      <tr>
                          <td>{{ $loop->iteration }}</td>
                          <td>{{ $doc->judul }}</td>
                          <td>{{ $docs_excerpt[$doc->id] }}
                            <button class="btn btn-link p-0 modal-detail"  
                            data-judul="{{ $doc->judul }}"
                            data-konten="{{ $doc->doc }}"
                            data-after="{{ $docs_after[$doc->id] }}"
                            >more</button>
                          </td>
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
        <!-- /.row -->
        
        {{-- tabel Frequency --}}
        <div class="row mt-3">
          <div class="col-12">
            <div class="card card-danger card-outline">
              <div class="card-header">
                <h3 class="card-title"><b>Frequency</b></h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="table2" class="table table-bordered table-hover">
                  <thead>
                  <tr>
                    <th scope="col">Term</th>
                    @foreach ($docs as $item)
                        <th scope="col" class="text-center">D{{ $loop->iteration }}</th>
                    @endforeach
                    <th scope="col" class="text-center">DF</th>
                  </tr>
                  </thead>
                  <tbody>
                    @foreach ($tf as $term => $value)
                        <tr>
                          <th>{{ $term }}</th>
                          @foreach ($docs as $doc)
                              @if (isset($value[$doc->id]))
                                  <td class="text-center">{{ $value[$doc->id] }}</td>
                              @else
                                  <td class="text-center">0</td>
                              @endif
                          @endforeach
                          <td class="text-center">{{ $df[$term] }}</td>
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
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->

    <div class="modal fade" id="modalDetail">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title"><span id="judul"></span></h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="card card-primary card-outline">
                <div class="card-header">
                  <h3 class="card-title"><b>Abstrak</b></h3>
                </div>
                <div class="card-body">
                  <p class="text-justify"><span id="konten"></span></p>
                </div>
            </div>
            <div class="card card-primary card-outline">
                <div class="card-header">
                  <h3 class="card-title"><b>After Preprocessing</b></h3>
                </div>
                <div class="card-body">
                  <p class="text-justify"><span id="after"></span></p>
                </div>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
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
      $('#table1').DataTable({
          "paging": true,
          "lengthChange": true,
          "searching": true,
          "ordering": true,
          "info": true,
          "autoWidth": false,
          "responsive": true,
          // "scrollX": true,
      });
      
      $('#table2').DataTable({
          "paging": true,
          "lengthChange": true,
          "searching": true,
          "ordering": true,
          "info": true,
          "autoWidth": false,
          "responsive": true,
          // "scrollX": true,
      });
    });
  </script>
  
  {{-- MODAL DETAIL --}}
  <script>
    $(function() {
      $('.modal-detail').on('click', function() {
          var judul = $(this).attr('data-judul');
          $('#judul').text(judul);
          var konten = $(this).attr('data-konten');
          $('#konten').text(konten);
          var after = $(this).attr('data-after');
          $('#after').text(after);

          $('#modalDetail').modal('show');   
      });     
    });
  </script>
@endsection
