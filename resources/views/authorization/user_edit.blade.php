@extends('layouts.admin')

@section('breadcrumb')
<ul class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="{{url('prosedur')}}">Authorization User Role</a></li>                            
    <li class="breadcrumb-item">{{ $model->name }}</li>
</ul>
@endsection

@section('content')
<div class="row clearfix" id="app_vue">
  <div class="col-md-12">
      <div class="card">
            <div class="header">
                <h2>User Detail Role & Permission</h2>
            </div>
            <div class="body">
                <form method="post" action="{{action('AuthorizationController@user')}}" enctype="multipart/form-data">
                @csrf
                
                <input name="id" type="hidden" value="{{ $id }}">
                <fieldset disabled>
                    <div class="form-group">
                        <label>Nama :</label>
                        <input type="text" class="form-control" name="name" value="{{ old('name', $model->name) }}">
                    </div>
                </fieldset>

                <div class="form-group">
                    <label>Daftar Roles:</label>

                    <select id="optrole" name="optrole[]" class="ms" multiple="multiple">
                        @foreach($all_roles as $data)
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
    <link rel="stylesheet" href="{!! asset('lucid/assets/vendor/multi-select/css/multi-select.css') !!}">
@endsection

@section('scripts')
<script src="{!! asset('lucid/assets/vendor/multi-select/js/jquery.multi-select.js') !!}"></script>
<script type="text/javascript" src="{{ URL::asset('js/app.js') }}"></script>
<script>
    var vm = new Vue({  
        el: "#app",
        data:  {
            list_roles: {!! json_encode($model->roles) !!},
        },
        methods: {
            getDatas: function(){
                var self = this;
                $('#wait_progres').modal('show');

                var roles_val =[];

                for(i=0;i<self.list_roles.length;++i){
                    roles_val.push(self.list_roles[i].id);
                }

                $("#optrole").val(roles_val);
                $("#optrole").multiSelect("refresh");

                $('#wait_progres').modal('hide');
            },
        }
    });

    $(document).ready(function() {
        vm.getDatas();
    });
</script>
@endsection