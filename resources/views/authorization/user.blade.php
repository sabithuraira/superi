@extends('layouts.admin')

@section('breadcrumb')
<ul class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon-home"></i></a></li>                     
    <li class="breadcrumb-item">Authorization User Role</li>
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
          <form action="{{url('authorization/user')}}" method="get">
            <div class="input-group mb-3">
                @csrf
                <input type="text" class="form-control" name="search" id="search" value="{{ $keyword }}" placeholder="Search..">

                <div class="input-group-append">
                    <button class="btn btn-info" type="submit"><i class="fa fa-search"></i></button>
                </div>
            </div>
          </form>

          <section class="datas">
            @include('authorization.user_list')
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
        methods: {
        }
    });
</script>
@endsection
