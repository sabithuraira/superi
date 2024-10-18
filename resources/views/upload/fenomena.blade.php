@extends('layouts.admin')

@section('breadcrumb')
<ul class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon-home"></i></a></li>                           
    <li class="breadcrumb-item">Import Excel Fenomena</li>
</ul>
@endsection

@section('content')
<div class="row clearfix">
  <div class="col-md-12">
      <div class="card">
          <div class="header">
              <h2>Import Excel Fenomena</h2>
          </div>
          <div class="body">
                <form method="post" action="{{url('upload/fenomena_import')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="row clearfix">

                        <div class="col-lg-4 col-md-12 left-box">
                            <div class="form-group">
                                <label>Kabupaten/Kota:</label>

                                <div class="input-group">
                                    <select class="form-control  form-control-sm" name="wilayah"  
                                        value="{{ $wilayah }}">
                                        @foreach (config('app.wilayah') as $key=>$value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-12 left-box">
                            <div class="form-group">
                                <label>Tahun:</label>

                                <div class="input-group">
                                <select class="form-control  form-control-sm" name="tahun" value="{{ $tahun }}">
                                    @for ($i=date('Y');$i>=2023;$i--)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-12 right-box">
                            <div class="form-group">
                                <label>Pilih File:</label>
                                <input type="file" class="form-control" name="excel_file">
                            </div>
                        </div>
                    </div>

                    <br>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
          </div>
      </div>
  </div>
</div>
@endsection