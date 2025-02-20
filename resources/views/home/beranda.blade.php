@extends('layouts.admin')

@section('breadcrumb')
<ul class="breadcrumb">
    <li class="breadcrumb-item"><a href="#"><i class="icon-home"></i> Beranda</a></li>
</ul>
@endsection

@section('content')
<div class="row clearfix" id="app_vue">
    <div class="col-md-12">
        <div class="container-fluid">
            <div class="row clearfix">
                <div class="col-lg-3 col-md-6">
                    <div class="card overflowhidden">
                        <div class="body text-center">
                            <div class="p-15">
                                <h3>109</h3>
                                <span>Today Works</span>
                            </div>                            
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card overflowhidden">
                        <div class="body text-center">
                            <div class="p-15">
                                <h3>{{ $data_resume['yony'] }}</h3>
                                <span>Y-ON-Y</span>
                            </div>                            
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card overflowhidden">
                        <div class="body text-center">
                            <div class="p-15">
                                <h3>{{ $data_resume['qtoq'] }}</h3>
                                <span>Q-TO-Q</span>
                            </div>                           
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card overflowhidden">
                        <div class="body text-center">
                            <div class="p-15">
                                <h3>{{ $data_resume['ctoc'] }}</h3>
                                <span>C-TO-C</span>
                            </div>                            
                        </div>
                    </div>
                </div>
            </div>

            <div class="row clearfix mb-2 float-right">
                <button v-if="isAllApproveProvinsi && !isAllApproveAdmin" type="button" class="btn btn-outline-success mx-1" >Semua Data Di Approve Provinsi</button>
                <button v-if="!isAllApproveProvinsi" type="button" class="btn btn-outline-secondary mx-1">Menunggu Approve Provinsi</button>

                
                <form method="post" action="{{ url('upload/approve_admin') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row clearfix">
                        <div class="col-lg-6">
                            <button v-if="isAllApproveProvinsi && !isAllApproveAdmin" class="btn btn-success float-left" type="submit" name="action" value="1"><i class="fa fa-thumbs-o-up"></i>&nbsp; Approve Admin</button>
                            <button v-if="isAllApproveAdmin" class="btn btn-danger float-left" type="submit" name="action" value="2"><i class="fa fa-thumbs-o-down"></i>&nbsp; Batalkan Approve Admin</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="header">
                <div class="row align-items-center">
                    <div class="col-auto mr-auto aligns">
                        <h2>BERANDA</h2>
                    </div>
                </div>
            </div>

            <div class="body">
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
    <meta name="_token" content="{{ csrf_token() }}" />
    <meta name="csrf-token" content="@csrf">
@endsection

@section('scripts')
    <script type="text/javascript" src="{{ URL::asset('js/app.js') }}"></script>
    <script>
        var vm = new Vue({
            el: "#app_vue",
            data: {
                isAllApproveProvinsi: false,
                isAllApproveAdmin: false,
            },
            methods: {
                setDatas: function(event) {
                    var self = this;
                    $('#wait_progres').modal('show');

                    $.ajaxSetup({
                        headers: { 'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content') }
                    })

                    $.ajax({
                        url: "{{ url('/upload/is_all_approve/') }}",
                        method: 'post',
                        dataType: 'json',
                        // data: {
                        //     wilayah: self.form_data.wilayah,
                        //     tahun: self.form_data.tahun,
                        // },   
                    }).done(function(data) {
                        self.isAllApproveProvinsi = data.resultProvinsi;
                        self.isAllApproveAdmin = data.resultAdmin;
                        $('#wait_progres').modal('hide');
                    }).fail(function(msg) {
                        console.log(JSON.stringify(msg));
                        $('#wait_progres').modal('hide');
                    });
                },
            }
        });

        $(document).ready(function() {
            vm.setDatas();
        });
    </script>
@endsection
