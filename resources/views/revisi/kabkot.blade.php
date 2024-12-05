@extends('layouts.admin')

@section('breadcrumb')
    <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="icon-home"></i></a></li>
        <li class="breadcrumb-item">Arah Revisi Provinsi (PKRT 12 Komponen)</li>
    </ul>
@endsection

@section('content')
    <div class="row clearfix">
        <div class="col-md-12">
            <div class="card">
                <div class="header">
                    <h2>Arah Revisi Provinsi (PKRT 12 Komponen)</h2>
                </div>
                <div class="body">
                    <div class="row">
                        <div class="col">
                            <form id="form_filter" method="get" action="{{ url('revisi_kabkot') }}">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-4">
                                        <select name="tabel_filter" id="tabel_filter" class="form-control"
                                            onchange="updateFormAction(this)">
                                            @foreach ($list_tabel as $key => $tbl)
                                                <option value="{{ $tbl['id'] }}" data-id="{{ $tbl['id'] }}"
                                                    @if ($tbl['id'] === $tabel_filter) selected @endif>
                                                    {{ $tbl['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-6 col-md-2 d-grid ">
                                        <button class="btn btn-primary w-100 " type="button" href="#komponenModal"
                                            data-toggle="modal" data-target="#komponenModal">Pilih Komponen</button>
                                    </div>

                                    <div class="form-group col-sm-6 col-md-2 d-grid ">
                                        <button class="btn btn-success w-100" type="button"onclick="exportToExcel()">
                                            Export Excel
                                        </button>
                                    </div>
                                    <div class="form-group col-sm-6 col-md-2 d-grid ">
                                        <a class="btn btn-primary w-100" type="button"
                                            href="{{ url('pdrb_kabkot_7pkrt/3.1') }}">PKRT
                                            7 Komponen</a>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-4">
                                        <select name="wilayah_filter" id="wilayah_filter" class="form-control"
                                            onchange="updateFormActionWilayah()">
                                            @foreach ($list_wilayah as $key => $wil)
                                                <option value="{{ $key }}" data-id="{{ $key }}"
                                                    @if ($key == $wilayah_filter) selected @endif>
                                                    {{ $key }} - {{ $wil }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-6 col-md-2   ">
                                        <button class="btn btn-primary w-100" type="button" href="#periodeModal"
                                            data-toggle="modal" data-target="#periodeModal">Pilih Periode</button>
                                    </div>
                                    <div class="col-sm-6 col-md-2"></div>
                                    <div class="form-group col-sm-6 col-md-2 d-grid ">
                                        <a class="btn btn-primary w-100" type="button"
                                            href="{{ url('pdrb_kabkot_rilis/3.1') }}">Tabel Rilis</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            @foreach ($list_tabel as $tabel)
                                @if ($tabel['id'] === $tabel_filter)
                                    <p>{{ $tabel['name'] }}</p>
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
                                            @foreach ($periode_filter as $periode)
                                                <th colspan="3">{{ $periode }}</th>
                                            @endforeach
                                        </tr>
                                        <tr class="text-center">
                                            @foreach ($periode_filter as $periode)
                                                <th>Rilis</th>
                                                <th>Revisi</th>
                                                <th>Arah</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $dt)
                                            @php
                                                $shouldBold = strlen($dt['id']) < 4 || $dt['id'] == 'c_pdrb';
                                            @endphp
                                            <tr
                                                style="@if ($shouldBold) background-color:#f2f2f2; font-weight: bold; @endif">

                                                <td>
                                                    {{ $dt['id'] }} {{ $dt['name'] }}
                                                </td>

                                                @foreach ($periode_filter as $periode)
                                                    @php
                                                        $rilis = $dt[$periode . '_rilis'] ?? null;
                                                        $revisi = $dt[$periode . '_revisi'] ?? null;
                                                    @endphp

                                                    <td class="text-right">
                                                        {{ $rilis != null ? number_format(round($rilis, 2), 2, ',', '.') : '' }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ $revisi != null ? number_format(round($revisi, 2), 2, ',', '.') : '' }}
                                                    </td>
                                                    <td>
                                                        @if ($rilis < $revisi)
                                                            <div class="text-danger">▼</div>
                                                        @elseif ($rilis > $revisi)
                                                            <div class="text-success">▲</div>
                                                        @else
                                                            <div class="text-warning">═</div>
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal fade" id="komponenModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="title" id="komponenModalLabel">Pilih Komponen</h4>
                            </div>
                            <form method="GET" action="">
                                <div class="modal-body">
                                    <select name="tabel_filter" id="tabel_filter" class="form-control" hidden>
                                        @foreach ($list_tabel as $key => $tbl)
                                            <option
                                                value="{{ $tbl['id'] }} "@if ($tbl['id'] == $tabel_filter) selected @endif>
                                                {{ $tbl['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @foreach ($list_group_komponen as $i => $kmp)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="{{ $kmp['column'] }}"
                                                name="komponen_filter[]" id="{{ 'komponen_filter' . $i }}"
                                                @foreach ($komponen_filter as $kom_fil)
                                                    @if ($kom_fil == $kmp['column'])
                                                    checked
                                                    @endif @endforeach>
                                            <label class="form-check-label" for="{{ 'komponen_filter' . $i }}">
                                                {{ $kmp['name'] }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-success">OK</button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="periodeModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="title" id="periodeModalLabel">Pilih Periode</h4>
                            </div>
                            <form method="GET" action="">
                                <div class="modal-body mx-4">
                                    <select name="tabel_filter" id="tabel_filter" class="form-control" hidden>
                                        @foreach ($list_tabel as $key => $tbl)
                                            <option
                                                value="{{ $tbl['id'] }} "@if ($tbl['id'] == $tabel_filter) selected @endif>
                                                {{ $tbl['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @for ($i = 2021; $i <= 2024; $i++)
                                        <div class ="row">
                                            @for ($q = 1; $q <= 4; $q++)
                                                <div class="form-check col-2">
                                                    <input class="form-check-input" type="checkbox"
                                                        value="{{ $i . 'Q' . $q }}" name="periode_filter[]"
                                                        id="{{ 'periode_filter_' . $i . 'Q' . $q }}"
                                                        @foreach ($periode_filter as $per_fil)
                                                    @if ($per_fil === $i . 'Q' . $q)
                                                    checked
                                                    @endif @endforeach>
                                                    <label class="form-check-label"
                                                        for="{{ 'periode_filter_' . $i . 'Q' . $q }}">
                                                        {{ $i . 'Q' . $q }}
                                                    </label>
                                                </div>
                                            @endfor
                                            <div class="form-check col-2">
                                                <input class="form-check-input" type="checkbox"
                                                    value="{{ $i }}" name="periode_filter[]"
                                                    id="{{ 'periode_filter_' . $i }}"
                                                    @foreach ($periode_filter as $per_fil)
                                                    @if ($per_fil === (string) $i)
                                                    checked
                                                    @endif @endforeach>
                                                <label class="form-check-label" for="{{ 'periode_filter_' . $i }}">
                                                    {{ $i }}
                                                </label>
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-success">OK</button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function updateFormAction(selectElement) {
            var form = document.getElementById('form_filter');
            var selectedOption = selectElement.options[selectElement.selectedIndex];
            var data_id = selectedOption.getAttribute('data-id');
            form.action = window.origin + '/superi/public/revisi_kabkot' + '/' + data_id;
            form.submit();
        }

        function updateFormActionWilayah() {
            var form = document.getElementById('form_filter');
            var tabel_option = document.getElementById('tabel_filter').options[document.getElementById('tabel_filter')
                .selectedIndex];
            var data_id = tabel_option.getAttribute('data-id');

            var wilayah_option = document.getElementById('wilayah_filter').options[document.getElementById('wilayah_filter')
                .selectedIndex];
            var wilayah = wilayah_option.getAttribute('data-id');

            form.action = window.origin + '/superi/public/revisi_kabkot' + '/' + data_id + '?wilayah_filter=' + wilayah;
            console.log(form.action)
            form.submit();
        }

        function exportToExcel() {
            var location = 'data:application/vnd.ms-excel;base64,';
            var excelTemplate = '<html> ' +
                '<head> ' +
                '<meta http-equiv="content-type" content="text/plain; charset=UTF-8"/> ' +
                '</head> ' +
                '<body> ' +
                document.getElementById("table-responsive").innerHTML +
                '</body> ' +
                '</html>'
            window.location.href = location + window.btoa(excelTemplate);
        }
    </script>
@endsection
