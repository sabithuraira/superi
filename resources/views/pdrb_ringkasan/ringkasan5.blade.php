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
                            <form id="form_filter" method="get">

                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-6">
                                        <select id="tabel_filter" class="form-control" onchange="updateFormAction()">
                                            @foreach ($list_tabel as $key => $tbl)
                                                <option data-url="{{ $tbl['url'] }}" value="{{ $tbl['id'] }}" data-id="{{ $tbl['id'] }}"
                                                    @if ($tbl['id'] == $id) selected @endif>
                                                    {{ $tbl['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-2">
                                        <select id="wilayah_filter" class="form-control" onchange="updateFormActionWilayah(this)">
                                            @foreach ($list_wilayah as $wil_id => $wil)
                                                <option value="{{ $wil_id }}" data-id="{{ $wil_id }}"
                                                    @if ($wil_id == $wilayah_filter) selected @endif>
                                                    16{{ $wil_id }} - {{ $wil }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-12 col-md-2">
                                        <select id="periode_filter" class="form-control" onchange="updateFormActionperiode(this)">
                                            @foreach ($list_periode as $key => $qtl)
                                                <option value="{{ $qtl }}" data-periode="{{ $qtl }}"
                                                    @if ($qtl == $periode_filter) selected @endif>
                                                    {{ $qtl }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-6 col-md-2 ">
                                        <button class="btn btn-success w-100 mb-2" type="button" onclick="exportToExcel()">Export Excel</button>
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
                                @if ($tabel['id'] == $id)
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
                                            <th>Komponen</th>
                                            <th>Pertumbuhan YoY</th>
                                            <th>Pertumbuhan QtQ</th>
                                            <th>Pertumbuhan CtC</th>
                                            <th>Implisit YoY</th>
                                            <th>Implisit QtQ</th>
                                            <th>Implisit CtC</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $dt)
                                            @php
                                                $shouldBold = strlen($dt['id']) < 4 || $dt['id'] == 'c_pdrb';
                                            @endphp
                                            <tr class="text-right"
                                                style="@if ($shouldBold) background-color:#f2f2f2; font-weight: bold; @endif">
                                                <td class="text-left">{{ $dt['name'] }}</td>
                                                <td style="@if (abs($dt['yoy']) >= 5) background-color: yellow @endif">
                                                    {{ array_key_exists('yoy', $dt) && $dt['yoy'] ? number_format(round($dt['yoy'], 2), 2, ',', '.') : '' }}
                                                </td>
                                                <td style="@if (abs($dt['qtq']) >= 5) background-color: yellow @endif">
                                                    {{ array_key_exists('qtq', $dt) && $dt['qtq'] ? number_format(round($dt['qtq'], 2), 2, ',', '.') : '' }}
                                                </td>
                                                <td style="@if (abs($dt['ctc']) >= 5) background-color: yellow @endif">
                                                    {{ array_key_exists('ctc', $dt) && $dt['ctc'] ? number_format(round($dt['ctc'], 2), 2, ',', '.') : '' }}
                                                </td>
                                                <td style="@if (abs($dt['implisit_yoy']) >= 5) background-color: yellow @endif">
                                                    {{ array_key_exists('implisit_yoy', $dt) && $dt['implisit_yoy'] ? number_format(round($dt['implisit_yoy'], 2), 2, ',', '.') : '' }}
                                                </td>
                                                <td style="@if (abs($dt['implisit_qtq']) >= 5) background-color: yellow @endif">
                                                    {{ array_key_exists('implisit_qtq', $dt) && $dt['implisit_qtq'] ? number_format(round($dt['implisit_qtq'], 2), 2, ',', '.') : '' }}
                                                </td>
                                                <td style="@if (abs($dt['implisit_ctc']) >= 5) background-color: yellow @endif">
                                                    {{ array_key_exists('implisit_ctc', $dt) && $dt['implisit_ctc'] ? number_format(round($dt['implisit_ctc'], 2), 2, ',', '.') : '' }}
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

        function updateFormActionperiode(selectElement) {
            const pathParts = window.location.pathname.split('/').filter(Boolean);
            const routeName = pathParts[pathParts.length - 2];
            const table_id = pathParts[pathParts.length - 1];
            const appBase = "{{ url('/') }}";
            const baseUrl = `${appBase}/${routeName}/${table_id}`;

            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const data_id = selectedOption.value;

            let newUrl = `${baseUrl}?periode_filter=${data_id}`;
            const params = new URLSearchParams(window.location.search);
            const wilayah_id = params.getAll('wilayah_filter');
            newUrl = newUrl += `&wilayah_filter=${wilayah_id}`;
            window.location.href = newUrl;
        }

        function updateFormActionWilayah(selectElement) {
            const pathParts = window.location.pathname.split('/').filter(Boolean);
            const routeName = pathParts[pathParts.length - 2];
            const table_id = pathParts[pathParts.length - 1];
            const appBase = "{{ url('/') }}";
            const baseUrl = `${appBase}/${routeName}/${table_id}`;

            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const data_id = selectedOption.value;

            let newUrl = `${baseUrl}?wilayah_filter=${data_id}`;
            const params = new URLSearchParams(window.location.search);
            const periode_id = params.getAll('periode_filter');
            newUrl = newUrl += `&periode_filter=${periode_id}`;
            window.location.href = newUrl;
        }
    </script>
@endsection
