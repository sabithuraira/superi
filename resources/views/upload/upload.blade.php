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
                                        <select class="form-control  form-control-sm" readonly name="tahun" v-model="form_data.tahun">
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
                                        <select class="form-control  form-control-sm" name="triwulan" disabled v-model="form_data.triwulan">
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
                                <button v-if="!isApproveProvinsi && !isApproveAdmin" type="submit" class="btn btn-primary mx-1 float-right"
                                    name="action" value="1">Simpan</button>

                                @hasrole('approval_provinsi')
                                    <button v-if="!isApproveProvinsi && !isApproveAdmin && !isDataReject && isDataLengkap" type="submit"
                                        class="btn btn-success mx-1 float-right" name="action" value="3"><i class="fa fa-thumbs-o-up"></i>&nbsp; Approve
                                        Provinsi</button>
                                    <button v-if="isApproveProvinsi" type="button" class="btn btn-outline-success mx-1 float-right"><i
                                            class="fa fa-thumbs-o-up"></i>&nbsp;
                                        Sudah Approve Provinsi</button>

                                    <button v-if="isApproveAdmin" type="button" class="btn btn-outline-success mx-1 float-right"><i
                                            class="fa fa-thumbs-o-up"></i>&nbsp;
                                        Sudah Approve Admin</button>

                                    <button v-if="isApproveProvinsi && !isApproveAdmin" type="submit" class="btn btn-danger mx-1 float-right" name="action"
                                        value="4"><i class="fa fa-thumbs-o-down"></i>&nbsp; Batalkan Approve Provinsi</button>
                                @endhasrole
                            </div>
                        </div>
                    </form>
                    <ul class="nav nav-tabs mt-2">
                        <li class="nav-item"><a class="nav-link active show" data-toggle="tab" href="#adhb">ADHB</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#adhk">ADHK</a></li>
                    </ul>
                    <div class="tab-content">
                        {{-- ADHB --}}
                        <div class="tab-pane show active" id="adhb">
                            <span class="text-muted font-italic">*dalam juta rupiah</span>
                            <table class="table table-bordered table-sm m-b-0" style="min-width:100%">
                                <tr class="text-center">
                                    <th>Komponen</th>
                                    <template v-if="form_data.triwulan==4">
                                        <template v-for="item in [(form_data.tahun-2), (form_data.tahun-1)]">
                                            <th v-for="n in 4">@{{ item }}Q@{{ n }}</th>
                                        </template>
                                    </template>
                                    <th>@{{ form_data.tahun }}Q1</th>
                                    <th v-if="form_data.triwulan>=2">@{{ form_data.tahun }}Q2</th>
                                    <th v-if="form_data.triwulan>=3">@{{ form_data.tahun }}Q3</th>
                                    <th v-if="form_data.triwulan==4">@{{ form_data.tahun }}Q4</th>
                                </tr>

                                <!-- WHEN TRIWULAN=4-->
                                <template v-if="form_data.triwulan==4">
                                    <template v-for="(data, index) in komponen" :key="data.id">
                                        <tr>
                                            <td>
                                                <span v-if="data.no_komponen.length>=4 && data.no_komponen!='pdrb'">&nbsp;&nbsp;&nbsp;&nbsp;
                                                </span>
                                                <span v-if="data.no_komponen!='pdrb'">
                                                    @{{ data.no_komponen }}
                                                </span>
                                                @{{ data.nama_komponen }}
                                            </td>

                                            <template v-for="item in [(form_data.tahun-2), (form_data.tahun-1), form_data.tahun]">
                                                <template v-for="n in 4">
                                                    <td class="text-right">
                                                        <span v-if="datas['adhb'][((item - (form_data.tahun-2))*4)+(n-1)]!=null">
                                                            @{{ formatNumber(datas['adhb'][((item - (form_data.tahun - 2)) * 4) + (n - 1)]['c_' + data.no_komponen.replaceAll('.', '')]) }}
                                                            <br>
                                                            <sup v-if="parseNumber(hitungSelisih(datas_rilis['adhb'][((item - (form_data.tahun - 2)) * 4) + (n - 1)]['c_' + data.no_komponen.replaceAll('.', '')], datas['adhb'][((item - (form_data.tahun - 2)) * 4) + (n - 1)]['c_' + data.no_komponen.replaceAll('.', '')]) )!== 0"
                                                                :class="{
                                                                    'text-success': parseNumber(hitungSelisih(
                                                                        datas_rilis['adhb'][((item - (form_data.tahun - 2)) * 4) + (n - 1)][
                                                                            'c_' +
                                                                            data.no_komponen.replaceAll('.', '')
                                                                        ],
                                                                        datas['adhb'][((item - (form_data.tahun - 2)) * 4) + (n - 1)]['c_' +
                                                                            data
                                                                            .no_komponen.replaceAll('.', '')
                                                                        ])) > 0,
                                                                    'text-danger': parseNumber(hitungSelisih(
                                                                        datas_rilis['adhb'][((item - (form_data.tahun - 2)) * 4) + (n - 1)][
                                                                            'c_' +
                                                                            data.no_komponen.replaceAll('.', '')
                                                                        ],
                                                                        datas['adhb'][((item - (form_data.tahun - 2)) * 4) + (n - 1)]['c_' +
                                                                            data
                                                                            .no_komponen.replaceAll('.', '')
                                                                        ])) < 0
                                                                }">
                                                                @{{ hitungSelisih(datas_rilis['adhb'][((item - (form_data.tahun - 2)) * 4) + (n - 1)]['c_' + data.no_komponen.replaceAll('.', '')], datas['adhb'][((item - (form_data.tahun - 2)) * 4) + (n - 1)]['c_' + data.no_komponen.replaceAll('.', '')]) }}
                                                            </sup>
                                                        </span>
                                                    </td>
                                                </template>
                                            </template>
                                        </tr>
                                    </template>

                                </template>

                                <!-- WHEN TRIWULAN <=4-->
                                <template v-else>
                                    <template v-for="(data, index) in komponen" :key="data.id">
                                        <tr>
                                            <td>
                                                <span v-if="data.no_komponen.length>=4 && data.no_komponen!='pdrb'">&nbsp;&nbsp;&nbsp;&nbsp;
                                                </span>
                                                <span v-if="data.no_komponen!='pdrb'">
                                                    @{{ data.no_komponen }}
                                                </span>
                                                @{{ data.nama_komponen }}
                                            </td>

                                            <td class="text-right">
                                                <span v-if="datas['adhb'][0]!=null">
                                                    @{{ formatNumber(datas['adhb'][0]['c_' + data.no_komponen.replaceAll('.', '')]) }}
                                                </span>
                                                <br>
                                                <sup v-if="parseNumber(hitungSelisih(datas_rilis['adhb'][0]['c_' + data.no_komponen.replaceAll('.', '')], datas['adhb'][0]['c_' + data.no_komponen.replaceAll('.', '')]) )!== 0"
                                                    :class="{
                                                        'text-success': parseNumber(hitungSelisih(
                                                            datas_rilis['adhb'][0]['c_' + data.no_komponen.replaceAll('.', '')],
                                                            datas['adhb'][0]['c_' + data.no_komponen.replaceAll('.', '')])) > 0,
                                                        'text-danger': parseNumber(hitungSelisih(
                                                            datas_rilis['adhb'][0]['c_' + data.no_komponen.replaceAll('.', '')],
                                                            datas['adhb'][0]['c_' + data.no_komponen.replaceAll('.', '')])) < 0
                                                    }">
                                                    @{{ hitungSelisih(datas_rilis['adhb'][0]['c_' + data.no_komponen.replaceAll('.', '')], datas['adhb'][0]['c_' + data.no_komponen.replaceAll('.', '')]) }}
                                                </sup>
                                            </td>
                                            {{-- tw 2 --}}
                                            <template v-if="form_data.triwulan>=2">
                                                <td class="text-right">
                                                    <span v-if="datas['adhb'][1]!=null">
                                                        @{{ formatNumber(datas['adhb'][1]['c_' + data.no_komponen.replaceAll('.', '')]) }}
                                                        <br>
                                                        <sup v-if="parseNumber(hitungSelisih(datas_rilis['adhb'][1]['c_' + data.no_komponen.replaceAll('.', '')], datas['adhb'][1]['c_' + data.no_komponen.replaceAll('.', '')]) )!== 0"
                                                            :class="{
                                                                'text-success': parseNumber(hitungSelisih(
                                                                    datas_rilis['adhb'][1]['c_' + data.no_komponen.replaceAll('.', '')],
                                                                    datas['adhb'][1]['c_' + data.no_komponen.replaceAll('.', '')])) > 0,
                                                                'text-danger': parseNumber(hitungSelisih(
                                                                    datas_rilis['adhb'][1]['c_' + data.no_komponen.replaceAll('.', '')],
                                                                    datas['adhb'][1]['c_' + data.no_komponen.replaceAll('.', '')])) < 0
                                                            }">
                                                            @{{ hitungSelisih(datas_rilis['adhb'][1]['c_' + data.no_komponen.replaceAll('.', '')], datas['adhb'][1]['c_' + data.no_komponen.replaceAll('.', '')]) }}
                                                        </sup>
                                                </td>
                                            </template>
                                            {{-- tw 3 --}}
                                            <template v-if="form_data.triwulan>=3">
                                                <td class="text-right">
                                                    <span v-if="datas['adhb'][2]!=null">
                                                        @{{ formatNumber(datas['adhb'][2]['c_' + data.no_komponen.replaceAll('.', '')]) }}</span>
                                                    <br>
                                                    <sup v-if="parseNumber(hitungSelisih(datas_rilis['adhb'][0]['c_' + data.no_komponen.replaceAll('.', '')], datas['adhb'][0]['c_' + data.no_komponen.replaceAll('.', '')]) )!== 0"
                                                        :class="{
                                                            'text-success': parseNumber(hitungSelisih(
                                                                datas_rilis['adhb'][2]['c_' + data.no_komponen.replaceAll('.', '')],
                                                                datas['adhb'][2]['c_' + data.no_komponen.replaceAll('.', '')])) > 0,
                                                            'text-danger': parseNumber(hitungSelisih(
                                                                datas_rilis['adhb'][2]['c_' + data.no_komponen.replaceAll('.', '')],
                                                                datas['adhb'][2]['c_' + data.no_komponen.replaceAll('.', '')])) < 0
                                                        }">
                                                        @{{ hitungSelisih(datas_rilis['adhb'][2]['c_' + data.no_komponen.replaceAll('.', '')], datas['adhb'][2]['c_' + data.no_komponen.replaceAll('.', '')]) }}
                                                    </sup>
                                                </td>
                                            </template>
                                        </tr>
                                    </template>

                                </template>
                            </table>
                        </div>

                        <!-- ADHK -->
                        <div class="tab-pane" id="adhk">
                            <span class="text-muted font-italic">*dalam juta rupiah</span>
                            <table class="table table-bordered m-b-0" style="min-width:100%">
                                <tr class="text-center">
                                    <th>Komponen</th>
                                    <template v-if="form_data.triwulan==4">
                                        <template v-for="item in [(form_data.tahun-2), (form_data.tahun-1)]">
                                            <th v-for="n in 4">@{{ item }}Q@{{ n }}</th>
                                        </template>
                                    </template>
                                    <th>@{{ form_data.tahun }}Q1</th>
                                    <th v-if="form_data.triwulan>=2">@{{ form_data.tahun }}Q2</th>
                                    <th v-if="form_data.triwulan>=3">@{{ form_data.tahun }}Q3</th>
                                    <th v-if="form_data.triwulan>=4">@{{ form_data.tahun }}Q4</th>
                                </tr>

                                <!-- WHEN TRIWULAN==4-->
                                <template v-if="form_data.triwulan==4">
                                    <template v-for="(data, index) in komponen" :key="data.id">
                                        <tr>
                                            <td>
                                                <span v-if="data.no_komponen.length>=4 && data.no_komponen!='pdrb'">&nbsp;&nbsp;&nbsp;&nbsp;
                                                </span>
                                                <span v-if="data.no_komponen!='pdrb'">
                                                    @{{ data.no_komponen }}
                                                </span>
                                                @{{ data.nama_komponen }}
                                            </td>

                                            <template v-for="item in [(form_data.tahun-2), (form_data.tahun-1), form_data.tahun]">
                                                <template v-for="n in 4">
                                                    <td class="text-right">
                                                        <span v-if="datas['adhk'][((item - (form_data.tahun-2))*4)+(n-1)]!=null">
                                                            @{{ formatNumber(datas['adhk'][((item - (form_data.tahun - 2)) * 4) + (n - 1)]['c_' + data.no_komponen.replaceAll('.', '')]) }}
                                                        </span>
                                                        <br>
                                                        <sup v-if="parseNumber(hitungSelisih(datas_rilis['adhk'][((item - (form_data.tahun - 2)) * 4) + (n - 1)]['c_' + data.no_komponen.replaceAll('.', '')], datas['adhk'][((item - (form_data.tahun - 2)) * 4) + (n - 1)]['c_' + data.no_komponen.replaceAll('.', '')]) )!== 0"
                                                            :class="{
                                                                'text-success': parseNumber(hitungSelisih(
                                                                    datas_rilis['adhk'][((item - (form_data.tahun - 2)) * 4) + (n - 1)]['c_' +
                                                                        data.no_komponen.replaceAll('.', '')
                                                                    ],
                                                                    datas['adhk'][((item - (form_data.tahun - 2)) * 4) + (n - 1)]['c_' + data
                                                                        .no_komponen.replaceAll('.', '')
                                                                    ])) > 0,
                                                                'text-danger': parseNumber(hitungSelisih(
                                                                    datas_rilis['adhk'][((item - (form_data.tahun - 2)) * 4) + (n - 1)]['c_' +
                                                                        data.no_komponen.replaceAll('.', '')
                                                                    ],
                                                                    datas['adhk'][((item - (form_data.tahun - 2)) * 4) + (n - 1)]['c_' + data
                                                                        .no_komponen.replaceAll('.', '')
                                                                    ])) < 0
                                                            }">
                                                            @{{ hitungSelisih(datas_rilis['adhk'][((item - (form_data.tahun - 2)) * 4) + (n - 1)]['c_' + data.no_komponen.replaceAll('.', '')], datas['adhk'][((item - (form_data.tahun - 2)) * 4) + (n - 1)]['c_' + data.no_komponen.replaceAll('.', '')]) }}
                                                        </sup>
                                                    </td>
                                                </template>
                                            </template>
                                        </tr>
                                    </template>

                                </template>

                                <!-- WHEN TRIWULAN <=4-->
                                <template v-else>
                                    <template v-for="(data, index) in komponen" :key="data.id">
                                        <tr>
                                            <td>
                                                <span v-if="data.no_komponen.length>=4 && data.no_komponen!='pdrb'">&nbsp;&nbsp;&nbsp;&nbsp;
                                                </span>
                                                <span v-if="data.no_komponen!='pdrb'">
                                                    @{{ data.no_komponen }}
                                                </span>
                                                @{{ data.nama_komponen }}
                                            </td>

                                            <td class="text-right">
                                                <span v-if="datas['adhk'][0]!=null">
                                                    @{{ formatNumber(datas['adhk'][0]['c_' + data.no_komponen.replaceAll('.', '')]) }}
                                                </span>
                                                <br>
                                                <sup v-if="parseNumber(hitungSelisih(datas_rilis['adhk'][0]['c_' + data.no_komponen.replaceAll('.', '')], datas['adhk'][0]['c_' + data.no_komponen.replaceAll('.', '')]) )!== 0"
                                                    :class="{
                                                        'text-success': parseNumber(hitungSelisih(
                                                            datas_rilis['adhk'][0]['c_' + data.no_komponen.replaceAll('.', '')],
                                                            datas['adhk'][0]['c_' + data.no_komponen.replaceAll('.', '')])) > 0,
                                                        'text-danger': parseNumber(hitungSelisih(
                                                            datas_rilis['adhk'][0]['c_' + data.no_komponen.replaceAll('.', '')],
                                                            datas['adhk'][0]['c_' + data.no_komponen.replaceAll('.', '')])) < 0
                                                    }">
                                                    @{{ hitungSelisih(datas_rilis['adhk'][0]['c_' + data.no_komponen.replaceAll('.', '')], datas['adhk'][0]['c_' + data.no_komponen.replaceAll('.', '')]) }}
                                                </sup>
                                            </td>

                                            <template v-if="form_data.triwulan>=2">
                                                <td class="text-right">
                                                    <span v-if="datas['adhk'][1]!=null">
                                                        @{{ formatNumber(datas['adhk'][1]['c_' + data.no_komponen.replaceAll('.', '')]) }}
                                                    </span>
                                                    <br>
                                                    <sup v-if="parseNumber(hitungSelisih(datas_rilis['adhk'][1]['c_' + data.no_komponen.replaceAll('.', '')], datas['adhk'][1]['c_' + data.no_komponen.replaceAll('.', '')]) )!== 0"
                                                        :class="{
                                                            'text-success': parseNumber(hitungSelisih(
                                                                datas_rilis['adhk'][1]['c_' + data.no_komponen.replaceAll('.', '')],
                                                                datas['adhk'][1]['c_' + data.no_komponen.replaceAll('.', '')])) > 0,
                                                            'text-danger': parseNumber(hitungSelisih(
                                                                datas_rilis['adhk'][1]['c_' + data.no_komponen.replaceAll('.', '')],
                                                                datas['adhk'][1]['c_' + data.no_komponen.replaceAll('.', '')])) < 0
                                                        }">
                                                        @{{ hitungSelisih(datas_rilis['adhk'][1]['c_' + data.no_komponen.replaceAll('.', '')], datas['adhk'][1]['c_' + data.no_komponen.replaceAll('.', '')]) }}
                                                    </sup>
                                                </td>
                                            </template>

                                            <template v-if="form_data.triwulan>=3">
                                                <td class="text-right">
                                                    <span v-if="datas['adhk'][2]!=null">
                                                        @{{ formatNumber(datas['adhk'][2]['c_' + data.no_komponen.replaceAll('.', '')]) }}
                                                    </span>
                                                    <br>
                                                    <sup v-if="parseNumber(hitungSelisih(datas_rilis['adhk'][2]['c_' + data.no_komponen.replaceAll('.', '')], datas['adhk'][2]['c_' + data.no_komponen.replaceAll('.', '')]) )!== 0"
                                                        :class="{
                                                            'text-success': parseNumber(hitungSelisih(
                                                                datas_rilis['adhk'][2]['c_' + data.no_komponen.replaceAll('.', '')],
                                                                datas['adhk'][2]['c_' + data.no_komponen.replaceAll('.', '')])) > 0,
                                                            'text-danger': parseNumber(hitungSelisih(
                                                                datas_rilis['adhk'][2]['c_' + data.no_komponen.replaceAll('.', '')],
                                                                datas['adhk'][2]['c_' + data.no_komponen.replaceAll('.', '')])) < 0
                                                        }">
                                                        @{{ hitungSelisih(datas_rilis['adhk'][2]['c_' + data.no_komponen.replaceAll('.', '')], datas['adhk'][2]['c_' + data.no_komponen.replaceAll('.', '')]) }}
                                                    </sup>
                                                </td>
                                            </template>
                                        </tr>
                                    </template>

                                </template>
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
                isApproveProvinsi: true,
                isApproveAdmin: true,
                isDataLengkap: true,
                isDataReject: true,
                datas: [],
                datas_rilis: [],
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
                        self.isApproveAdmin = true;
                        self.isApproveProvinsi = true;
                        self.isDataLengkap = true;
                        self.isDataReject = true;

                        self.datas = data.datas;
                        self.datas_rilis = data.data_rilis;
                        self.komponen = data.komponen;
                        self.komponen.push({
                            'id': '',
                            'no_komponen': 'pdrb',
                            'nama_komponen': 'PDRB',
                            'status_aktif': '',
                            'update_at': '',
                            'updated_by': '',
                            'create_at': '',
                            'created_by': ''
                        });
                        ///
                        // console.log(data)
                        let total_data = self.form_data.triwulan;
                        if (self.form_data.triwulan == 4) total_data = 12;

                        for (let i = 0; i < total_data; ++i) {
                            // for (const el of self.datas['adhb']) {
                            let el_adhb = self.datas['adhb'][i];
                            let el_adhk = self.datas['adhk'][i];

                            if (el_adhb == null || el_adhk == null) self.isDataLengkap = false;
                            if (el_adhb == null || el_adhk == null || el_adhb['status_data'] != 2 ||
                                el_adhk['status_data'] != 2) self.isApproveProvinsi = false;
                            if (el_adhb == null || el_adhk == null || el_adhb['status_data'] != 3 ||
                                el_adhk['status_data'] != 3) self.isApproveAdmin = false;
                            if (el_adhb == null || el_adhk == null || el_adhb['status_data'] != 4 ||
                                el_adhk['status_data'] != 4) self.isDataReject = false;
                        }
                        // for (const el of self.datas['adhb']) {
                        //     if(el==null) self.isDataLengkap = false;

                        //     if(el==null || el['status_data']==1){
                        //         self.isApproveProvinsi = false;
                        //         self.isApproveAdmin = false;
                        //     }

                        //     if(el==null || el['status_data']==2) self.isApproveAdmin = false;
                        //     // if(!self.isApproveProvinsi && !self.isApproveAdmin) break;
                        // }

                        // for (const el of self.datas['adhk']) {
                        //     if(el==null) self.isDataLengkap = false;
                        //     if(el==null || el['status_data']==1){
                        //         self.isApproveProvinsi = false;
                        //         self.isApproveAdmin = false;
                        //     }

                        //     if(el==null || el['status_data']==2) self.isApproveAdmin = false;
                        //     // if(!self.isApproveProvinsi && !self.isApproveAdmin) break;
                        // }

                        // console.log(self.datas);
                        // console.log(self.komponen);
                        $('#wait_progres').modal('hide');
                        // console.log(self.datas_rilis)
                    }).fail(function(msg) {
                        console.log(JSON.stringify(msg));
                        $('#wait_progres').modal('hide');
                    });
                },

                hitungSelisih(val_a, val_b) {
                    const values = this.formatNumber(this.parseNumber(val_b) - this.parseNumber(val_a));
                    return values;


                },
                parseNumber(value) {
                    if (value === null || value === undefined) return 0;
                    if (typeof value === 'number') return value;
                    if (typeof value === 'string') {
                        if (/^-?\d+\.?\d*$/.test(value)) {
                            return parseFloat(value);
                        }
                        let cleanValue = value.replace(/\./g, '');
                        cleanValue = cleanValue.replace(',', '.');
                        return parseFloat(cleanValue);
                    }
                    return 0;
                },

                formatNumber(value) {
                    return new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(value);
                },
            }
        });

        $(document).ready(function() {
            vm.setDatas();
        });
    </script>
@endsection
