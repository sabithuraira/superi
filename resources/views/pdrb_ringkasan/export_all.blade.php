<table border="1px solid black" style="border-spacing: 0;">
    @foreach ($table as $tbl)
        @if (in_array($tbl['id'], ['1.1', '1.2'], true))
            <thead>
                <tr>
                    <th>
                        <b>{{ $tbl['name'] }}</b>
                    </th>
                </tr>
                <tr>
                    <th></th>
                </tr>
                <tr>
                    <th rowspan="3"
                        style="border: 3px solid black; background-color:#a6a6a6; text-align: center; width:300px">
                        <b>Komponen</b>
                    </th>
                    @foreach ($tbl['periode_filter'] as $periode)
                        <th colspan="6" style="border: 3px solid black; background-color:#a6a6a6; text-align: center">
                            <b>{{ $periode }}</b>
                        </th>
                    @endforeach
                </tr>
                <tr style="text-align: center">
                    @foreach ($tbl['periode_filter'] as $periode)
                        <th style="white-space:nowrap; border: 3px solid black; background-color:#a6a6a6; text-align: center"
                            colspan="2">
                            <b>Q-to-Q</b>
                        </th>
                        <th style="white-space:nowrap; border: 3px solid black; background-color:#a6a6a6; text-align: center"
                            colspan="2">
                            <b>Y-on-Y</b>
                        </th>
                        <th style="white-space:nowrap; border: 3px solid black; background-color:#a6a6a6; text-align: center"
                            colspan="2">
                            <b>C-to-C</b>
                        </th>
                    @endforeach
                </tr>
                <tr>
                    @foreach ($tbl['periode_filter'] as $periode)
                        @for ($i = 1; $i <= 3; $i++)
                            <th
                                style="white-space:nowrap; border: 3px solid black; background-color:#a6a6a6; text-align: center">
                                <b>Kabupaten</b>
                            </th>
                            <th
                                style="white-space:nowrap; border: 3px solid black; background-color:#a6a6a6; text-align: center">
                                <b>Provinsi</b>
                            </th>
                        @endfor
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($tbl['data'] as $dt)
                    @php
                        $shouldBold = strlen($dt['komponen']) < 4 || $dt['komponen'] == 'c_pdrb';
                    @endphp
                    <tr style="@if ($shouldBold) background-color: #d7d7d7; @endif">
                        <td
                            style="border: 3px solid black; 
                            @if ($shouldBold) font-weight: bold; background-color:#d7d7d7; @endif">
                            {{ $dt['komponen_name'] }}
                        </td>
                        @foreach ($tbl['periode_filter'] as $periode)
                            @php
                                $qtqProv = $dt[$periode . 'qtq_prov'] ?? null;
                                $qtqKab = $dt[$periode . 'qtq_kab'] ?? null;
                                $yoyProv = $dt[$periode . 'yoy_prov'] ?? null;
                                $yoyKab = $dt[$periode . 'yoy_kab'] ?? null;
                                $ctcProv = $dt[$periode . 'ctc_prov'] ?? null;
                                $ctcKab = $dt[$periode . 'ctc_kab'] ?? null;
                                $shouldHighlight =
                                    ($qtqProv && $qtqKab && $qtqProv * $qtqKab < 0) ||
                                    ($yoyProv && $yoyKab && $yoyProv * $yoyKab < 0) ||
                                    ($ctcProv && $ctcKab && $ctcProv * $ctcKab < 0);
                            @endphp
                            <td class="text-right"
                                style="border: 3px solid black; @if ($shouldBold) font-weight: bold; background-color:#d7d7d7; @endif 
                                @if ($shouldHighlight) background-color: yellow @endif">
                                {{ $qtqKab ? round($qtqKab, 2) : '' }}
                            </td>
                            <td class="text-right"
                                style="border: 3px solid black; @if ($shouldBold) font-weight: bold; background-color:#d7d7d7; @endif  
                                @if ($shouldHighlight) background-color: yellow @endif">
                                {{ array_key_exists($periode . 'qtq_prov', $dt) && $dt[$periode . 'qtq_prov'] ? round($dt[$periode . 'qtq_prov'], 2) : '' }}
                            </td>

                            <td class="text-right"
                                style="border: 3px solid black; @if ($shouldBold) font-weight: bold;  background-color:#d7d7d7; @endif  @if ($shouldHighlight) background-color: yellow @endif">
                                {{ array_key_exists($periode . 'yoy_kab', $dt) && $dt[$periode . 'yoy_kab'] ? round($dt[$periode . 'yoy_kab'], 2) : '' }}
                            </td>
                            <td class="text-right"
                                style="border: 3px solid black; @if ($shouldBold) font-weight: bold;  background-color:#d7d7d7; @endif  @if ($shouldHighlight) background-color: yellow @endif">
                                {{ array_key_exists($periode . 'yoy_prov', $dt) && $dt[$periode . 'yoy_prov'] ? round($dt[$periode . 'yoy_prov'], 2) : '' }}
                            </td>
                            <td class="text-right"
                                style="border: 3px solid black; @if ($shouldBold) font-weight: bold;  background-color:#d7d7d7; @endif  @if ($shouldHighlight) background-color: yellow @endif">
                                {{ array_key_exists($periode . 'ctc_kab', $dt) && $dt[$periode . 'ctc_kab'] ? round($dt[$periode . 'ctc_kab'], 2) : '' }}
                            </td>
                            <td class="text-right"
                                style="border: 3px solid black; @if ($shouldBold) font-weight: bold;  background-color:#d7d7d7; @endif  @if ($shouldHighlight) background-color: yellow @endif">
                                {{ array_key_exists($periode . 'ctc_prov', $dt) && $dt[$periode . 'ctc_prov'] ? round($dt[$periode . 'ctc_prov'], 2) : '' }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                <tr>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                </tr>
            </tbody>
        @elseif(in_array($tbl['id'], ['1.3'], true))
            <thead>
                <tr>
                    <th>
                        <b>{{ $tbl['name'] }}</b>
                    </th>
                </tr>
                <tr>
                    <th><b>Periode :</b></th>
                    <th><b>{{ $tbl['periode_filter'] }}</b></th>
                </tr>
                <tr>
                    <th rowspan="2"style="border: 3px solid black;  background-color:#a6a6a6; text-align: center">
                        <b> Kabupaten/Kota</b>
                    </th>
                    <th colspan="2"style="border: 3px solid black;  background-color:#a6a6a6; text-align: center">
                        <b>Pertumbuhan YoY</b>
                    </th>
                    <th colspan="2"style="border: 3px solid black;  background-color:#a6a6a6; text-align: center">
                        <b>Pertumbuhan QtQ</b>
                    </th>
                    <th colspan="2"style="border: 3px solid black;  background-color:#a6a6a6; text-align: center">
                        <b>Pertumbuhan CtC</b>
                    </th>
                    <th rowspan="2"style="border: 3px solid black;  background-color:#a6a6a6; text-align: center">
                        <b>Implisit YoY {{ $tbl['periode_filter'] }}</b>
                    </th>
                    <th rowspan="2"style="border: 3px solid black;  background-color:#a6a6a6; text-align: center">
                        <b>Share terhadap provinsi</b>
                    </th>
                </tr>
                <tr>
                    @php
                        $parts = explode('Q', $tbl['periode_filter']);
                        $tahun = isset($parts[0]) ? $parts[0] : null;
                        $quarter = isset($parts[1]) ? 'Q' . $parts[1] : null;
                    @endphp
                    <th style="border: 3px solid black; background-color:#a6a6a6; text-align: center">
                        <b>{{ $tahun - 1 . $quarter }}</b>
                    </th>
                    <th style="border: 3px solid black; background-color:#a6a6a6; text-align: center">
                        <b>{{ $tbl['periode_filter'] }}</b>
                    </th>
                    <th style="border: 3px solid black; background-color:#a6a6a6; text-align: center">
                        <b> {{ $tahun - 1 . $quarter }}</b>
                    </th>
                    <th style="border: 3px solid black; background-color:#a6a6a6; text-align: center">
                        <b>{{ $tbl['periode_filter'] }}</b>
                    </th>
                    <th style="border: 3px solid black; background-color:#a6a6a6; text-align: center">
                        <b> {{ $tahun - 1 . $quarter }}</b>
                    </th>
                    <th style="border: 3px solid black; background-color:#a6a6a6; text-align: center">
                        <b>{{ $tbl['periode_filter'] }}</b>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tbl['data'] as $dt)
                    @php
                        $shouldBold = $dt['id'] == '00';
                    @endphp
                    <tr>
                        <td
                            style="border: 3px solid black; @if ($shouldBold) background-color:#d7d7d7; font-weight: bold; @endif">
                            [{{ $dt['id'] }}] {{ $dt['alias'] }}
                        </td>
                        <td
                            style="border: 3px solid black; @if ($shouldBold) background-color:#d7d7d7; font-weight: bold; @endif">
                            {{ array_key_exists('yoy_prev', $dt) && $dt['yoy_prev'] ? round($dt['yoy_prev'], 2) : '' }}
                        </td>
                        <td
                            style="border: 3px solid black; @if ($shouldBold) background-color:#d7d7d7; font-weight: bold; @endif">
                            {{ array_key_exists('yoy_current', $dt) && $dt['yoy_current'] ? round($dt['yoy_current'], 2) : '' }}
                        </td>
                        <td
                            style="border: 3px solid black; @if ($shouldBold) background-color:#d7d7d7; font-weight: bold; @endif">
                            {{ array_key_exists('qtq_prev', $dt) && $dt['qtq_prev'] ? round($dt['qtq_prev'], 2) : '' }}
                        </td>
                        <td
                            style="border: 3px solid black; @if ($shouldBold) background-color:#d7d7d7; font-weight: bold; @endif">
                            {{ array_key_exists('qtq_current', $dt) && $dt['qtq_current'] ? round($dt['qtq_current'], 2) : '' }}
                        </td>
                        <td
                            style="border: 3px solid black; @if ($shouldBold) background-color:#d7d7d7; font-weight: bold; @endif">
                            {{ array_key_exists('ctc_prev', $dt) && $dt['ctc_prev'] ? round($dt['ctc_prev'], 2) : '' }}
                        </td>
                        <td
                            style="border: 3px solid black; @if ($shouldBold) background-color:#d7d7d7; font-weight: bold; @endif">
                            {{ array_key_exists('ctc_current', $dt) && $dt['ctc_current'] ? round($dt['ctc_current'], 2) : '' }}
                        </td>
                        <td
                            style="border: 3px solid black; @if ($shouldBold) background-color:#d7d7d7; font-weight: bold; @endif">
                            {{ array_key_exists('implisit_yoy', $dt) && $dt['implisit_yoy'] ? round($dt['implisit_yoy'], 2) : '' }}
                        </td>
                        <td
                            style="border: 3px solid black; @if ($shouldBold) background-color:#d7d7d7; font-weight: bold; @endif">
                            {{ array_key_exists('share_prov', $dt) && $dt['share_prov'] ? round($dt['share_prov'], 2) : '' }}
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                </tr>
            </tbody>
        @elseif(in_array($tbl['id'], ['1.4', '1.5', '1.6', '1.7', '1.8', '1.9', '1.10', '1.15', '1.16'], true))
            <thead>
                <tr>
                    <th>
                        <b>{{ $tbl['name'] }}</b>
                    </th>
                </tr>
                <tr>
                    <th><b>Periode :</b></th>
                    <th><b>{{ $tbl['periode_filter'] }}</b></th>
                </tr>
                <tr>
                    <th style="border: 3px solid black;  background-color:#a6a6a6; text-align: center">
                        <b> Kabupaten/Kota</b>
                    </th>
                    @foreach ($tbl['komponens'] as $komp)
                        <th style="border: 3px solid black;  background-color:#a6a6a6; text-align: center">
                            <b> {{ $komp['alias'] }}</b>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($tbl['data'] as $dt)
                    @php
                        $shouldBold = $dt['id'] == '00';
                    @endphp
                    <tr>
                        <td
                            style="border: 3px solid black; @if ($shouldBold) background-color:#d7d7d7; font-weight: bold; @endif">
                            [{{ $dt['id'] }}] {{ $dt['alias'] }}
                        </td>
                        @foreach ($tbl['komponens'] as $key => $komp)
                            <td
                                style="border: 3px solid black; @if ($shouldBold) background-color:#d7d7d7; font-weight: bold; @endif">
                                {{ array_key_exists($komp['id'], $dt) && $dt[$komp['id']]
                                    ? number_format(round($dt[$komp['id']], 2), 2, ',', '.')
                                    : '' }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                <tr>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                </tr>
            </tbody>
        @elseif(in_array($tbl['id'], ['1.11', '1.12'], true))
            <thead>
                <tr>
                    <th>
                        <b>{{ $tbl['name'] }}</b>
                    </th>
                </tr>
                <tr>
                    <th></th>
                </tr>
                <tr>
                    <th rowspan="3"style="border: 3px solid black;  background-color:#a6a6a6; text-align: center">
                        <b>Komponen</b>
                    </th>
                    @foreach ($tbl['periode_filter'] as $periode)
                        <th
                            colspan="2"style="border: 3px solid black;  background-color:#a6a6a6; text-align: center">
                            <b>{{ $periode }}</b>
                        </th>
                    @endforeach

                </tr>
                <tr>
                    @foreach ($tbl['periode_filter'] as $periode)
                        <th colspan="2"
                            style="border: 3px solid black; background-color:#a6a6a6; text-align: center">
                            <b> Diskrepansi</b>
                        </th>
                    @endforeach
                </tr>
                <tr class="text-center">
                    @foreach ($tbl['periode_filter'] as $periode)
                        <th style="border: 3px solid black;  background-color:#a6a6a6; text-align: center">
                            <b>ADHB</b>
                        </th>
                        <th style="border: 3px solid black;  background-color:#a6a6a6; text-align: center">
                            <b>ADHK</b>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($tbl['data'] as $dt)
                    @php
                        $shouldBold = strlen($dt['komponen']) < 4 || $dt['komponen'] == 'c_pdrb';
                    @endphp
                    <tr>
                        <td
                            style="border: 3px solid black; @if ($shouldBold) background-color:#d7d7d7; font-weight: bold; @endif">
                            {{ $dt['komponen_name'] }}
                        </td>
                        @foreach ($tbl['periode_filter'] as $periode)
                            <td
                                style="border: 3px solid black; @if ($shouldBold) background-color:#d7d7d7; font-weight: bold; @endif">
                                {{ array_key_exists($periode . 'adhb', $dt) && $dt[$periode . 'adhb'] ? round($dt[$periode . 'adhb'], 2) : '' }}
                            </td>
                            <td
                                style="border: 3px solid black; @if ($shouldBold) background-color:#d7d7d7; font-weight: bold; @endif">
                                {{ array_key_exists($periode . 'adhk', $dt) && $dt[$periode . 'adhb'] ? round($dt[$periode . 'adhk'], 2) : '' }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                <tr>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                </tr>
            </tbody>
        @elseif(in_array($tbl['id'], ['1.13'], true))
            <thead>
                <tr>
                    <th>
                        <b>{{ $tbl['name'] }}</b>
                    </th>
                </tr>
                <tr>
                    <th><b>wilayah :</b></th>
                    <th><b>{{ $tbl['wilayah_filter'] }}</b></th>
                    <th><b>Periode :</b></th>
                    <th><b>{{ $tbl['periode_filter'] }}</b></th>
                </tr>
                <tr>
                    <th style="border: 3px solid black;  background-color:#a6a6a6; text-align: center">
                        <b>Komponen</b>
                    </th>
                    <th style="border: 3px solid black;  background-color:#a6a6a6; text-align: center">
                        <b>Pertumbuhan YoY</b>
                    </th>
                    <th style="border: 3px solid black;  background-color:#a6a6a6; text-align: center">
                        <b>Pertumbuhan QtQ</b>
                    </th>
                    <th style="border: 3px solid black;  background-color:#a6a6a6; text-align: center">
                        <b>Pertumbuhan CtC</b>
                    </th>
                    <th style="border: 3px solid black;  background-color:#a6a6a6; text-align: center">
                        <b>Implisit YoY </b>
                    </th>
                    <th style="border: 3px solid black;  background-color:#a6a6a6; text-align: center">
                        <b>Implisit QtQ </b>
                    </th>
                    <th style="border: 3px solid black;  background-color:#a6a6a6; text-align: center">
                        <b>Implisit CtC </b>
                    </th>
                </tr>

            </thead>
            <tbody>
                @foreach ($tbl['data'] as $dt)
                    @php
                        $shouldBold = strlen($dt['id']) < 4 || $dt['id'] == 'c_pdrb';
                    @endphp
                    <tr>
                        <td
                            style="border: 3px solid black; @if ($shouldBold) background-color:#d7d7d7; font-weight: bold; @endif">
                            {{ $dt['name'] }}
                        </td>
                        <td
                            style="border: 3px solid black; @if ($shouldBold) background-color:#d7d7d7; font-weight: bold; @endif">
                            {{ array_key_exists('yoy', $dt) && $dt['yoy'] ? round($dt['yoy'], 2) : '' }}
                        </td>
                        <td
                            style="border: 3px solid black; @if ($shouldBold) background-color:#d7d7d7; font-weight: bold; @endif">
                            {{ array_key_exists('qtq', $dt) && $dt['qtq'] ? round($dt['qtq'], 2) : '' }}
                        </td>
                        <td
                            style="border: 3px solid black; @if ($shouldBold) background-color:#d7d7d7; font-weight: bold; @endif">
                            {{ array_key_exists('ctc', $dt) && $dt['ctc'] ? round($dt['ctc'], 2) : '' }}
                        </td>
                        <td
                            style="border: 3px solid black; @if ($shouldBold) background-color:#d7d7d7; font-weight: bold; @endif">
                            {{ array_key_exists('implisit_yoy', $dt) && $dt['implisit_yoy'] ? round($dt['implisit_yoy'], 2) : '' }}
                        </td>
                        <td
                            style="border: 3px solid black; @if ($shouldBold) background-color:#d7d7d7; font-weight: bold; @endif">
                            {{ array_key_exists('implisit_qtq', $dt) && $dt['implisit_qtq'] ? round($dt['implisit_qtq'], 2) : '' }}
                        </td>
                        <td
                            style="border: 3px solid black; @if ($shouldBold) background-color:#d7d7d7; font-weight: bold; @endif">
                            {{ array_key_exists('implisit_ctc', $dt) && $dt['implisit_ctc'] ? round($dt['implisit_ctc'], 2) : '' }}
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                </tr>
            </tbody>
        @elseif(in_array($tbl['id'], ['1.14'], true))
            <thead>
                <tr>
                    <th>
                        <b>{{ $tbl['name'] }}</b>
                    </th>
                </tr>
                <tr>
                    <th><b>wilayah :</b></th>
                    <th><b>{{ $tbl['wilayah_filter'] }}</b></th>
                    <th><b>Periode :</b></th>
                    <th><b>{{ $tbl['periode_filter'] }}</b></th>
                </tr>
                <tr>
                    <th rowspan="2" style="border: 3px solid black;  background-color:#a6a6a6; text-align: center">
                        <b>Komponen </b>
                    </th>
                    <th colspan="2" style="border: 3px solid black;  background-color:#a6a6a6; text-align: center">
                        <b>Pertumbuhan YoY</b>
                    </th>
                    <th colspan="2" style="border: 3px solid black;  background-color:#a6a6a6; text-align: center">
                        <b>Pertumbuhan QtQ</b>
                    </th>
                    <th colspan="2" style="border: 3px solid black;  background-color:#a6a6a6; text-align: center">
                        <b>Pertumbuhan CtC</b>
                    </th>
                    <th colspan="2" style="border: 3px solid black;  background-color:#a6a6a6; text-align: center">
                        <b>Implisit YoY </b>
                    </th>
                    <th colspan="2" style="border: 3px solid black;  background-color:#a6a6a6; text-align: center">
                        <b>Implisit QtQ </b>
                    </th>
                    <th colspan="2" style="border: 3px solid black;  background-color:#a6a6a6; text-align: center">
                        <b>Implisit CtC </b>
                    </th>
                </tr>
                <tr>
                    <td style="border: 3px solid black;  background-color:#a6a6a6; text-align: center">
                        <b>Rilis </b>
                    </td>
                    <td style="border: 3px solid black;  background-color:#a6a6a6; text-align: center">
                        <b>Revisi </b>
                    </td>
                    <td style="border: 3px solid black;  background-color:#a6a6a6; text-align: center">
                        <b>Rilis </b>
                    </td>
                    <td style="border: 3px solid black;  background-color:#a6a6a6; text-align: center">
                        <b>Revisi </b>
                    </td>
                    <td style="border: 3px solid black;  background-color:#a6a6a6; text-align: center">
                        <b>Rilis </b>
                    </td>
                    <td style="border: 3px solid black;  background-color:#a6a6a6; text-align: center">
                        <b>Revisi </b>
                    </td>
                    <td style="border: 3px solid black;  background-color:#a6a6a6; text-align: center">
                        <b>Rilis </b>
                    </td>
                    <td style="border: 3px solid black;  background-color:#a6a6a6; text-align: center">
                        <b>Revisi </b>
                    </td>
                    <td style="border: 3px solid black;  background-color:#a6a6a6; text-align: center">
                        <b>Rilis </b>
                    </td>
                    <td style="border: 3px solid black;  background-color:#a6a6a6; text-align: center">
                        <b>Revisi </b>
                    </td>
                    <td style="border: 3px solid black;  background-color:#a6a6a6; text-align: center">
                        <b>Rilis </b>
                    </td>
                    <td style="border: 3px solid black;  background-color:#a6a6a6; text-align: center">
                        <b>Revisi </b>
                    </td>

                </tr>
            </thead>
            <tbody>
                @foreach ($tbl['data'] as $dt)
                    @php
                        $shouldBold = strlen($dt['id']) < 4 || $dt['id'] == 'c_pdrb';
                    @endphp
                    <tr>
                        <td
                            style="border: 3px solid black; @if ($shouldBold) background-color:#d7d7d7; font-weight: bold; @endif">
                            {{ $dt['name'] }}
                        </td>
                        <td
                            style="border: 3px solid black; @if ($shouldBold) background-color:#d7d7d7; font-weight: bold; @endif">
                            {{ array_key_exists('yoy_rilis', $dt) && $dt['yoy_rilis'] ? round($dt['yoy_rilis'], 2) : '' }}
                        </td>
                        <td
                            style="border: 3px solid black; @if ($shouldBold) background-color:#d7d7d7; font-weight: bold; @endif">
                            {{ array_key_exists('yoy_revisi', $dt) && $dt['yoy_revisi'] ? round($dt['yoy_revisi'], 2) : '' }}
                        </td>
                        <td
                            style="border: 3px solid black; @if ($shouldBold) background-color:#d7d7d7; font-weight: bold; @endif">
                            {{ array_key_exists('qtq_rilis', $dt) && $dt['qtq_rilis'] ? round($dt['qtq_rilis'], 2) : '' }}
                        </td>
                        <td
                            style="border: 3px solid black; @if ($shouldBold) background-color:#d7d7d7; font-weight: bold; @endif">
                            {{ array_key_exists('qtq_revisi', $dt) && $dt['qtq_revisi'] ? round($dt['qtq_revisi'], 2) : '' }}
                        </td>
                        <td
                            style="border: 3px solid black; @if ($shouldBold) background-color:#d7d7d7; font-weight: bold; @endif">
                            {{ array_key_exists('ctc_rilis', $dt) && $dt['ctc_rilis'] ? round($dt['ctc_rilis'], 2) : '' }}
                        </td>
                        <td
                            style="border: 3px solid black; @if ($shouldBold) background-color:#d7d7d7; font-weight: bold; @endif">
                            {{ array_key_exists('ctc_revisi', $dt) && $dt['ctc_revisi'] ? round($dt['ctc_revisi'], 2) : '' }}
                        </td>
                        <td
                            style="border: 3px solid black; @if ($shouldBold) background-color:#d7d7d7; font-weight: bold; @endif">
                            {{ array_key_exists('implisit_yoy_rilis', $dt) && $dt['implisit_yoy_rilis'] ? round($dt['implisit_yoy_rilis'], 2) : '' }}
                        </td>
                        <td
                            style="border: 3px solid black; @if ($shouldBold) background-color:#d7d7d7; font-weight: bold; @endif">
                            {{ array_key_exists('implisit_yoy_revisi', $dt) && $dt['implisit_yoy_revisi'] ? round($dt['implisit_yoy_revisi'], 2) : '' }}
                        </td>
                        <td
                            style="border: 3px solid black; @if ($shouldBold) background-color:#d7d7d7; font-weight: bold; @endif">
                            {{ array_key_exists('implisit_qtq_rilis', $dt) && $dt['implisit_qtq_rilis'] ? round($dt['implisit_qtq_rilis'], 2) : '' }}
                        </td>
                        <td
                            style="border: 3px solid black; @if ($shouldBold) background-color:#d7d7d7; font-weight: bold; @endif">
                            {{ array_key_exists('implisit_qtq_revisi', $dt) && $dt['implisit_qtq_revisi'] ? round($dt['implisit_qtq_revisi'], 2) : '' }}
                        </td>
                        <td
                            style="border: 3px solid black; @if ($shouldBold) background-color:#d7d7d7; font-weight: bold; @endif">
                            {{ array_key_exists('implisit_ctc_rilis', $dt) && $dt['implisit_ctc_rilis'] ? round($dt['implisit_ctc_rilis'], 2) : '' }}
                        </td>
                        <td
                            style="border: 3px solid black; @if ($shouldBold) background-color:#d7d7d7; font-weight: bold; @endif">
                            {{ array_key_exists('implisit_ctc_revisi', $dt) && $dt['implisit_ctc_revisi'] ? round($dt['implisit_ctc_revisi'], 2) : '' }}
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                </tr>
            </tbody>
        @endif
    @endforeach
</table>
