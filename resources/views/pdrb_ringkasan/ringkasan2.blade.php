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

                                    <div class="form-group col-sm-12 col-md-2" @if (in_array($tabel_filter, ['1.1', '1.2']))  @endif>
                                        <select name="periode_filter" id="periode_filter"
                                            class="form-control"@if (in_array($tabel_filter, ['1.1', '1.2'])) disabled @endif
                                            onchange="updateFormActionperiode()">
                                            @foreach ($list_quartil as $key => $qtl)
                                                <option value="{{ $qtl }}" data-periode="{{ $qtl }}"
                                                    @if ($qtl == $periode_filter) selected @endif>
                                                    {{ $qtl }}</option>
                                            @endforeach
                                        </select>
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
                                            <th rowspan="2">Kabupaten/Kota</th>
                                            <th colspan="2">Pertumbuhan YoY</th>
                                            <th colspan="2">Pertumbuhan QtQ</th>
                                            <th colspan="2">Pertumbuhan CtC</th>
                                            <th rowspan="2">Implisit YoY {{ $periode_filter }}</th>
                                            <th rowspan="2">Share</th>
                                        </tr>
                                        <tr>
                                            <th>
                                                {{ explode('Q', $periode_filter)[0] - 1 . 'Q' . explode('Q', $periode_filter)[1] }}
                                            </th>
                                            <th>
                                                {{ $periode_filter }}
                                            </th>
                                            <th>
                                                {{ explode('Q', $periode_filter)[0] - 1 . 'Q' . explode('Q', $periode_filter)[1] }}
                                            </th>
                                            <th>
                                                {{ $periode_filter }}
                                            </th>
                                            <th>
                                                {{ explode('Q', $periode_filter)[0] - 1 . 'Q' . explode('Q', $periode_filter)[1] }}
                                            </th>
                                            <th>
                                                {{ $periode_filter }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $dt)
                                            <tr>
                                                <td>
                                                    [{{ $dt['wilayah']['id'] }}] {{ $dt['wilayah']['alias'] }}
                                                </td>
                                                <td>
                                                    {{ $dt['data']['yoy_prev'] }}
                                                </td>
                                                <td>
                                                    {{ $dt['data']['yoy_current'] }}
                                                </td>
                                                <td>
                                                    {{ $dt['data']['qtq_prev'] }}
                                                </td>
                                                <td>
                                                    {{ $dt['data']['qtq_current'] }}
                                                </td>
                                                <td>
                                                    {{ $dt['data']['ctc_prev'] }}
                                                </td>
                                                <td>
                                                    {{ $dt['data']['ctc_current'] }}
                                                </td>
                                                <td></td>
                                                <td></td>
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
            var periode_option = document.getElementById('periode_filter').options[document.getElementById('periode_filter')
                .selectedIndex];
            var periode = periode_option.getAttribute('data-periode')
            form.action = window.origin + '/superi/public/' + url + '/' + data_id + '?periode_filter=' + periode;
            console.log(form.action)
            form.submit();
        }
    </script>
@endsection
