@extends('layouts.admin')

@section('breadcrumb')
    <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="icon-home"></i></a></li>
        <li class="breadcrumb-item">Hitory Putaran</li>
    </ul>
@endsection

@section('content')
    <div class="row clearfix">
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
                                        <select name="putaran_filter" id="putaran_filter" class="form-control">
                                            <option value="1">Putaran 1</option>
                                            <option value="2">Putaran 2</option>
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
                                            onchange="updateFormActionWilayah()">
                                            @foreach ($list_wilayah as $key => $wil)
                                                <option value="{{ $key }}" data-id="{{ $key }}"
                                                    @if ($key == $wilayah_filter) selected @endif>
                                                    {{ $key }} - {{ $wil }}</option>
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
                                                <input class="form-check-input" type="checkbox" value="{{ $i }}"
                                                    name="periode_filter[]" id="{{ 'periode_filter_' . $i }}"
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
            form.action = window.origin + '/superi/public/pdrb_putaran' + '/' + data_id;
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

            form.action = window.origin + '/superi/public/pdrb_putaran' + '/' + data_id + '?wilayah_filter=' + wilayah;
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
