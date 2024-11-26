<table>
    <thead>
        <tr>
            <th></th><th colspan="3"><b>Fenomena PDRB</b></th>
        </tr>
        <tr>
            <th></th><th><b>Kabupaten/Kota</b></th><th><b>{{ $wilayah }}</b></th><th></th>
        </tr>
        <tr>
            <th></th><th><b>Tahun</b></th><th><b>{{ $tahun }}</b></th><th></th>
        </tr>
        <tr>
            <th></th><th><b>Triwulan</b></th><th><b>{{ $triwulan }}</b></th><th></th>
        </tr>

        <tr>
            <th width="3">No</th>
            <th width="50">Komponen/Subkomponen</th>
            <th width="15">Pertumbuhan</th>
            <th width="150">Fenomena</th>
        </tr>
    </thead>

    <tbody>
        @php 
            $null_parent = array_filter($komponen->toArray(), function ($x) {
                return $x['parent_id']==null;
            });
            $no = 0;
        @endphp

        @foreach($null_parent as $data)
            <tr>
                @php 
                    $no++;
                @endphp
                <td rowspan="3">{{ $no }}</td>
                <td rowspan="3">{{ $data['no_komponen'] }} {{ $data['nama_komponen'] }}</td>
              
                <td>q-to-q</td>
                <td>
                    @if($datas['q-to-q'][$triwulan-1]!=null)
                        {{ $datas['q-to-q'][$triwulan-1]->{'fenomena_c_'.str_replace('.', '', $data['no_komponen'])} }}
                    @endif
                </td>
            </tr>
            
            <tr>
                <td>y-o-y</td>
                <td>
                    @if($datas['y-o-y'][$triwulan-1]!=null)
                        {{ $datas['y-o-y'][$triwulan-1]->{'fenomena_c_'.str_replace('.', '', $data['no_komponen'])} }}
                    @endif
                </td>
            </tr>
            
            <tr>
                <td>c-to-c</td>
                <td>
                    @if($datas['c-to-c'][$triwulan-1]!=null)
                        {{ $datas['c-to-c'][$triwulan-1]->{'fenomena_c_'.str_replace('.', '', $data['no_komponen'])} }}
                    @endif
                </td>
            </tr>

            @php 
                $non_null_parent = array_filter($komponen->toArray(), function ($x) use ($data) {
                    return $x['parent_id']==$data['no_komponen'];
                });
            @endphp
        
            @foreach($non_null_parent as $data2)
                <tr>
                    @php 
                        $no++;
                    @endphp
                    <td rowspan="3">{{ $no }}</td>
                    <td rowspan="3">{{ $data2['no_komponen'] }} {{ $data2['nama_komponen'] }}</td>

                    <td>q-to-q</td>
                    <td>
                        @if($datas['q-to-q'][$triwulan-1]!=null)
                            {{ $datas['q-to-q'][$triwulan-1]->{'fenomena_c_'.str_replace('.', '', $data2['no_komponen'])} }}
                        @endif
                    </td>
                </tr>
                
                <tr>
                    <td>y-o-y</td>
                    <td>
                        @if($datas['y-o-y'][$triwulan-1]!=null)
                            {{ $datas['y-o-y'][$triwulan-1]->{'fenomena_c_'.str_replace('.', '', $data2['no_komponen'])} }}
                        @endif
                    </td>
                </tr>
                
                <tr>
                    <td>c-to-c</td>
                    <td>
                        @if($datas['c-to-c'][$triwulan-1]!=null)
                            {{ $datas['c-to-c'][$triwulan-1]->{'fenomena_c_'.str_replace('.', '', $data2['no_komponen'])} }}
                        @endif
                    </td>
                </tr>
            @endforeach
        @endforeach

        <tr>
            @php 
                $no++;
            @endphp
            <td rowspan="3">{{ $no }}</td>
            <td rowspan="3">PDRB</td>

            <td>q-to-q</td>
            <td>
                @if($datas['q-to-q'][$triwulan-1]!=null)
                    {{ $datas['q-to-q'][$triwulan-1]->{'fenomena_c_pdrb'} }}
                @endif
            </td>
        </tr>
        
        <tr>
            <td>y-o-y</td>
            <td>
                @if($datas['y-o-y'][$triwulan-1]!=null)
                    {{ $datas['y-o-y'][$triwulan-1]->{'fenomena_c_pdrb'} }}
                @endif
            </td>
        </tr>
        
        <tr>
            <td>c-to-c</td>
            <td>
                @if($datas['c-to-c'][$triwulan-1]!=null)
                    {{ $datas['c-to-c'][$triwulan-1]->{'fenomena_c_pdrb'} }}
                @endif
            </td>
        </tr>
    </tbody>
</table>