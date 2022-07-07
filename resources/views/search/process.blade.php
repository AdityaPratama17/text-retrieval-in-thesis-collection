@extends('base')

@section('container')    
    <!-- Main content -->
    <section class="content pt-5">
        <div class="container-fluid">
            {{-- search bar --}}
            <h2 class="text-center display-4">Search</h2>
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <form action="/" method="POST">
                        @csrf
                        <div class="input-group">
                            <input type="text" class="form-control form-control-lg" placeholder="Type your keywords here" name="search" value="{{ $old }}">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-lg btn-default">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- table Frequency--}}
            <div class="row mt-5">
                <div class="col-12">
                  <div class="card card-info card-outline">
                    <div class="card-header">
                      <h3 class="card-title"><b>TF-IDF</b></h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                      <table id="table1" class="table table-bordered table-hover">
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
                      <table id="table2" class="table table-bordered table-hover">
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
                    <!-- /.card-body -->
                  </div>
                  <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>

            {{-- table Cosine--}}
            <div class="row my-3">
                <div class="col-6">
                  <div class="card card-info card-outline">
                    <div class="card-header">
                      <h3 class="card-title"><b>Cosine Similarity</b></h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                      <table id="table3" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                          <th scope="col" class="text-center">Doc</th>
                          <th scope="col" class="text-center">Similarity</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($rank as $key => $value)
                                <tr>
                                    <td class="text-center">{{ $value['id'] }}</td>
                                    <td class="text-center">{{ $value['cosine'] }}</td>
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
    </section>
    
@endsection