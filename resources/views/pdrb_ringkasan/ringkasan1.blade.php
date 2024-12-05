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
                                            onchange="updateFormAction(this)">
                                            @foreach ($list_tabel as $key => $tbl)
                                                <option data-url="{{ $tbl['url'] }}" value="{{ $tbl['id'] }}"
                                                    data-id="{{ $tbl['id'] }}"
                                                    @if ($tbl['id'] === $tabel_filter) selected @endif>
                                                    {{ $tbl['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-6 col-md-2 col-lg-2 d-grid gap-2 mx-auto">
                                        <button class="btn btn-primary w-100" type="button" href="#komponenModal"
                                            data-toggle="modal" data-target="#komponenModal">Pilih Komponen</button>
                                    </div>
                                    <div class="form-group col-sm-6 col-md-2 col-lg-2 d-grid gap-2 mx-auto">
                                        <button class="btn btn-primary w-100" type="button" href="#periodeModal"
                                            data-toggle="modal" data-target="#periodeModal">Pilih Periode</button>
                                    </div>
                                    <div class="form-group col-sm-6 col-md-2 col-lg-2  d-grid gap-2 mx-auto">
                                        <button class="btn btn-success w-100" type="button"
                                            onclick="exportToExcel()">Export Excel</button>
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
                            <div class="table-responsive ">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="text-center">
                                            <th rowspan="3">Komponen</th>
                                            @foreach ($periode_filter as $periode)
                                                <th colspan="6">{{ $periode }}</th>
                                            @endforeach
                                        </tr>
                                        <tr class="text-center">
                                            @foreach ($periode_filter as $periode)
                                                <th style="white-space:nowrap" colspan="2">Q-to-Q</th>
                                                <th style="white-space:nowrap" colspan="2">Y-on-Y</th>
                                                <th style="white-space:nowrap" colspan="2">C-to-C</th>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            @foreach ($periode_filter as $periode)
                                                @for ($i = 1; $i <= 3; $i++)
                                                    <th style="white-space:nowrap">Kabupaten</th>
                                                    <th style="white-space:nowrap">Provinsi</th>
                                                @endfor
                                            @endforeach

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $dt)
                                            @php
                                                $shouldBold =
                                                    strlen($dt['komponen']) < 4 || $dt['komponen'] == 'c_pdrb';
                                            @endphp
                                            <tr style="@if ($shouldBold) background-color:#f2f2f2; @endif">
                                                <td style="@if ($shouldBold) font-weight: bold; @endif">
                                                    {{ $dt['komponen_name'] }}
                                                </td>
                                                @foreach ($periode_filter as $periode)
                                                    @php
                                                        $qtqProv = $dt[$periode . 'qtq_prov'] ?? null;
                                                        $qtqKab = $dt[$periode . 'qtq_kab'] ?? null;
                                                        $yoyProv = $dt[$periode . 'yoy_prov'] ?? null;
                                                        $yoyKab = $dt[$periode . 'yoy_kab'] ?? null;
                                                        $ctcProv = $dt[$periode . 'ctc_prov'] ?? null;
                                                        $ctcKab = $dt[$periode . 'ctc_kab'] ?? null;
                                                        $shouldHighlight =
                                                            ($qtqProv && $qtqKab && $qtqProv * $qtqKab < 0) ||
                                                            ($yoyProv && $yoyKab && $yoyProv * $yoyKab < 0) ||
                                                            ($ctcProv && $ctcKab && $ctcProv * $ctcKab < 0);
                                                    @endphp
                                                    <td class="text-right"
                                                        style="@if ($shouldBold) font-weight: bold; @endif  @if ($shouldHighlight) background-color: yellow @endif">
                                                        {{ $qtqKab ? round($qtqKab, 2) : '' }}
                                                    </td>
                                                    <td class="text-right"
                                                        style="@if ($shouldBold) font-weight: bold; @endif  @if ($shouldHighlight) background-color: yellow @endif">
                                                        {{ array_key_exists($periode . 'qtq_prov', $dt) && $dt[$periode . 'qtq_prov'] ? round($dt[$periode . 'qtq_prov'], 2) : '' }}
                                                    </td>

                                                    <td class="text-right"
                                                        style="@if ($shouldBold) font-weight: bold; @endif  @if ($shouldHighlight) background-color: yellow @endif">
                                                        {{ array_key_exists($periode . 'yoy_kab', $dt) && $dt[$periode . 'yoy_kab'] ? round($dt[$periode . 'yoy_kab'], 2) : '' }}
                                                    </td>
                                                    <td class="text-right"
                                                        style="@if ($shouldBold) font-weight: bold; @endif  @if ($shouldHighlight) background-color: yellow @endif">
                                                        {{ array_key_exists($periode . 'yoy_prov', $dt) && $dt[$periode . 'yoy_prov'] ? round($dt[$periode . 'yoy_prov'], 2) : '' }}
                                                    </td>
                                                    <td class="text-right"
                                                        style="@if ($shouldBold) font-weight: bold; @endif  @if ($shouldHighlight) background-color: yellow @endif">
                                                        {{ array_key_exists($periode . 'ctc_kab', $dt) && $dt[$periode . 'ctc_kab'] ? round($dt[$periode . 'ctc_kab'], 2) : '' }}
                                                    </td>
                                                    <td class="text-right"
                                                        style="@if ($shouldBold) font-weight: bold; @endif  @if ($shouldHighlight) background-color: yellow @endif">
                                                        {{ array_key_exists($periode . 'ctc_prov', $dt) && $dt[$periode . 'ctc_prov'] ? round($dt[$periode . 'ctc_prov'], 2) : '' }}
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
            var url = selectedOption.getAttribute('data-url');
            var data_id = selectedOption.getAttribute('data-id');
            form.action = window.origin + '/superi/public/' + url + '/' + data_id;
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
