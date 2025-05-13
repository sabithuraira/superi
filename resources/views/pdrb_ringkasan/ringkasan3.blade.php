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
                                    <div class="form-group col-sm-12 col-md-2">
                                        <button class="btn btn-primary w-100" type="button" href="#komponenModal"
                                            data-toggle="modal" data-target="#komponenModal">Pilih Komponen</button>
                                    </div>
                                    <div class="form-group col-sm-6 col-md-2 col-lg-2  d-grid gap-2 mx-auto">
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
                                @if ($tabel['id'] === $tabel_filter)
                                    <p>{{ $tabel['name'] }} <span class="text-muted font-italic"> (dalam persen)</span></p>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <div class="row">
                        <div class="col table-responsive">
                            <div class="table-responsive table-bordered table-striped " id="table-responsive">
                                <table class="table" border="1px solid black">
                                    <thead>
                                        <tr class="text-center">
                                            <th>Kabupaten/Kota</th>
                                            @foreach ($komponens as $komp)
                                                <th>{{ $komp['alias'] }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $dt)
                                            @if ($dt['id'] == '99')
                                                @continue
                                            @endif
                                            @php
                                                $shouldBold = $dt['id'] == '00';
                                            @endphp
                                            <tr style="@if ($shouldBold) background-color:#f2f2f2; @endif">
                                                <td style="@if ($shouldBold) font-weight: bold; @endif">
                                                    [16{{ $dt['id'] }}] {{ $dt['alias'] }}
                                                </td>
                                                @foreach ($komponens as $key => $komp)
                                                    <td style="@if ($shouldBold) font-weight: bold; @endif"
                                                        class="text-right">
                                                        {{ array_key_exists($komp['id'], $dt) && $dt[$komp['id']]
                                                            ? number_format(round($dt[$komp['id']], 2), 2, ',', '.')
                                                            : '' }}
                                                    </td>
                                                @endforeach


                                            </tr>
                                            @if ($dt['id'] == '00')
                                                <tr>
                                                    <td style="@if ($shouldBold) font-weight: bold; @endif">
                                                        [1600] Total 17 Kabkot
                                                    </td>
                                                    @foreach ($komponens as $key => $komp)
                                                        <td style="@if ($shouldBold) font-weight: bold; @endif"
                                                            class="text-right">
                                                            {{ array_key_exists($komp['id'], $dt) && $data['total_kabkot'][$komp['id']]
                                                                ? number_format(round($data['total_kabkot'][$komp['id']], 2), 2, ',', '.')
                                                                : '' }}
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endif
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
                                    @foreach ($list_detail_komponen as $i => $kmp)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="{{ $kmp['id'] }}"
                                                name="komponen_filter[]" id="{{ 'komponen_filter' . $i }}"
                                                @foreach ($komponen_filter as $kom_fil)
                                                    @if ($kom_fil == $kmp['id'])
                                                    checked
                                                    @endif @endforeach>
                                            <label class="form-check-label" for="{{ 'komponen_filter' . $i }}">
                                                {{ $kmp['name'] }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="modal-footer">
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button"
                                            data-toggle="dropdown" aria-expanded="false">
                                            Pilihan
                                        </button>
                                        <div class="dropdown-menu">
                                            <button class="dropdown-item" id="modal_komp_pilih" type="button">semua
                                                komponen</button>
                                            <button class="dropdown-item" id="modal_komp_hapus" type="button">hapus
                                                semua</button>
                                        </div>
                                    </div>
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
            form.submit();
        }

        document.getElementById('modal_komp_pilih').addEventListener('click', () => {
            const checkboxes = document.querySelectorAll("input[id^='komponen_filter']");
            checkboxes.forEach((checkbox) => {
                checkbox.checked = true;
            });
        });

        document.getElementById('modal_komp_hapus').addEventListener('click', () => {
            const checkboxes = document.querySelectorAll("input[id^='komponen_filter']");
            checkboxes.forEach((checkbox) => {
                checkbox.checked = false;
            });
        });
    </script>
@endsection
