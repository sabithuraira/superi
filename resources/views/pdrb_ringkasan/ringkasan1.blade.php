@extends('layouts.admin')

@section('breadcrumb')
    <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="icon-home"></i></a></li>
        <li class="breadcrumb-item">Tabel Ringkasan</li>
    </ul>
@endsection

@section('css')
    <style>
        .dropdown-submenu {
            position: relative;
        }

        .dropdown-submenu .dropdown-menu {
            top: 0;
            left: 100%;
            margin-top: -1px;
        }
    </style>
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
                                        <button class="btn btn-success w-100 mb-2" type="button"
                                            onclick="exportToExcel()">Export Excel
                                        </button>
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
                        <div class="col">
                            <div class="table-responsive table-bordered" id="table-responsive">
                                <table class="table" border="1px solid black">
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
                                                    @endphp
                                                    <td class="text-right"
                                                        style="@if ($shouldBold) font-weight: bold; @endif
                                                        @if ($qtqProv && $qtqKab && $qtqProv * $qtqKab < 0) background-color: yellow @endif">
                                                        {{ $qtqKab ? number_format(round($qtqKab, 2), 2, ',', '.') : '' }}
                                                    </td>
                                                    <td class="text-right"
                                                        style="@if ($shouldBold) font-weight: bold; @endif
                                                        @if ($qtqProv && $qtqKab && $qtqProv * $qtqKab < 0) background-color: yellow @endif">
                                                        {{ array_key_exists($periode . 'qtq_prov', $dt) && $dt[$periode . 'qtq_prov'] ? number_format(round($dt[$periode . 'qtq_prov'], 2), 2, ',', '.') : '' }}
                                                    </td>

                                                    <td class="text-right"
                                                        style="@if ($shouldBold) font-weight: bold; @endif
                                                        @if ($yoyProv && $yoyKab && $yoyProv * $yoyKab < 0) background-color: yellow @endif">
                                                        {{ array_key_exists($periode . 'yoy_kab', $dt) && $dt[$periode . 'yoy_kab'] ? number_format(round($dt[$periode . 'yoy_kab'], 2), 2, ',', '.') : '' }}
                                                    </td>
                                                    <td class="text-right"
                                                        style="@if ($shouldBold) font-weight: bold; @endif
                                                        @if ($yoyProv && $yoyKab && $yoyProv * $yoyKab < 0) background-color: yellow @endif">
                                                        {{ array_key_exists($periode . 'yoy_prov', $dt) && $dt[$periode . 'yoy_prov'] ? number_format(round($dt[$periode . 'yoy_prov'], 2), 2, ',', '.') : '' }}
                                                    </td>
                                                    <td class="text-right"
                                                        style="@if ($shouldBold) font-weight: bold; @endif
                                                        @if ($ctcProv && $ctcKab && $ctcProv * $ctcKab < 0) background-color: yellow @endif">
                                                        {{ array_key_exists($periode . 'ctc_kab', $dt) && $dt[$periode . 'ctc_kab'] ? number_format(round($dt[$periode . 'ctc_kab'], 2), 2, ',', '.') : '' }}
                                                    </td>
                                                    <td class="text-right"
                                                        style="@if ($shouldBold) font-weight: bold; @endif
                                                        @if ($ctcProv && $ctcKab && $ctcProv * $ctcKab < 0) background-color: yellow @endif">
                                                        {{ array_key_exists($periode . 'ctc_prov', $dt) && $dt[$periode . 'ctc_prov'] ? number_format(round($dt[$periode . 'ctc_prov'], 2), 2, ',', '.') : '' }}
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
                                                value="{{ $tbl['id'] }} "@if ($tbl['id'] === $tabel_filter) selected @endif>
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
                                <div class="modal-footer ">
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
                                                value="{{ $tbl['id'] }} "@if ($tbl['id'] === $tabel_filter) selected @endif>
                                                {{ $tbl['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="row">
                                        @foreach ($list_periode as $li_per)
                                            @if ($li_per < 2018)
                                                <div class="col-8"></div>
                                                <div class="form-check col-4">
                                                    <input class="form-check-input" type="checkbox"
                                                        value="{{ $li_per }}" name="periode_filter[]"
                                                        id="{{ 'periode_filter_' . $li_per }}"
                                                        @foreach ($periode_filter as $per_fil)
                                                @if ($per_fil === $li_per)
                                                checked
                                                @endif @endforeach>
                                                    <label class="form-check-label"
                                                        for="{{ 'periode_filter_' . $li_per }}">
                                                        {{ $li_per }}
                                                    </label>
                                                </div>
                                            @else
                                                <div
                                                    class="form-check @if (strlen($li_per) > 4) col-2 @else col-4 @endif ">
                                                    <input class="form-check-input" type="checkbox"
                                                        value="{{ $li_per }}" name="periode_filter[]"
                                                        id="{{ 'periode_filter_' . $li_per }}"
                                                        @foreach ($periode_filter as $per_fil)
                                                    @if ($per_fil === $li_per)
                                                    checked
                                                    @endif @endforeach>
                                                    <label class="form-check-label"
                                                        for="{{ 'periode_filter_' . $li_per }}">
                                                        {{ $li_per }}
                                                    </label>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button"
                                            data-toggle="dropdown" aria-expanded="false">
                                            Pilihan
                                        </button>
                                        <div class="dropdown-menu">
                                            <button class="dropdown-item" id="modal_periode_semua" type="button">
                                                Semua Periode
                                            </button>
                                            <button class="dropdown-item" id="modal_periode_q1" type="button">
                                                Semua Q1
                                            </button>
                                            <button class="dropdown-item" id="modal_periode_q2" type="button">
                                                Semua Q2
                                            </button>
                                            <button class="dropdown-item" id="modal_periode_q3" type="button">
                                                Semua Q3
                                            </button>
                                            <button class="dropdown-item" id="modal_periode_q4" type="button">
                                                Semua Q4
                                            </button>
                                            <button class="dropdown-item" id="modal_periode_tahun" type="button">
                                                Semua Tahun
                                            </button>
                                            <div class="dropdown-submenu">
                                                <button class="dropdown-item dropdown-toggle" type="button">
                                                    Tahun
                                                </button>
                                                <div class="dropdown-menu">
                                                    @for ($i = 3; $i >= 0; $i--)
                                                        <button class="dropdown-item tahun-selector"
                                                            id="{{ 'modal_tahun_' . ($tahun_berlaku - $i) }}"
                                                            type="button">{{ $tahun_berlaku - $i }}</button>
                                                    @endfor
                                                </div>
                                            </div>
                                            <button class="dropdown-item" id="modal_periode_hapus" type="button">
                                                Hapus Semua</button>
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

        function updateFormAction(selectElement) {
            var form = document.getElementById('form_filter');
            var selectedOption = selectElement.options[selectElement.selectedIndex];
            var url = selectedOption.getAttribute('data-url');
            var data_id = selectedOption.getAttribute('data-id');
            form.action = APP_URL + '/' + url + '/' + data_id;
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

        document.getElementById('modal_periode_semua').addEventListener('click', () => {
            const checkboxes = document.querySelectorAll("input[id^='periode_filter']");
            checkboxes.forEach((checkbox) => {
                checkbox.checked = true;
            });
        });

        document.getElementById('modal_periode_hapus').addEventListener('click', () => {
            const checkboxes = document.querySelectorAll("input[id^='periode_filter']");
            checkboxes.forEach((checkbox) => {
                checkbox.checked = false;
            });
        });

        document.getElementById('modal_periode_q1').addEventListener('click', () => {
            const allCheckboxes = document.querySelectorAll("input[id^='periode_filter']");
            allCheckboxes.forEach((checkbox) => {
                checkbox.checked = false;
            });
            const q1Checkboxes = document.querySelectorAll("input[id^='periode_filter_'][id$='Q1']");
            q1Checkboxes.forEach((checkbox) => {
                checkbox.checked = true;
            });
        });

        document.getElementById('modal_periode_q2').addEventListener('click', () => {
            const allCheckboxes = document.querySelectorAll("input[id^='periode_filter']");
            allCheckboxes.forEach((checkbox) => {
                checkbox.checked = false;
            });
            const q2Checkboxes = document.querySelectorAll("input[id^='periode_filter_'][id$='Q2']");
            q2Checkboxes.forEach((checkbox) => {
                checkbox.checked = true;
            });
        });

        document.getElementById('modal_periode_q3').addEventListener('click', () => {
            const allCheckboxes = document.querySelectorAll("input[id^='periode_filter']");
            allCheckboxes.forEach((checkbox) => {
                checkbox.checked = false;
            });
            const q3Checkboxes = document.querySelectorAll("input[id^='periode_filter_'][id$='Q3']");
            q3Checkboxes.forEach((checkbox) => {
                checkbox.checked = true;
            });
        });

        document.getElementById('modal_periode_q4').addEventListener('click', () => {
            const allCheckboxes = document.querySelectorAll("input[id^='periode_filter']");
            allCheckboxes.forEach((checkbox) => {
                checkbox.checked = false;
            });
            const q4Checkboxes = document.querySelectorAll("input[id^='periode_filter_'][id$='Q4']");
            q4Checkboxes.forEach((checkbox) => {
                checkbox.checked = true;
            });
        });

        document.getElementById('modal_periode_tahun').addEventListener('click', () => {
            const allCheckboxes = document.querySelectorAll("input[id^='periode_filter']");
            allCheckboxes.forEach((checkbox) => {
                checkbox.checked = false;
            });
            allCheckboxes.forEach((checkbox) => {
                const id = checkbox.id;
                if (/periode_filter_\d{4}$/.test(id)) {
                    checkbox.checked = true;
                }
            });
        });

        document.querySelectorAll('.tahun-selector').forEach((button) => {
            button.addEventListener('click', (event) => {
                const buttonId = event.target.id;
                const year = buttonId.split('_').pop();
                const allCheckboxes = document.querySelectorAll("input[id^='periode_filter']");
                allCheckboxes.forEach((checkbox) => {
                    checkbox.checked = false;
                });
                allCheckboxes.forEach((checkbox) => {
                    const id = checkbox.id;
                    if (id.includes(year)) {
                        checkbox.checked = true;
                    }
                });
                console.log(`Tombol untuk tahun ${year} diproses.`);
            });
        });

        document.querySelectorAll('.dropdown-submenu .dropdown-toggle').forEach(function(element) {
            element.addEventListener('click', function(e) {
                e.stopPropagation();
                this.nextElementSibling.classList.toggle('show');
            });
        });

        document.addEventListener('click', function(e) {
            document.querySelectorAll('.dropdown-submenu .dropdown-menu').forEach(function(submenu) {
                submenu.classList.remove('show');
            });
        });
    </script>
@endsection
