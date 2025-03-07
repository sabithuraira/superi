@extends('layouts.admin')

@section('breadcrumb')
    <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="icon-home"></i></a></li>
        <li class="breadcrumb-item">Hitory Putaran</li>
    </ul>
@endsection

@section('content')
    <div class="row clearfix" id="app_vue">
        <div class="col-md-12">
            <div class="card">
                <div class="header">
                    <h2>History Putaran</h2>
                </div>
                <div class="body">
                    <div class="row">
                        <div class="col">
                            <form id="form_filter" method="get" action="{{ url('pdrb_putaran_7pkrt') }}">
                                @csrf
                                <input type="hidden" name="periode_filter" v-model="periode_filter" />
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-4">
                                        <select id="tabel_filter" class="form-control"
                                            v-model="tabel_filter"
                                            onchange="updateFormActionWilayah()">
                                            @foreach ($list_tabel as $key => $tbl)
                                                <option value="{{ $tbl['id'] }}" data-id="{{ $tbl['id'] }}"
                                                    @if ($tbl['id'] === $tabel_filter) selected @endif>
                                                    {{ $tbl['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-6 col-md-2 d-grid ">
                                        <select name="putaran_filter" id="putaran_filter" class="form-control"
                                            v-model="putaran_filter"
                                            onchange="updateFormActionWilayah()">
                                            @for($i=0;$i<=$max_putaran;++$i)
                                                <option value="{{ $i }}">Putaran {{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-6 col-md-2   ">
                                        <button class="btn btn-primary w-100" type="button" href="#periodeModal"
                                            data-toggle="modal" data-target="#periodeModal">Pilih Periode</button>
                                    </div>

                                    <div class="form-group col-sm-6 col-md-2 d-grid ">
                                        <button class="btn btn-success w-100" type="button"onclick="exportToExcel()">
                                            Export Excel
                                        </button>
                                    </div>


                                </div>
                                <div class="row">
                                    <div class="form-group col-sm-12 col-md-4">
                                        <select name="wilayah_filter" id="wilayah_filter" class="form-control"
                                            v-model="wilayah_filter"
                                            onchange="updateFormActionWilayah()">
                                            @foreach ($list_wilayah as $key => $wil)
                                                @if(Auth::user()->kdkab == '00' || Auth::user()->kdkab == $key)
                                                    <option value="{{ $key }}" data-id="{{ $key }}"
                                                        @if ($key == $wilayah_filter) selected @endif>
                                                        {{ $key }} - {{ $wil }}</option>
                                                @endif 
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-sm-6 col-md-2"></div>

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
                                                    {{ $dt['id'] }} {{ $dt['name'] }}
                                                </td>

                                                @foreach ($periode_filter as $periode)
                                                    <td class="text-right">
                                                        @if(count(explode("Q", $periode))>1)
                                                            {{ array_key_exists($periode, $dt) && $dt[$periode] ? number_format(round($dt[$periode], 2), 2, ',', '.') : '' }}
                                                        @else 
                                                            @php 
                                                                $is_all_year = 1;
                                                                $sum_year = 0;
                                                            @endphp 
                                                            @for ($i=1; $i<=4;$i++)
                                                                @if(array_key_exists($periode."Q".$i, $dt) && $dt[$periode."Q".$i])
                                                                    @php 
                                                                        $sum_year += $dt[$periode."Q".$i];
                                                                    @endphp 
                                                                @else 
                                                                    @php 
                                                                        $is_all_year = 0;
                                                                    @endphp 
                                                                @endif
                                                            @endfor
                                                            @if($is_all_year==1)
                                                                {{ number_format(round($sum_year, 2), 2, ',', '.') }}
                                                            @endif
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

                <div class="modal" id="periodeModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="title" id="periodeModalLabel">Pilih Periode</h4>
                            </div>
                            <form method="GET" action="">
                                <div class="modal-body mx-4">
                                    <input type="hidden" v-model="tabel_filter" name="tabel_filter" />
                                    <input type="hidden" v-model="wilayah_filter" name="wilayah_filter" />
                                    <input type="hidden" v-model="putaran_filter" name="putaran_filter" />

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
                                    <button type="button" id="btn_check_all" class="btn btn-info">Pilih Semua</button>
                                    <button type="button" id="btn_uncheck_all" class="btn btn-danger">Kosongkan Semua</button>
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

@section('css')
    <meta name="_token" content="{{ csrf_token() }}" />
    <meta name="csrf-token" content="@csrf">
@endsection

@section('scripts')
    <script type="text/javascript" src="{{ URL::asset('js/app.js') }}"></script>
    <script>
        var vm = new Vue({
            el: "#app_vue",
            data: {
                tabel_filter: {!! json_encode($tabel_filter) !!},
                wilayah_filter: {!! json_encode($wilayah_filter) !!},
                putaran_filter: {!! json_encode($putaran_filter) !!},
                periode_filter: {!! json_encode($periode_filter) !!},
            }
        });
    </script>

    <script>
        function updateFormActionWilayah() {
            var form = document.getElementById('form_filter');
            var tabel_option = document.getElementById('tabel_filter').options[document.getElementById('tabel_filter')
                .selectedIndex];
            var data_id = tabel_option.getAttribute('data-id');

            form.action = window.origin + '/superi/public/pdrb_putaran' + '/' + data_id;
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

        document.getElementById('btn_check_all').addEventListener('click', () => {
            const checkboxes = document.querySelectorAll("input[id^='periode_filter']");
            checkboxes.forEach((checkbox) => {
                checkbox.checked = true;
            });
        });

        document.getElementById('btn_uncheck_all').addEventListener('click', () => {
            const checkboxes = document.querySelectorAll("input[id^='periode_filter']");
            checkboxes.forEach((checkbox) => {
                checkbox.checked = false;
            });
        });

        // document.getElementById('modal_periode_q1').addEventListener('click', () => {
        //     const allCheckboxes = document.querySelectorAll("input[id^='periode_filter']");
        //     allCheckboxes.forEach((checkbox) => {
        //         checkbox.checked = false;
        //     });
        //     const q1Checkboxes = document.querySelectorAll("input[id^='periode_filter_'][id$='Q1']");
        //     q1Checkboxes.forEach((checkbox) => {
        //         checkbox.checked = true;
        //     });
        // });

        // document.getElementById('modal_periode_q2').addEventListener('click', () => {
        //     const allCheckboxes = document.querySelectorAll("input[id^='periode_filter']");
        //     allCheckboxes.forEach((checkbox) => {
        //         checkbox.checked = false;
        //     });
        //     const q2Checkboxes = document.querySelectorAll("input[id^='periode_filter_'][id$='Q2']");
        //     q2Checkboxes.forEach((checkbox) => {
        //         checkbox.checked = true;
        //     });
        // });

        // document.getElementById('modal_periode_q3').addEventListener('click', () => {
        //     const allCheckboxes = document.querySelectorAll("input[id^='periode_filter']");
        //     allCheckboxes.forEach((checkbox) => {
        //         checkbox.checked = false;
        //     });
        //     const q3Checkboxes = document.querySelectorAll("input[id^='periode_filter_'][id$='Q3']");
        //     q3Checkboxes.forEach((checkbox) => {
        //         checkbox.checked = true;
        //     });
        // });

        // document.getElementById('modal_periode_q4').addEventListener('click', () => {
        //     const allCheckboxes = document.querySelectorAll("input[id^='periode_filter']");
        //     allCheckboxes.forEach((checkbox) => {
        //         checkbox.checked = false;
        //     });
        //     const q4Checkboxes = document.querySelectorAll("input[id^='periode_filter_'][id$='Q4']");
        //     q4Checkboxes.forEach((checkbox) => {
        //         checkbox.checked = true;
        //     });
        // });

        // document.getElementById('modal_periode_tahun').addEventListener('click', () => {
        //     const allCheckboxes = document.querySelectorAll("input[id^='periode_filter']");
        //     allCheckboxes.forEach((checkbox) => {
        //         checkbox.checked = false;
        //     });
        //     allCheckboxes.forEach((checkbox) => {
        //         const id = checkbox.id;
        //         if (/periode_filter_\d{4}$/.test(id)) {
        //             checkbox.checked = true;
        //         }
        //     });
        // });
    </script>
@endsection
