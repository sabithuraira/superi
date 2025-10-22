@extends('layouts.admin')

@section('breadcrumb')
    <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="icon-home"></i></a></li>
        <li class="breadcrumb-item">Tabel Resume Per Komponen</li>
    </ul>
@endsection

@section('content')
    <div class="row clearfix">
        <div class="col-md-12">
            <div class="card">
                <div class="header">
                    <h2>Tabel Resume Per Komponen</h2>
                </div>
                <div class="body">
                    <div class="row">
                        <div class="col">
                            <form id="form_filter">

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
                                    <input class="form-check-input" type="checkbox" name="periode_filter[]" hidden>
                                    <div class="form-group col-sm-6 col-md-2 d-grid ">
                                        <button class="btn btn-success w-100" type="button"onclick="exportToExcel()">
                                            Export Excel
                                        </button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-4">
                                        <select name="komponen_filter" id="komponen_filter" class="form-control"
                                            onchange="updateKomponenFormAction(this)">
                                            @foreach ($list_group_komponen as $key => $komponens)
                                                <option value="{{ $komponens['id'] }}" data-id="{{ $komponens['id'] }}"
                                                    @if ($komponens['id'] === $komponen_filter) selected @endif>
                                                    {{ $komponens['name'] }}
                                                </option>
                                            @endforeach
                                        </select>

                                    </div>
                                    <div class="form-group col-sm-6 col-md-2 ">
                                        <button class="btn btn-primary w-100" type="button" href="#periodeModal" data-toggle="modal"
                                            data-target="#periodeModal">Pilih Periode</button>
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
                            <div class="table-bordered table-striped" id="table-responsive">
                                <table class="table" border="1px solid black">
                                    <thead>
                                        <tr class="text-center">
                                            <th>Wilayah</th>
                                            @foreach ($periode_filter as $periode)
                                                <th>{{ $periode }}</th>
                                            @endforeach
                                        </tr>

                                    </thead>
                                    <tbody>
                                        @foreach ($data as $dt)
                                            @php
                                                $shouldBold = $dt['id'] == '00';
                                            @endphp
                                            <tr style="@if ($shouldBold) background-color:#f2f2f2; font-weight: bold; @endif">
                                                <td>
                                                    [16{{ $dt['id'] }}] {{ $dt['name'] }}
                                                </td>
                                                @foreach ($periode_filter as $periode)
                                                    <td class="text-right">
                                                        {{ $dt[$periode] != null ? number_format(round($dt[$periode], 2), 2, ',', '.') : '' }}
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
                                            <option value="{{ $tbl['id'] }} "@if ($tbl['id'] === $tabel_filter) selected @endif>
                                                {{ $tbl['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="text" name="komponen_filter" hidden value="{{ $komponen_filter }}">
                                    <div class="row">
                                        @foreach ($list_periode as $li_per)
                                            <div class="form-check @if (strlen($li_per) > 4) col-2 @else col-4 @endif ">
                                                <input class="form-check-input" type="checkbox" value="{{ $li_per }}" name="periode_filter[]"
                                                    id="{{ 'periode_filter_' . $li_per }}"
                                                    @foreach ($periode_filter as $per_fil)
                                                    @if ($per_fil === $li_per)
                                                    checked
                                                    @endif @endforeach>
                                                <label class="form-check-label" for="{{ 'periode_filter_' . $li_per }}">
                                                    {{ $li_per }}
                                                </label>
                                            </div>
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

        function updateFormAction(selectElement) {
            const baseUrl = "{{ url('/pdrb_resume') }}";
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const data_id = selectedOption.getAttribute('data-id');
            const params = new URLSearchParams(window.location.search);

            const komponen_list = params.getAll('komponen_filter');
            const periode_list = params.getAll('periode_filter[]');

            let newUrl = `${baseUrl}/${data_id}?tabel_filter=${data_id}`;
            komponen_list.forEach(k => newUrl += `&komponen_filter=${encodeURIComponent(k)}`);
            periode_list.forEach(p => newUrl += `&periode_filter[]=${encodeURIComponent(p)}`);

            window.location.href = newUrl; // ✅ langsung pindah tanpa submit
        }

        function updateKomponenFormAction(selectElement) {
            const baseUrl = "{{ url('/pdrb_resume') }}";
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const data_id = selectedOption.getAttribute('data-id');
            const pathParts = window.location.pathname.split('/');
            const table_id = pathParts[pathParts.length - 1];
            const params = new URLSearchParams(window.location.search);
            const periode_list = params.getAll('periode_filter[]');
            let newUrl = `${baseUrl}/${table_id}?komponen_filter=${data_id}`;
            periode_list.forEach(p => newUrl += `&periode_filter[]=${encodeURIComponent(p)}`);

            window.location.href = newUrl; // ✅ langsung pindah tanpa submit
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
            window.location.href = location + window.btoa(unescape(encodeURIComponent(excelTemplate)));
        }

        // document.getElementById('modal_komp_pilih').addEventListener('click', () => {
        //     const checkboxes = document.querySelectorAll("input[id^='komponen_filter']");
        //     checkboxes.forEach((checkbox) => {
        //         checkbox.checked = true;
        //     });
        // });

        // document.getElementById('modal_komp_hapus').addEventListener('click', () => {
        //     const checkboxes = document.querySelectorAll("input[id^='komponen_filter']");
        //     checkboxes.forEach((checkbox) => {
        //         checkbox.checked = false;
        //     });
        // });

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
