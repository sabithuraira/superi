@extends('layouts.admin')

@section('breadcrumb')
    <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="icon-home"></i></a></li>
        <li class="breadcrumb-item">Tabel Ringkasan</li>
    </ul>
@endsection

@section('content')
    <div class="row clearfix">
        <div class="col-md-12">
            <div class="card">
                <div class="header">
                    <h2>Tabel Ringkasan PDRB Provinsi Sumatera Selatan</h2>
                </div>
                <div class="body">
                    <div class="row">
                        <div class="col">
                            <form id="form_filter" method="get" action="{{ url('pdrb_ringkasan1') }}">

                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-6">
                                        <select name="tabel_filter" id="tabel_filter" class="form-control"
                                            onchange="updateFormAction()">
                                            @foreach ($list_tabel as $key => $tbl)
                                                <option data-url="{{ $tbl['url'] }}" value="{{ $tbl['id'] }}"
                                                    data-id="{{ $tbl['id'] }}"
                                                    @if ($tbl['id'] == $tabel_filter) selected @endif>
                                                    {{ $tbl['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-sm-12 col-md-2">
                                        <select name="periode_filter" id="periode_filter" class="form-control"
                                            onchange="updateFormActionperiode()">
                                            @foreach ($list_periode as $key => $qtl)
                                                <option value="{{ $qtl }}" data-periode="{{ $qtl }}"
                                                    @if ($qtl == $periode_filter) selected @endif>
                                                    {{ $qtl }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-6 col-md-2 col-lg-2  d-grid gap-2 mx-auto">
                                        <button class="btn btn-success w-100" type="button"
                                            onclick="exportToExcel()">Export Excel</button>
                                    </div>
                                    <div class="form-group col-sm-6 col-md-2 col-lg-2  d-grid gap-2 mx-auto">
                                        <button class="btn btn-success w-100" type="button" onclick="export_all()">
                                            Export All
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            @foreach ($list_tabel as $tabel)
                                @if ($tabel['id'] == $tabel_filter)
                                    <p>{{ $tabel['name'] }}  <span class="text-muted font-italic"> (dalam persen)</span></p>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <div class="row">
                        <div class="col table-responsive">
                            <div class="table-responsive table-bordered" id="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr class="text-center">
                                            <th rowspan="2">Kabupaten/Kota</th>
                                            <th colspan="2">Pertumbuhan YoY</th>
                                            <th colspan="2">Pertumbuhan QtQ</th>
                                            <th colspan="2">Pertumbuhan CtC</th>
                                            <th rowspan="2">Implisit YoY {{ $periode_filter }}</th>
                                            <th rowspan="2">Share terhadap provinsi</th>
                                        </tr>
                                        <tr>
                                            @php
                                                $parts = explode('Q', $periode_filter);
                                                $tahun = isset($parts[0]) ? $parts[0] : null;
                                                $quarter = isset($parts[1]) ? 'Q' . $parts[1] : null;
                                            @endphp
                                            <th>
                                                {{ $tahun - 1 . $quarter }}
                                            </th>
                                            <th>
                                                {{ $periode_filter }}
                                            </th>
                                            <th>
                                                {{ $tahun - 1 . $quarter }}
                                            </th>
                                            <th>
                                                {{ $periode_filter }}
                                            </th>
                                            <th>
                                                {{ $tahun - 1 . $quarter }}
                                            </th>
                                            <th>
                                                {{ $periode_filter }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $dt)
                                            @php
                                                $shouldBold = $dt['id'] == '00';

                                            @endphp
                                            <tr
                                                style="@if ($shouldBold) background-color:#f2f2f2;font-weight: bold; @endif">
                                                <td style="@if ($shouldBold) font-weight: bold; @endif">
                                                    [16{{ $dt['id'] }}] {{ $dt['alias'] }}
                                                </td>
                                                <td>
                                                    {{ array_key_exists('yoy_prev', $dt) && $dt['yoy_prev'] ? round($dt['yoy_prev'], 2) : '' }}
                                                </td>
                                                <td>
                                                    {{ array_key_exists('yoy_current', $dt) && $dt['yoy_current'] ? round($dt['yoy_current'], 2) : '' }}
                                                </td>
                                                <td>
                                                    {{ array_key_exists('qtq_prev', $dt) && $dt['qtq_prev'] ? round($dt['qtq_prev'], 2) : '' }}
                                                </td>
                                                <td>
                                                    {{ array_key_exists('qtq_current', $dt) && $dt['qtq_current'] ? round($dt['qtq_current'], 2) : '' }}
                                                </td>
                                                <td>
                                                    {{ array_key_exists('ctc_prev', $dt) && $dt['ctc_prev'] ? round($dt['ctc_prev'], 2) : '' }}
                                                </td>
                                                <td>
                                                    {{ array_key_exists('ctc_current', $dt) && $dt['ctc_current'] ? round($dt['ctc_current'], 2) : '' }}
                                                </td>
                                                <td>{{ array_key_exists('implisit_yoy', $dt) && $dt['implisit_yoy'] ? round($dt['implisit_yoy'], 2) : '' }}
                                                </td>
                                                <td>{{ array_key_exists('share_prov', $dt) && $dt['share_prov'] ? round($dt['share_prov'], 2) : '' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript" src="{{ URL::asset('js/exportExcel.js') }}"></script>
    <script>
        var APP_URL = {!! json_encode(url('/')) !!}

        function updateFormAction() {
            var form = document.getElementById('form_filter');
            var tabel_option = document.getElementById('tabel_filter').options[document.getElementById('tabel_filter')
                .selectedIndex];
            var url = tabel_option.getAttribute('data-url');
            var data_id = tabel_option.getAttribute('data-id');
            document.getElementById("periode_filter").disabled = true;
            form.action = APP_URL + '/' + url + '/' + data_id;
            form.submit();
        }

        function updateFormActionperiode() {
            var form = document.getElementById('form_filter');
            var tabel_option = document.getElementById('tabel_filter').options[document.getElementById('tabel_filter')
                .selectedIndex];
            var url = tabel_option.getAttribute('data-url');
            var data_id = tabel_option.getAttribute('data-id');
            var periode_option = document.getElementById('periode_filter').options[document.getElementById('periode_filter')
                .selectedIndex];
            var periode = periode_option.getAttribute('data-periode')
            form.action = APP_URL + '/' + url + '/' + data_id + '?periode_filter=' + periode;
            console.log(form.action)
            form.submit();
        }
    </script>
@endsection
