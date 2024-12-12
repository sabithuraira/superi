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
                                @csrf
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
                                                    @if ($wil == $wilayah_filter) selected @endif>
                                                    {{ $wil_id }} - {{ $wil }}</option>
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
                                    <p>{{ $tabel['name'] }}</p>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <div class="row">
                        <div class="col table-responsive">
                            <div class="table-responsive">
                                <table class="table">
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
                                                <td>
                                                    {{ array_key_exists('yoy_rilis', $dt) && $dt['yoy_rilis'] ? round($dt['yoy_rilis'], 2) : '' }}
                                                </td>
                                                <td>
                                                    {{ array_key_exists('yoy_revisi', $dt) && $dt['yoy_revisi'] ? round($dt['yoy_revisi'], 2) : '' }}
                                                </td>
                                                <td>
                                                    {{ array_key_exists('qtq_rilis', $dt) && $dt['qtq_rilis'] ? round($dt['qtq_rilis'], 2) : '' }}
                                                </td>
                                                <td>
                                                    {{ array_key_exists('qtq_revisi', $dt) && $dt['qtq_revisi'] ? round($dt['qtq_revisi'], 2) : '' }}
                                                </td>
                                                <td>
                                                    {{ array_key_exists('ctc_rilis', $dt) && $dt['ctc_rilis'] ? round($dt['ctc_rilis'], 2) : '' }}
                                                </td>
                                                <td>
                                                    {{ array_key_exists('ctc_revisi', $dt) && $dt['ctc_revisi'] ? round($dt['ctc_revisi'], 2) : '' }}
                                                </td>
                                                <td>
                                                    {{ array_key_exists('implisit_yoy_rilis', $dt) && $dt['implisit_yoy_rilis'] ? round($dt['implisit_yoy_rilis'], 2) : '' }}
                                                </td>
                                                <td>
                                                    {{ array_key_exists('implisit_yoy_revisi', $dt) && $dt['implisit_yoy_revisi'] ? round($dt['implisit_yoy_revisi'], 2) : '' }}
                                                </td>
                                                <td>
                                                    {{ array_key_exists('implisit_qtq_rilis', $dt) && $dt['implisit_qtq_rilis'] ? round($dt['implisit_qtq_rilis'], 2) : '' }}
                                                </td>
                                                <td>
                                                    {{ array_key_exists('implisit_qtq_revisi', $dt) && $dt['implisit_qtq_revisi'] ? round($dt['implisit_qtq_revisi'], 2) : '' }}
                                                </td>
                                                <td>
                                                    {{ array_key_exists('implisit_ctc_rilis', $dt) && $dt['implisit_ctc_rilis'] ? round($dt['implisit_ctc_rilis'], 2) : '' }}
                                                </td>
                                                <td>
                                                    {{ array_key_exists('implisit_ctc_revisi', $dt) && $dt['implisit_ctc_revisi'] ? round($dt['implisit_ctc_revisi'], 2) : '' }}
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
    <script>
        function updateFormAction() {
            var form = document.getElementById('form_filter');
            var tabel_option = document.getElementById('tabel_filter').options[document.getElementById('tabel_filter')
                .selectedIndex];
            var url = tabel_option.getAttribute('data-url');
            var data_id = tabel_option.getAttribute('data-id');
            document.getElementById("periode_filter").disabled = true;
            form.action = window.origin + '/superi/public/' + url + '/' + data_id;
            console.log(form.action)
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
            form.action = window.origin + '/superi/public/' + url + '/' + data_id + '?periode_filter=' + periode;
            console.log(form.action)
            form.submit();
        }

        function export_all() {
            const url = new URL(window.location.href);
            const periode_filter = url.searchParams.get('periode_filter');
            let currentUrl = window.location.origin;
            let newUrl = `${currentUrl}/superi/public/pdrb_ringkasan_export_all`;
            if (periode_filter) {
                newUrl += `?periode_filter=${periode_filter}`;
            }
            console.log(newUrl)
            window.open(newUrl, '_blank');
        }
    </script>
@endsection
