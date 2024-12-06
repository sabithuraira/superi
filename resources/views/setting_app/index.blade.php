@extends('layouts.admin')

@section('breadcrumb')
<ul class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon-home"></i></a></li>                           
    <li class="breadcrumb-item">Import Excel PDRB</li>
</ul>
@endsection

@section('content')
<div class="row clearfix">
  <div class="col-md-12">
      <div class="card" id="app_vue">
          <div class="header">
              <h2>Konfigurasi Aplikasi</h2>
          </div>
          <div class="body">
                <form method="post" action="{{url('setting_app')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="row clearfix">

                        <div class="col-lg-4 col-md-12 left-box">
                            <div class="form-group">
                                <label>Tahun Berlaku:</label>
                            </div>
                        </div>

                        <div class="col-lg-8 col-md-12 right-box">
                            <div class="form-group">
                                <select class="form-control  form-control-sm" name="tahun_berlaku" v-model="tahun_berlaku">
                                    @for ($i=date('Y');$i>=2023;$i--)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row clearfix">
                        <div class="col-lg-4 col-md-12 left-box">
                            <div class="form-group">
                                <label>Triwulan Berlaku:</label>
                            </div>
                        </div>

                        <div class="col-lg-8 col-md-12 right-box">
                            <select class="form-control  form-control-sm" name="triwulan_berlaku" v-model="triwulan_berlaku">
                                @for ($i=1;$i<=4;$i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    
                    <div class="row clearfix">
                        <div class="col-lg-12 col-md-12">
                            <button class="btn btn-success float-right" type="submit">SIMPAN</button>
                        </div>
                    </div>
                </form>
          </div>
      </div>
  </div>
</div>
@endsection


@section('css')
  <meta name="_token" content="{{csrf_token()}}" />
  <meta name="csrf-token" content="@csrf">
@endsection

@section('scripts')
<script type="text/javascript" src="{{ URL::asset('js/app.js') }}"></script>

<script>
var vm = new Vue({  
    el: "#app_vue",
    data:  {
        tahun_berlaku: {!! json_encode($tahun_berlaku->setting_value) !!},
        triwulan_berlaku: {!! json_encode($triwulan_berlaku->setting_value) !!},
    },
    watch: {
    },
    methods: {
    }
});

$(document).ready(function() {
});
</script>
@endsection