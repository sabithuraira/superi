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
                                        <select name="wilayah_filter" id="wilayah_filter" class="form-control"
                                            onchange="updateFormActionWilayah()">
                                            @foreach ($list_wilayah as $wil_id => $wil)
                                                <option value="{{ $wil_id }}" data-id="{{ $wil_id }}"
                                                    @if ($wil_id == $wilayah_filter) selected @endif>
                                                    16{{ $wil_id }} - {{ $wil }}</option>
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
                                    <div class="form-group col-sm-6 col-md-2 ">
                                        <button class="btn btn-success w-100 mb-2" type="button"
                                            onclick="exportToExcel()">Export Excel</button>
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
                                    <p>{{ $tabel['name'] }} <span class="text-muted font-italic"> (dalam persen)</span></p>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <div class="row">
                        <div class="col table-responsive">
                            <div class="table-responsive table-bordered" id="table-responsive">
                                <table class="table" border="1px solid black">
                                    <thead>
                                        <tr class="text-center">
                                            <th rowspan="2">Komponen</th>
                                            <th colspan="2">Pertumbuhan YoY</th>
                                            <th colspan="2">Pertumbuhan QtQ</th>
                                            <th colspan="2">Pertumbuhan CtC</th>
                                            <th colspan="2">Implisit YoY</th>
                                            <th colspan="2">Implisit QtQ</th>
                                            <th colspan="2">Implisit CtC</th>
                                        </tr>
                                        <tr>
                                            <td>Rilis</td>
                                            <td>Revisi</td>
                                            <td>Rilis</td>
                                            <td>Revisi</td>
                                            <td>Rilis</td>
                                            <td>Revisi</td>
                                            <td>Rilis</td>
                                            <td>Revisi</td>
                                            <td>Rilis</td>
                                            <td>Revisi</td>
                                            <td>Rilis</td>
                                            <td>Revisi</td>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $dt)
                                            @php
                                                $shouldBold = strlen($dt['id']) < 4 || $dt['id'] == 'c_pdrb';
                                            @endphp
                                            <tr class="text-right"
                                                style="@if ($shouldBold) background-color:#f2f2f2; font-weight: bold; @endif">
                                                <td class="text-left">
                                                    {{ $dt['name'] }}
                                                </td>
                                                <td
                                                    style="@if (abs($dt['yoy_rilis'] - $dt['yoy_revisi']) > 1) background-color: orange @endif
                                                    @if ($dt['yoy_rilis'] * $dt['yoy_revisi'] < 0) background-color: yellow @endif">
                                                    {{ array_key_exists('yoy_rilis', $dt) && $dt['yoy_rilis'] ? number_format(round($dt['yoy_rilis'], 2), 2, ',', '.') : '' }}
                                                </td>
                                                <td
                                                    style="@if (abs($dt['yoy_rilis'] - $dt['yoy_revisi']) > 1) background-color: orange @endif
                                                     @if ($dt['yoy_rilis'] * $dt['yoy_revisi'] < 0) background-color: yellow @endif">
                                                    {{ array_key_exists('yoy_revisi', $dt) && $dt['yoy_revisi'] ? number_format(round($dt['yoy_revisi'], 2), 2, ',', '.') : '' }}
                                                </td>
                                                <td
                                                    style="@if (abs($dt['qtq_rilis'] - $dt['qtq_revisi']) > 1) background-color: orange @endif
                                                     @if ($dt['qtq_rilis'] * $dt['qtq_revisi'] < 0) background-color: yellow @endif">
                                                    {{ array_key_exists('qtq_rilis', $dt) && $dt['qtq_rilis'] ? number_format(round($dt['qtq_rilis'], 2), 2, ',', '.') : '' }}
                                                </td>
                                                <td
                                                    style="@if (abs($dt['qtq_rilis'] - $dt['qtq_revisi']) > 1) background-color: orange @endif
                                                     @if ($dt['qtq_rilis'] * $dt['qtq_revisi'] < 0) background-color: yellow @endif">
                                                    {{ array_key_exists('qtq_revisi', $dt) && $dt['qtq_revisi'] ? number_format(round($dt['qtq_revisi'], 2), 2, ',', '.') : '' }}
                                                </td>
                                                <td
                                                    style="@if (abs($dt['ctc_rilis'] - $dt['ctc_revisi']) > 1) background-color: orange @endif
                                                     @if ($dt['ctc_rilis'] * $dt['ctc_revisi'] < 0) background-color: yellow @endif">
                                                    {{ array_key_exists('ctc_rilis', $dt) && $dt['ctc_rilis'] ? number_format(round($dt['ctc_rilis'], 2), 2, ',', '.') : '' }}
                                                </td>
                                                <td
                                                    style="@if (abs($dt['ctc_rilis'] - $dt['ctc_revisi']) > 1) background-color: orange @endif
                                                     @if ($dt['ctc_rilis'] * $dt['ctc_revisi'] < 0) background-color: yellow @endif">
                                                    {{ array_key_exists('ctc_revisi', $dt) && $dt['ctc_revisi'] ? number_format(round($dt['ctc_revisi'], 2), 2, ',', '.') : '' }}
                                                </td>
                                                <td
                                                    style="@if (abs($dt['implisit_yoy_rilis'] - $dt['implisit_yoy_revisi']) > 1) background-color: orange @endif
                                                     @if ($dt['implisit_yoy_rilis'] * $dt['implisit_yoy_revisi'] < 0) background-color: yellow @endif">
                                                    {{ array_key_exists('implisit_yoy_rilis', $dt) && $dt['implisit_yoy_rilis'] ? number_format(round($dt['implisit_yoy_rilis'], 2), 2, ',', '.') : '' }}
                                                </td>
                                                <td
                                                    style="@if (abs($dt['implisit_yoy_rilis'] - $dt['implisit_yoy_revisi']) > 1) background-color: orange @endif
                                                     @if ($dt['implisit_yoy_rilis'] * $dt['implisit_yoy_revisi'] < 0) background-color: yellow @endif">
                                                    {{ array_key_exists('implisit_yoy_revisi', $dt) && $dt['implisit_yoy_revisi'] ? number_format(round($dt['implisit_yoy_revisi'], 2), 2, ',', '.') : '' }}
                                                </td>
                                                <td
                                                    style="@if (abs($dt['implisit_qtq_rilis'] - $dt['implisit_qtq_revisi']) > 1) background-color: orange @endif
                                                     @if ($dt['implisit_qtq_rilis'] * $dt['implisit_qtq_revisi'] < 0) background-color: yellow @endif">
                                                    {{ array_key_exists('implisit_qtq_rilis', $dt) && $dt['implisit_qtq_rilis'] ? number_format(round($dt['implisit_qtq_rilis'], 2), 2, ',', '.') : '' }}
                                                </td>
                                                <td
                                                    style="@if (abs($dt['implisit_qtq_rilis'] - $dt['implisit_qtq_revisi']) > 1) background-color: orange @endif
                                                     @if ($dt['implisit_qtq_rilis'] * $dt['implisit_qtq_revisi'] < 0) background-color: yellow @endif">
                                                    {{ array_key_exists('implisit_qtq_revisi', $dt) && $dt['implisit_qtq_revisi'] ? number_format(round($dt['implisit_qtq_revisi'], 2), 2, ',', '.') : '' }}
                                                </td>
                                                <td
                                                    style="@if (abs($dt['implisit_ctc_rilis'] - $dt['implisit_ctc_revisi']) > 1) background-color: orange @endif
                                                     @if ($dt['implisit_ctc_rilis'] * $dt['implisit_ctc_revisi'] < 0) background-color: yellow @endif">
                                                    {{ array_key_exists('implisit_ctc_rilis', $dt) && $dt['implisit_ctc_rilis'] ? number_format(round($dt['implisit_ctc_rilis'], 2), 2, ',', '.') : '' }}
                                                </td>
                                                <td
                                                    style="@if (abs($dt['implisit_ctc_rilis'] - $dt['implisit_ctc_revisi']) > 1) background-color: orange @endif
                                                     @if ($dt['implisit_ctc_rilis'] * $dt['implisit_ctc_revisi'] < 0) background-color: yellow @endif">
                                                    {{ array_key_exists('implisit_ctc_revisi', $dt) && $dt['implisit_ctc_revisi'] ? number_format(round($dt['implisit_ctc_revisi'], 2), 2, ',', '.') : '' }}
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
            var wilayah_option = document.getElementById(
                'wilayah_filter').options[document.getElementById('wilayah_filter').selectedIndex];
            var wilayah = wilayah_option.getAttribute('data-id');
            form.action = APP_URL + '/' + url + '/' + data_id + '?periode_filter=' +
                periode + '?wilayah_filter=' + wilayah;
            form.submit();
        }


        function updateFormActionWilayah() {
            var form = document.getElementById('form_filter');
            var tabel_option = document.getElementById('tabel_filter').options[document.getElementById('tabel_filter')
                .selectedIndex];
            var url = tabel_option.getAttribute('data-url');
            var data_id = tabel_option.getAttribute('data-id');
            var periode_option = document.getElementById('periode_filter').options[document.getElementById('periode_filter')
                .selectedIndex];
            var periode = periode_option.getAttribute('data-periode');
            var wilayah_option = document.getElementById('wilayah_filter').options[document.getElementById('wilayah_filter')
                .selectedIndex];
            var wilayah = wilayah_option.getAttribute('data-id');
            form.action = APP_URL + '/' + url + '/' + data_id + '?periode_filter=' + periode +
                '?wilayah_filter=' + wilayah;
            form.submit();
        }
    </script>
@endsection
