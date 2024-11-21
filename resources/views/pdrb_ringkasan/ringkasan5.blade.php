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
                                            @foreach ($list_wilayah as $wil)
                                                <option value="{{ $wil['id'] }}" data-id="{{ $wil['id'] }}"
                                                    @if ($wil == $wilayah_filter) selected @endif>
                                                    {{ $wil['id'] }} - {{ $wil['alias'] }}</option>
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
                                        <button class="btn btn-success w-100" type="button">Export Excel</button>
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
                                            <tr class="text-right">
                                                <td class="text-left">{{ $dt['name'] }}</td>
                                                <td>
                                                    {{ array_key_exists('yoy', $dt) ? round($dt['yoy'], 2) : 'N/A' }}
                                                </td>
                                                <td>
                                                    {{ array_key_exists('qtq', $dt) ? round($dt['qtq'], 2) : 'N/A' }}
                                                </td>
                                                <td>
                                                    {{ array_key_exists('ctc', $dt) ? round($dt['ctc'], 2) : 'N/A' }}
                                                </td>
                                                <td>
                                                    {{ array_key_exists('implisit_yoy', $dt) ? round($dt['implisit_yoy'], 2) : 'N/A' }}
                                                </td>
                                                <td>
                                                    {{ array_key_exists('implisit_qtq', $dt) ? round($dt['implisit_qtq'], 2) : 'N/A' }}
                                                </td>
                                                <td>
                                                    {{ array_key_exists('implisit_ctc', $dt) ? round($dt['implisit_ctc'], 2) : 'N/A' }}
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
            var periode_option = document.getElementById('periode_filter').options[document.getElementById(
                'periode_filter').selectedIndex];
            var periode = periode_option.getAttribute('data-periode')
            var wilayah_option = document.getElementById(
                'wilayah_filter').options[document.getElementById('wilayah_filter').selectedIndex];
            var wilayah = wilayah_option.getAttribute('data-id');
            form.action = window.origin + '/superi/public/' + url + '/' + data_id + '?periode_filter=' +
                periode + '?wilayah_filter=' + wilayah;
            console.log(form.action)
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
            form.action = window.origin + '/superi/public/' + url + '/' + data_id + '?periode_filter=' + periode +
                '?wilayah_filter=' + wilayah;
            console.log(form.action)
            form.submit();
        }
    </script>
@endsection
