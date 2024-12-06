@extends('layouts.admin')

@section('breadcrumb')
<ul class="breadcrumb">
    <li class="breadcrumb-item"><a href="#"><i class="icon-home"></i></a></li>
</ul>
@endsection

@section('content')
<div class="row clearfix">
    <div class="col-md-12">
        <div class="card">
            <div class="header">
                <div class="row align-items-center">
                    <div class="col-auto mr-auto aligns">
                        <h2>BERANDA</h2>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-success btn-sm" type="button" onclick="exportToExcel()">Export Excel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
