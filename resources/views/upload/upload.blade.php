@extends('layouts.admin')

@section('breadcrumb')
    <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="icon-home"></i></a></li>
        <li class="breadcrumb-item">Import Excel PDRB</li>
    </ul>
@endsection

@section('content')
    <div class="row clearfix">
        <div class="col-md-12">
            <div class="card" id="app_vue">
                <div class="header">
                    <h2>Import Excel PDRB</h2>
                </div>
                <div class="body">
                    <form method="post" action="{{ url('upload/import') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row clearfix">

                            <div class="col-lg-3 col-md-12 left-box">
                                <div class="form-group">
                                    <label>Kabupaten/Kota:</label>

                                    <div class="input-group">
                                        <select class="form-control  form-control-sm" name="wilayah" v-model="form_data.wilayah">
                                            @foreach (config('app.wilayah') as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-12 left-box">
                                <div class="form-group">
                                    <label>Tahun:</label>

                                    <div class="input-group">
                                        <select class="form-control  form-control-sm" disabled name="tahun" v-model="form_data.tahun">
                                            @for ($i = date('Y'); $i >= 2023; $i--)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-12 left-box">
                                <div class="form-group">
                                    <label>Triwulan:</label>
                                    <div class="input-group">
                                        <select class="form-control  form-control-sm" disabled name="triwulan" v-model="form_data.triwulan">
                                            @for ($i = 1; $i <= 4; $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-12 right-box">
                                <div class="form-group">
                                    <label>Pilih File:</label>
                                    <input type="file" class="form-control" name="excel_file">
                                </div>
                            </div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-lg-6">
                                <button name="action" class="btn btn-success float-left" type="submit" value="2"><i
                                        class="fa fa-file-excel-o"></i>&nbsp; Export Excel</button>
                            </div>
                            <div class="col-lg-6">
                                <button type="submit" class="btn btn-primary float-right" name="action" value="1">Simpan</button>
                            </div>
                        </div>
                    </form>

                    <ul class="nav nav-tabs mt-2">
                        <li class="nav-item"><a class="nav-link active show" data-toggle="tab" href="#adhb">ADHB</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#adhk">ADHK</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane show active" id="adhb">
                            <table class="table table-bordered m-b-0" style="min-width:100%">
                                <tr class="text-center">
                                    <th>Komponen</th>
                                    <th>@{{ form_data.tahun }}Q1</th>
                                    <th>@{{ form_data.tahun }}Q2</th>
                                    <th>@{{ form_data.tahun }}Q3</th>
                                    <th>@{{ form_data.tahun }}Q4</th>
                                </tr>

                                <template v-for="(data, index) in komponen.filter(x=>x.parent_id==null)" :key="data.id">
                                    <tr>
                                        <td>@{{ data.no_komponen }} @{{ data.nama_komponen }}</td>

                                        <td v-if="datas['adhb'][0]==null"></td>
                                        <td class="text-right" v-else>@{{ datas['adhb'][0]['c_' + data.no_komponen.replaceAll('.', '')] }}</td>

                                        <td v-if="datas['adhb'][1]==null"></td>
                                        <td class="text-right" v-else>@{{ datas['adhb'][1]['c_' + data.no_komponen.replaceAll('.', '')] }}</td>

                                        <td v-if="datas['adhb'][2]==null"></td>
                                        <td class="text-right" v-else>@{{ datas['adhb'][2]['c_' + data.no_komponen.replaceAll('.', '')] }}</td>

                                        <td v-if="datas['adhb'][3]==null"></td>
                                        <td class="text-right" v-else>@{{ datas['adhb'][3]['c_' + data.no_komponen.replaceAll('.', '')] }}</td>

                                    </tr>

                                    <tr v-for="(data2, index2) in komponen.filter(y=>y.parent_id==data.no_komponen)" :key="data2.id">
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp; @{{ data2.no_komponen }} @{{ data2.nama_komponen }}</td>

                                        <td v-if="datas['adhb'][0]==null"></td>
                                        <td class="text-right" v-else>@{{ datas['adhb'][0]['c_' + data2.no_komponen.replaceAll('.', '')] }}</td>

                                        <td v-if="datas['adhb'][1]==null"></td>
                                        <td class="text-right" v-else>@{{ datas['adhb'][1]['c_' + data2.no_komponen.replaceAll('.', '')] }}</td>

                                        <td v-if="datas['adhb'][2]==null"></td>
                                        <td class="text-right" v-else>@{{ datas['adhb'][2]['c_' + data2.no_komponen.replaceAll('.', '')] }}</td>

                                        <td v-if="datas['adhb'][3]==null"></td>
                                        <td class="text-right" v-else>@{{ datas['adhb'][3]['c_' + data2.no_komponen.replaceAll('.', '')] }}</td>
                                    </tr>
                                </template>

                                <td><b>PDRB</b></td>

                                <td v-if="datas['adhb'][0]==null"></td>
                                <td class="text-right" v-else>@{{ datas['adhb'][0]['c_pdrb'] }}</td>

                                <td v-if="datas['adhb'][1]==null"></td>
                                <td class="text-right" v-else>@{{ datas['adhb'][1]['c_pdrb'] }}</td>

                                <td v-if="datas['adhb'][2]==null"></td>
                                <td class="text-right" v-else>@{{ datas['adhb'][2]['c_pdrb'] }}</td>

                                <td v-if="datas['adhb'][3]==null"></td>
                                <td class="text-right" v-else>@{{ datas['adhb'][3]['c_pdrb'] }}</td>
                            </table>
                        </div>

                        <div class="tab-pane" id="adhk">
                            <table class="table table-bordered m-b-0" style="min-width:100%">
                                <tr class="text-center">
                                    <th>Komponen</th>
                                    <th>@{{ form_data.tahun }}Q1</th>
                                    <th>@{{ form_data.tahun }}Q2</th>
                                    <th>@{{ form_data.tahun }}Q3</th>
                                    <th>@{{ form_data.tahun }}Q4</th>
                                </tr>

                                <template v-for="(data, index) in komponen.filter(x=>x.parent_id==null)" :key="data.id">
                                    <tr>
                                        <td>@{{ data.no_komponen }} @{{ data.nama_komponen }}</td>

                                        <td v-if="datas['adhk'][0]==null"></td>
                                        <td class="text-right" v-else>@{{ datas['adhk'][0]['c_' + data.no_komponen.replaceAll('.', '')] }}</td>

                                        <td v-if="datas['adhk'][1]==null"></td>
                                        <td class="text-right" v-else>@{{ datas['adhk'][1]['c_' + data.no_komponen.replaceAll('.', '')] }}</td>

                                        <td v-if="datas['adhk'][2]==null"></td>
                                        <td class="text-right" v-else>@{{ datas['adhk'][2]['c_' + data.no_komponen.replaceAll('.', '')] }}</td>

                                        <td v-if="datas['adhk'][3]==null"></td>
                                        <td class="text-right" v-else>@{{ datas['adhk'][3]['c_' + data.no_komponen.replaceAll('.', '')] }}</td>

                                    </tr>

                                    <tr v-for="(data2, index2) in komponen.filter(y=>y.parent_id==data.no_komponen)" :key="data2.id">
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp; @{{ data2.no_komponen }} @{{ data2.nama_komponen }}</td>

                                        <td v-if="datas['adhk'][0]==null"></td>
                                        <td class="text-right" v-else>@{{ datas['adhk'][0]['c_' + data2.no_komponen.replaceAll('.', '')] }}</td>

                                        <td v-if="datas['adhk'][1]==null"></td>
                                        <td class="text-right" v-else>@{{ datas['adhk'][1]['c_' + data2.no_komponen.replaceAll('.', '')] }}</td>

                                        <td v-if="datas['adhk'][2]==null"></td>
                                        <td class="text-right" v-else>@{{ datas['adhk'][2]['c_' + data2.no_komponen.replaceAll('.', '')] }}</td>

                                        <td v-if="datas['adhk'][3]==null"></td>
                                        <td class="text-right" v-else>@{{ datas['adhk'][3]['c_' + data2.no_komponen.replaceAll('.', '')] }}</td>
                                    </tr>
                                </template>


                                <td><b>PDRB</b></td>

                                <td v-if="datas['adhk'][0]==null"></td>
                                <td class="text-right" v-else>@{{ datas['adhk'][0]['c_pdrb'] }}</td>

                                <td v-if="datas['adhk'][1]==null"></td>
                                <td class="text-right" v-else>@{{ datas['adhk'][1]['c_pdrb'] }}</td>

                                <td v-if="datas['adhk'][2]==null"></td>
                                <td class="text-right" v-else>@{{ datas['adhk'][2]['c_pdrb'] }}</td>

                                <td v-if="datas['adhk'][3]==null"></td>
                                <td class="text-right" v-else>@{{ datas['adhk'][3]['c_pdrb'] }}</td>
                            </table>
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
                form_data: {
                    wilayah: {!! json_encode($wilayah) !!},
                    tahun: {!! json_encode($tahun) !!},
                    triwulan: {!! json_encode($triwulan) !!},
                },
                datas: [],
                komponen: [],
            },
            watch: {
                form_data: {
                    handler(val) {
                        this.setDatas();
                    },
                    deep: true
                }
            },
            methods: {
                setDatas: function(event) {
                    var self = this;
                    $('#wait_progres').modal('show');

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    })

                    $.ajax({
                        url: "{{ url('/upload/pdrb/') }}",
                        method: 'post',
                        dataType: 'json',
                        data: {
                            wilayah: self.form_data.wilayah,
                            tahun: self.form_data.tahun,
                        },
                    }).done(function(data) {
                        self.datas = data.datas;
                        self.komponen = data.komponen;

                        $('#wait_progres').modal('hide');
                    }).fail(function(msg) {
                        console.log(JSON.stringify(msg));
                        $('#wait_progres').modal('hide');
                    });
                },
            }
        });

        $(document).ready(function() {
            vm.setDatas();
        });
    </script>
@endsection
