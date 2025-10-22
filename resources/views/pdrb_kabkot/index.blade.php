@extends('layouts.admin')

@section('breadcrumb')
    <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="icon-home"></i></a></li>
        <li class="breadcrumb-item">Tabel PDRB per Kabupaten Kota (PKRT 12 Komponen)</li>
    </ul>
@endsection

@section('content')
    <div class="row clearfix">
        <div class="col-md-12">
            <div class="card">
                <div class="header">
                    @if (Request::is('pdrb_kabkot/*'))
                        <h2>Tabel PDRB per Kabupaten Kota (PKRT 12 Komponen)</h2>
                    @elseif (Request::is('pdrb_kabkot_7pkrt/*'))
                        <h2>Tabel PDRB per Kabupaten Kota (PKRT 7 Komponen)</h2>
                    @elseif (Request::is('pdrb_kabkot_rilis/*'))
                        <h2>Tabel PDRB per Kabupaten Kota (Rilis)</h2>
                    @endif
                </div>
                <div class="body">
                    <div class="row">
                        <div class="col">
                            <form id="form_filter" method="get">
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-4">
                                        <select name="tabel_filter" id="tabel_filter" class="form-control" onchange="updateFormAction(this)">
                                            @foreach ($list_tabel as $key => $tbl)
                                                <option value="{{ $tbl['id'] }}" data-id="{{ $tbl['id'] }}"
                                                    @if ($tbl['id'] === $id) selected @endif>
                                                    {{ $tbl['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-6 col-md-2 d-grid ">
                                        <button class="btn btn-primary w-100 " type="button" href="#komponenModal" data-toggle="modal"
                                            data-target="#komponenModal">Pilih Komponen</button>
                                    </div>

                                    <div class="form-group col-sm-6 col-md-2 d-grid ">
                                        <button class="btn btn-success w-100" type="button"onclick="exportToExcel()">
                                            Export Excel
                                        </button>
                                    </div>
                                    <div class="form-group col-sm-6 col-md-2 d-grid ">

                                        @if (Request::is('pdrb_kabkot/*') || Request::is('pdrb_kabkot_rilis/*'))
                                            <a class="btn btn-primary w-100" type="button" href="{{ url('pdrb_kabkot_7pkrt/3.1') }}">
                                                PKRT 7 Komponen
                                            </a>
                                        @elseif (Request::is('pdrb_kabkot_7pkrt/*'))
                                            <a class="btn btn-primary w-100" type="button" href="{{ url('pdrb_kabkot/3.1') }}">
                                                PKRT 12 Komponen
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-4">
                                        <select name="wilayah_filter" id="wilayah_filter" class="form-control" onchange="updateFormActionWilayah(this)">
                                            @foreach ($list_wilayah as $key => $wil)
                                                @if (Auth::user()->kdkab == '00' || Auth::user()->kdkab == $key)
                                                    <option value="{{ $key }}" data-id="{{ $key }}"
                                                        @if ($key == $wilayah_filter) selected @endif>
                                                        16{{ $key }} - {{ $wil }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-6 col-md-2   ">
                                        <button class="btn btn-primary w-100" type="button" href="#periodeModal" data-toggle="modal"
                                            data-target="#periodeModal">Pilih Periode</button>
                                    </div>
                                    <div class="col-sm-6 col-md-2"></div>
                                    <div class="form-group col-sm-6 col-md-2 d-grid ">
                                        @if (Request::is('pdrb_kabkot/*') || Request::is('pdrb_kabkot_7pkrt/*'))
                                            <a class="btn btn-primary w-100" type="button" href="{{ url('pdrb_kabkot_rilis/3.1') }}">
                                                Tabel Rilis
                                            </a>
                                        @elseif (Request::is('pdrb_kabkot_rilis/*'))
                                            <a class="btn btn-primary w-100" type="button" href="{{ url('pdrb_kabkot/3.1') }}">
                                                PKRT 12 Komponen
                                            </a>
                                        @endif
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
                                            <th>Komponen</th>
                                            @foreach ($periode_filter as $periode)
                                                <th>{{ $periode }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $dt)
                                            <tr>
                                                @php
                                                    $shouldBold = strlen($dt['id']) < 4 || $dt['id'] == 'c_pdrb';
                                                @endphp

                                                <td style="@if ($shouldBold) font-weight: bold; @endif">
                                                    {{ $dt['name'] }}</td>

                                                @foreach ($periode_filter as $periode)
                                                    <td class="text-right" style="@if ($shouldBold) font-weight: bold; @endif">
                                                        {{ array_key_exists($periode, $dt) && $dt[$periode] ? number_format(round($dt[$periode], 2), 2, ',', '.') : '' }}
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
                                    {{-- <input type="text" name="tabel_filter" id="tabel_filter" class="form-control" value="{{ $id }}" hidden> --}}
                                    <input type="text" name="wilayah_filter" id="wilayah_filter" class="form-control" value="{{ $wilayah_filter }}"
                                        hidden>

                                    @foreach ($periode_filter as $p)
                                        <input type="hidden" name="periode_filter[]" value="{{ $p }}">
                                    @endforeach

                                    @foreach ($list_group_komponen as $i => $kmp)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="{{ $kmp['column'] }}" name="komponen_filter[]"
                                                id="{{ 'komponen_filter' . $i }}"
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
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
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
                                    <input type="text" name="wilayah_filter" id="wilayah_filter" class="form-control"
                                        value="{{ $wilayah_filter }}" hidden>
                                    @foreach ($komponen_filter as $k)
                                        <input type="hidden" name="komponen_filter[]" value="{{ $k }}">
                                    @endforeach
                                    <div class="row">
                                        @foreach ($list_periode as $li_per)
                                            @if ($li_per < 2018)
                                                <div class="col-8"></div>
                                                <div class="form-check col-4">
                                                    <input class="form-check-input" type="checkbox" value="{{ $li_per }}"
                                                        name="periode_filter[]" id="{{ 'periode_filter_' . $li_per }}"
                                                        @foreach ($periode_filter as $per_fil)
                                                @if ($per_fil === $li_per)
                                                checked
                                                @endif @endforeach>
                                                    <label class="form-check-label" for="{{ 'periode_filter_' . $li_per }}">
                                                        {{ $li_per }}
                                                    </label>
                                                </div>
                                            @else
                                                <div class="form-check @if (strlen($li_per) > 4) col-2 @else col-4 @endif ">
                                                    <input class="form-check-input" type="checkbox" value="{{ $li_per }}"
                                                        name="periode_filter[]" id="{{ 'periode_filter_' . $li_per }}"
                                                        @foreach ($periode_filter as $per_fil)
                                                    @if ($per_fil === $li_per)
                                                    checked
                                                    @endif @endforeach>
                                                    <label class="form-check-label" for="{{ 'periode_filter_' . $li_per }}">
                                                        {{ $li_per }}
                                                    </label>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
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
                                                        <button class="dropdown-item tahun-selector" id="{{ 'modal_tahun_' . ($tahun_berlaku - $i) }}"
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
    <script>
        var APP_URL = {!! json_encode(url('/')) !!}
        var URL_SEGMENT = "{{ Request::segment(1) }}";

        function updateFormAction(selectElement) {
            const pathParts = window.location.pathname.split('/').filter(Boolean);
            const routeName = pathParts[pathParts.length - 2];
            const appBase = "{{ url('/') }}";
            const baseUrl = `${appBase}/${routeName}`;
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const data_id = selectedOption.getAttribute('data-id');
            const params = new URLSearchParams(window.location.search);

            const wilayah_id = params.getAll('wilayah_filter');
            const komponen_list = params.getAll('komponen_filter[]');
            const periode_list = params.getAll('periode_filter[]');

            let newUrl = `${baseUrl}/${data_id}?wilayah_filter=${wilayah_id}`;
            komponen_list.forEach(k => newUrl += `&komponen_filter[]=${encodeURIComponent(k)}`);
            periode_list.forEach(p => newUrl += `&periode_filter[]=${encodeURIComponent(p)}`);

            window.location.href = newUrl;
        }

        function updateFormActionWilayah(selectElement) {
            const pathParts = window.location.pathname.split('/').filter(Boolean);
            const routeName = pathParts[pathParts.length - 2];
            const appBase = "{{ url('/') }}";
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const data_id = selectedOption.getAttribute('data-id');
            const pathParts = window.location.pathname.split('/');
            const table_id = pathParts[pathParts.length - 1];

            const params = new URLSearchParams(window.location.search);
            const komponen_list = params.getAll('komponen_filter[]');
            const periode_list = params.getAll('periode_filter[]');

            let newUrl = `${baseUrl}/${table_id}?wilayah_filter=${data_id}`;
            komponen_list.forEach(k => newUrl += `&komponen_filter[]=${encodeURIComponent(k)}`);
            periode_list.forEach(p => newUrl += `&periode_filter[]=${encodeURIComponent(p)}`);

            window.location.href = newUrl;
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
