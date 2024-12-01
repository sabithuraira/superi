@extends('layouts.admin')

@section('breadcrumb')
<ul class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="{{url('prosedur')}}">Authorization Role</a></li>                            
    <li class="breadcrumb-item">{{ $model->name }}</li>
</ul>
@endsection

@section('content')
<div class="row clearfix" id="app_vue">
  <div class="col-md-12">
      <div class="card">
            <div class="header">
                <h2>Role</h2>
            </div>
            <div class="body">
                <form method="post" action="{{action('AuthorizationController@role')}}" enctype="multipart/form-data">
                @csrf
                
                <input name="id" type="hidden" value="{{ $id }}">
                <fieldset>
                    <div class="form-group">
                        <label>Nama :</label>
                        <input type="text" class="form-control" name="name" value="{{ old('name', $model->name) }}">
                    </div>
                </fieldset>

                <div class="form-group">
                    <label>Halaman (Pindahkan Fitur yang diizinkan untuk ROLE ini di sisi kanan select menu):</label>

                    <select id="optpermission" name="optpermission[]" class="ms" multiple="multiple">
                        @foreach($all_permissions as $data)
                            <option value="{{ $data->id }}">{{ $data->name }}</option>
                        @endforeach
                    </select>
                </div>

                <br>
                <button type="submit" class="btn btn-primary">Simpan</button>

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
        el: "#app",
        data:  {
            list_permissions: {!! json_encode($model->permissions) !!},
        },
        methods: {
            getDatas: function(){
                var self = this;
                $('#wait_progres').modal('show');

                var permissions_val =[];

                for(i=0;i<self.list_permissions.length;++i){
                    permissions_val.push(self.list_permissions[i].id);
                }
                
                $("#optpermission").val(permissions_val);
                $("#optpermission").multiSelect("refresh");

                $('#wait_progres').modal('hide');
            },
        }
    });

    $(document).ready(function() {
        vm.getDatas();
    });

    // // //Multi-select
    // $('#optpermission').multiSelect();
</script>
@endsection