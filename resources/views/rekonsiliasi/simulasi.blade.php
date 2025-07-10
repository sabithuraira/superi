@extends('layouts.admin')

@section('breadcrumb')
    <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="icon-home"></i></a></li>
        <li class="breadcrumb-item">Simulasi</li>
    </ul>
@endsection

@section('css')
    {{-- <style>
        .dropdown-submenu {
            position: relative;
        }

        .dropdown-submenu .dropdown-menu {
            top: 0;
            left: 100%;
            margin-top: -1px;
        }

        .table-container {
            max-height: 750px;
            overflow: auto;
            position: relative;
            border: 1px solid #ddd;
        }

        /* Kolom pertama yang benar-benar fixed */
        /* Header rows */
        .fixed-row-1 {
            position: sticky;
            top: 0;
            z-index: 15;
            /* background: white; */
        }

        .fixed-col {
            position: sticky;
            left: 0;
            z-index: 15;
            /* background: white; */
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sticky-cell {
            position: sticky;
            left: 0;
            top: 0;
            z-index: 30;
            /* background: white; */
            box-shadow:
                2px 0 5px rgba(0, 0, 0, 0.1),
                0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .fixed-row-2 {
            position: sticky;
            top: 40px;
            /* Adjust based on row height */
            z-index: 15;
            /* background: white; */
        }

        .fixed-row-3 {
            position: sticky;
            top: 80px;
            /* Adjust based on cumulative height */
            z-index: 15;
            /* background: white; */
        }

        /* Pastikan tidak ada transform yang mengganggu */
        table {
            border-collapse: separate;
            border-spacing: 0;
        }

        .bg-tw1 {
            background-color: #ffee8e;
        }

        .bg-tw2 {
            background-color: #a6ff98;
        }

        .bg-tw3 {
            background-color: #a3c6fa;
        }

        .bg-tw4 {
            background-color: #ff8e8e;
        }
    </style> --}}
@endsection

@section('content')
    <div class="row clearfix">
        <div class="col-md-12">
            <div class="card">
                <div class="header">
                    <h2>Simulasi</h2>
                </div>
                <div class="body">
                    <div class="row">
                        <div class="col">
                            <form id="form_filter">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-sm-6 col-md-2">
                                        <button class="btn btn-warning w-100" type="button" href="#simulasiModal" data-toggle="modal" id="simulasi_btn"
                                            data-target="#simulasiModal">Hitung Simulasi</button>
                                    </div>
                                    <div class="form-group col-sm-6 col-md-2">
                                        <button class="btn btn-primary w-100" type="button" href="#tabelModal" data-toggle="modal"
                                            data-target="#tabelModal">Pilih Tabel</button>
                                    </div>
                                    <div class="form-group col-sm-6 col-md-2">
                                        <button class="btn btn-primary w-100" type="button" href="#komponenModal" data-toggle="modal"
                                            data-target="#komponenModal">Pilih Komponen</button>
                                    </div>
                                    <div class="form-group col-sm-6 col-md-2 ">
                                        {{-- <label for="periode_filter" class="text-white">Periode</label> --}}
                                        <button class="btn btn-primary w-100" type="button" href="#periodeModal" data-toggle="modal"
                                            data-target="#periodeModal">Pilih Periode</button>
                                    </div>
                                    <div class="form-group col-sm-6 col-md-2 ">
                                        {{-- <label for="simpan" class="text-white">Simpan</label> --}}
                                        <button class="btn btn-success w-100" type="button" id="simpan">Simpan</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="table-area"></div>
                    <div class="row">
                        <div class="col">
                            <div class="table-container">
                                <table class="table text-center table-bordered" id="tabel-output">
                                    <thead id="thead-row">
                                    </thead>
                                    <tbody id="tbody-data" class="text-left"></tbody>
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

                                    <div class="row">

                                        @foreach ($list_periode as $li_per)
                                            <div class="form-check @if (strlen($li_per) > 4) col-2 @else col-3 @endif ">
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
                                    <button type="button" class="btn btn-success" id="submit_periode">OK</button>
                                </div>
                            </form>
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
                                    @foreach ($list_komponen as $i => $kmp)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="{{ $kmp['id'] }}" name="komponen_filter[]"
                                                id="{{ 'komponen_filter' . $i }}"
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
                                <div class="modal-footer ">
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
                                    <button type="button" class="btn btn-success" id="submit_komponen">OK</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="tabelModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="title" id="tabelModalLabel">Pilih Tabel</h4>
                            </div>
                            <form method="GET" action="">
                                <div class="modal-body">
                                    @foreach ($list_tabel as $i => $tbl)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="{{ $tbl['id'] }}" name="tabel_filter[]"
                                                id="{{ 'tabel_filter' . $i }}"
                                                @foreach ($tabel_filter as $tbl_fil)
                                                                @if ($tbl_fil == $tbl['id'])
                                                                checked
                                                                @endif @endforeach>
                                            <label class="form-check-label" for="{{ 'tabel_filter' . $i }}">
                                                {{ $tbl['name'] }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="modal-footer ">
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                            Pilihan
                                        </button>
                                        <div class="dropdown-menu">
                                            <button class="dropdown-item" id="modal_tbl_pilih" type="button">semua
                                                tabel</button>
                                            <button class="dropdown-item" id="modal_tbl_hapus" type="button">hapus
                                                semua</button>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-success" id="submit_tabel">OK</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="simulasiModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="title" id="simulasiModalLabel">Hitung Simulasi</h4>
                            </div>
                            <form method="GET" action="">
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <select name="jenis_simulasi" id="jenis_simulasi" class="form-control" required>
                                                <option value="">Pilih Simulasi</option>
                                                <option value="1">Growth QtoQ</option>
                                                <option value="2">Growth QtoQ & implisit QtoQ tetap</option>
                                                <option value="3">Growth YonY</option>
                                                <option value="4">Growth YonY & implisit YonY tetap</option>
                                                <option value="5">Ubah Nilai berlaku</option>
                                                <option value="6">Ubah Nilai berlaku & Nilai Konstan Tetap</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <table class="table table-sm" id="table_simulasi">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th>Komponen</th>
                                                        <th>Distribusi ADHB</th>
                                                        <th>Q-to-Q ADHB</th>
                                                        <th>Y-on-Y ADHB</th>
                                                        <th>C-to-C ADHB</th>
                                                        <th>angka simulasi</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="simulasi_table_body"></tbody>
                                            </table>
                                        </div>
                                    </div>

                                </div>
                                <div class="modal-footer ">
                                    <button type="button" class="btn btn-secondary" id="">Reset Simulasi</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-success" id="submit_simulasi">OK</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('css')
    <meta name="_token" content="{{ csrf_token() }}" />
    <meta name="csrf-token" content="@csrf">
@endsection

@section('scripts')
    <script type="text/javascript" src="{{ URL::asset('js/jquery-3.3.1.min.js') }}"></script>
    <script>
        var periode_filter = {!! json_encode($periode_filter) !!};
        var komponen_filter = {!! json_encode($komponen_filter) !!};
        var tabel_filter = {!! json_encode($tabel_filter) !!};
        var simulasi_data = [];


        $(document).ready(function() {
            getDatas();

            $('#submit_periode').click(function() {
                var selectedPeriods = [];
                $('input[name="periode_filter[]"]:checked').each(function() {
                    selectedPeriods.push($(this).val());
                });
                periode_filter = selectedPeriods;
                getDatas();
                $('#periodeModal').modal('hide');
            })
            $('#submit_tabel').click(function() {
                var selected = [];
                $('input[name="tabel_filter[]"]:checked').each(function() {
                    selected.push($(this).val());
                });
                tabel_filter = selected;
                getDatas();
                $('#tabelModal').modal('hide');
            })
            $('#submit_komponen').click(function() {
                var selected = [];
                $('input[name="komponen_filter[]"]:checked').each(function() {
                    selected.push($(this).val());
                });
                komponen_filter = selected;
                getDatas();
                $('#komponenModal').modal('hide');
            })
            $("#simulasi_btn").click(function() {
                getDataSimulasi();
            })

            pilihan_button();
        });


        function getDatas() {
            $('#wait_progres').modal('show');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
            $.ajax({
                url: "{{ url('/simulasi/get_data') }}",
                method: 'get',
                dataType: 'json',
                data: {
                    komponen_filter: this.komponen_filter,
                    periode_filter: this.periode_filter,
                    tabel_filter: this.tabel_filter,
                    // wilayah_filter: this.wilayah_filter,
                },
            }).done(function(data) {
                console.log(data)
                $('#wait_progres').modal('toggle')
            }).fail(function(msg) {
                // console.log(JSON.stringify(msg));
                $('#wait_progres').modal('toggle')
            });
        }

        function getDataSimulasi() {
            $('#wait_progres').modal('show');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
            $.ajax({
                url: "{{ url('/simulasi/get_data_simulasi') }}",
                method: 'get',
                dataType: 'json',
                data: {
                    komponen_filter: this.komponen_filter,
                    periode_filter: this.periode_filter,
                    // wilayah_filter: this.wilayah_filter,
                },
            }).done(function(data) {
                console.log(data)
                $('#simulasi_table_body').empty();
                if (data.success == 1) {

                    data.data.forEach(row => {


                        let rowHtml = '<tr class ="text-right">';
                        rowHtml +=
                            `<td class="text-left">${row['name'] ?? ''}</td>
                            <td>${formatNumber(row['distribusi'])}</td>
                            <td>${formatNumber(row['qtq'])}</td>
                            <td>${formatNumber(row['yty'])}</td>
                            <td>${formatNumber(row['ctc'])}</td>
                            <td><input id="${'simulasi_'+row['id']} value="${simulasi_data[row['id']]} data-id="${row['id']}"></td>
                            `
                        rowHtml += '</tr>';
                        $('#simulasi_table_body').append(rowHtml);

                    });
                }
                $('#wait_progres').modal('toggle')
            }).fail(function(msg) {
                // console.log(JSON.stringify(msg));
                $('#wait_progres').modal('toggle')
            });
        }

        $(document).on('input', '#simulasi_*', function() {
            const input = $(this);
            console.log(input.data('id'));
            // this.simulasi_data['']
        });

        // Mengubah text/number jadi satuan rupiah(text)
        function formatNumber(num) {
            const number = parseFloat(num);
            if (isNaN(number)) return "0,00";
            return number.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // Mengubah text rupiah jadi float/number
        function parseNumberIndonesian(numStr) {

            if (typeof numStr === 'number') return numStr;
            const cleaned = numStr.toString()
                .replace(/\./g, '') // Hapus semua .
                .replace(/,/g, '.'); // Ganti , dengan .
            return parseFloat(cleaned) || 0; // Konversi ke float
        }

        // ubah jadi satuan rupiah berdasarkan input(text rupiah juga)
        function formatText(input) {
            value = input.replace(/[^\d,-]/g, '');
            let parts = value.split(',');
            let bagianBulat = parts[0];
            let bagianDesimal = parts.length > 1 ? ',' + parts[1] : '';
            bagianBulat = bagianBulat.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            let formattedValue = bagianBulat + bagianDesimal;
            return formattedValue
        }

        function pilihan_button() {
            document.getElementById('modal_tbl_pilih').addEventListener('click', () => {
                const checkboxes = document.querySelectorAll("input[id^='tabel_filter']");
                checkboxes.forEach((checkbox) => {
                    checkbox.checked = true;
                });
            });

            document.getElementById('modal_tbl_hapus').addEventListener('click', () => {
                const checkboxes = document.querySelectorAll("input[id^='tabel_filter']");
                checkboxes.forEach((checkbox) => {
                    checkbox.checked = false;
                });
            });

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
                    e.preventDefault();
                    this.nextElementSibling.classList.toggle('show');
                });
            });

            document.addEventListener('click', function(e) {
                document.querySelectorAll('.dropdown-submenu .dropdown-menu').forEach(function(submenu) {
                    submenu.classList.remove('show');
                });
            });
        }
    </script>
@endsection
