@extends('layouts.admin')

@section('breadcrumb')
<ul class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon-home"></i></a></li>                     
    <li class="breadcrumb-item">Authorization Role</li>
</ul>
@endsection

@section('content')
    <div class="container" id="app_vue">
      <br />
      @if (\Session::has('success'))
        <div class="alert alert-success">
          <p>{{ \Session::get('success') }}</p>
        </div><br />
      @endif

      <div class="card">
        <div class="body">
          <a href="{{ action('AuthorizationController@role_edit', 0) }}" class="btn btn-info">Tambah</a>
          <br/><br/>

          <section class="datas">
            <div id="load" class="table-responsive">
              <table class="table m-b-0">
                  @if (count($datas)==0)
                      <thead>
                          <tr><th>Tidak ditemukan data</th></tr>
                      </thead>
                  @else
                      <thead>
                          <tr>
                              <th>Role</th>
                              <th>Daftar Akses Halaman</th>
                              <th></th>
                          </tr>
                      </thead>
                      <tbody>
                          @foreach($datas as $data)
                          <tr>
                                <td>{{ $data['name'] }}</td>
                                <td>
                                    <ul>
                                    @foreach($data->permissions as $permission)
                                        <li>{{ $permission['name'] }}</li>
                                    @endforeach
                                    </ul>
                                </td>
                                <td class="text-center">
                                    <a href="{{ action('AuthorizationController@role_edit', $data['id']) }}"><i class="icon-pencil text-info"></i></a>
                                </td>
                          </tr>
                          @endforeach
                      </tbody>
                  @endif
              </table>
            </div>
          </section>
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
        // data:  {
        //     role_name : '',
        //     id: 0,
        // },
        methods: {
            // saveDatas: function () {
            //     var self = this;
            //     $('#wait_progres').modal('show');
            //     $.ajaxSetup({
            //         headers: {
            //             'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            //         }
            //     })
            //     $.ajax({
            //         url : "{{ url('/authorization/role/') }}",
            //         method : 'post',
            //         dataType: 'json',
            //         data:{
            //             id: self.id,
            //             role_name: self.role_name,
            //         },
            //     }).done(function (data) {
            //         self.id = 0;
            //         self.role_name = '';
            //         $('#wait_progres').modal('hide');
            //         window.location.reload(false);
            //     }).fail(function (msg) {
            //         console.log(JSON.stringify(msg));
            //         $('#wait_progres').modal('hide');
            //     });
            // },
        }
    });

    // $('#btn-tambah').click(function(e) {
    //     e.preventDefault();
    //     $('#form_modal').modal('show');
    //     vm.id = 0;
    // });
    
    // $('#btn-edit').click(function(e) {
    //     e.preventDefault();
    //     $('#form_modal').modal('show');
    //     vm.id = $(this).data("id");
    //     vm.role_name = $(this).data("name");
    // });

    // $('#btn-submit').click(function() {
    //     vm.saveDatas();
    // });
</script>
@endsection
