@extends('layouts.admin')

@section('breadcrumb')
<ul class="breadcrumb">
    <li class="breadcrumb-item"><a href="#"><i class="icon-home"></i></a></li>
</ul>
@endsection

@section('content')
<div class="row clearfix">
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
