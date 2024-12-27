<table>
    <thead>
        <tr><th colspan="13"><b>Tabel PDRB ADHB</b></th></tr>
        
        <tr><th colspan="13"></th></tr>

        <tr>
            <th width="50"><b>Komponen</b></th>

            @if($triwulan==4)
                @for($i=($tahun-2);$i<=($tahun-1);++$i)
                    @for($j=1;$j<=4;++$j)
                        <th><b>{{ $i }}Q{{ $j }}</b></th>
                    @endfor
                @endfor
            @endif

            <th><b>{{ $tahun }}Q1</b></th>
            @if($triwulan>=2)
                <th><b>{{ $tahun }}Q2</b></th>
            @endif
            @if($triwulan>=3)
                <th><b>{{ $tahun }}Q3</b></th>
            @endif
            @if($triwulan==4)
                <th><b>{{ $tahun }}Q4</b></th>
            @endif
        </tr>
    </thead>

    @php 
        $null_parent = array_filter($komponen->toArray(), function ($x) {
            return $x['parent_id']==null;
        });
    @endphp

    @if($triwulan==4)
        <tbody>
            @foreach($null_parent as $data)
                <tr>
                    <td>{{ $data['no_komponen'] }} {{ $data['nama_komponen'] }}</td>
                    
                    @for($i=($tahun-2);$i<=$tahun;++$i)
                        @for($j=1;$j<=4;++$j)
                            <td>
                                @if($datas['adhb'][(($i - ($tahun-2))*4)+($j-1)]!=null)
                                    {{ $datas['adhb'][(($i - ($tahun-2))*4)+($j-1)]->{'c_'.str_replace('.', '', $data['no_komponen'])} }}
                                @endif
                            </td>
                        @endfor
                    @endfor
                    <!-- -->
                </tr>

                @php 
                    $non_null_parent = array_filter($komponen->toArray(), function ($x) use ($data) {
                        return $x['parent_id']==$data['no_komponen'];
                    });
                @endphp
            
                @foreach($non_null_parent as $data2)
                    <tr>
                        <td>{{ $data2['no_komponen'] }} {{ $data2['nama_komponen'] }}</td>
                        
                        @for($i=($tahun-2);$i<=$tahun;++$i)
                            @for($j=1;$j<=4;++$j)
                                <td>
                                    @if($datas['adhb'][(($i - ($tahun-2))*4)+($j-1)]!=null)
                                        {{ $datas['adhb'][(($i - ($tahun-2))*4)+($j-1)]->{'c_'.str_replace('.', '', $data2['no_komponen'])} }}
                                    @endif
                                </td>
                            @endfor
                        @endfor
                        <!-- -->
                    </tr>
                    <!-- -->
                @endforeach
            @endforeach
                
            <tr>
                <td><b>PDRB</b></td>

                @for($i=($tahun-2);$i<=$tahun;++$i)
                    @for($j=1;$j<=4;++$j)
                        <td>
                            @if($datas['adhb'][(($i - ($tahun-2))*4)+($j-1)]!=null)
                                {{ $datas['adhb'][(($i - ($tahun-2))*4)+($j-1)]->{'c_pdrb'} }}
                            @endif
                        </td>
                    @endfor
                @endfor
                <!-- -->
            </tr>
        </tbody>
    @else
        <tbody>
            @foreach($null_parent as $data)
                <tr>
                    <td>{{ $data['no_komponen'] }} {{ $data['nama_komponen'] }}</td>
                    
                    <td>
                        @if($datas['adhb'][0]!=null)
                            {{ $datas['adhb'][0]->{'c_'.str_replace('.', '', $data['no_komponen'])} }}
                        @endif
                    </td>
                    
                    @if($triwulan>=2)
                        <td>
                            @if($datas['adhb'][1]!=null)
                                {{ $datas['adhb'][1]->{'c_'.str_replace('.', '', $data['no_komponen'])} }}
                            @endif
                        </td>
                    @endif
                    
                    @if($triwulan>=3)
                        <td>
                            @if($datas['adhb'][2]!=null)
                                {{ $datas['adhb'][2]->{'c_'.str_replace('.', '', $data['no_komponen'])} }}
                            @endif
                        </td>
                    @endif
                </tr>

                @php 
                    $non_null_parent = array_filter($komponen->toArray(), function ($x) use ($data) {
                        return $x['parent_id']==$data['no_komponen'];
                    });
                @endphp
            
                @foreach($non_null_parent as $data2)
                    <tr>
                        <td>{{ $data2['no_komponen'] }} {{ $data2['nama_komponen'] }}</td>

                        <td>
                            @if($datas['adhb'][0]!=null)
                                {{ $datas['adhb'][0]->{'c_'.str_replace('.', '', $data2['no_komponen'])} }}
                            @endif
                        </td>
                        
                        @if($triwulan>=2)
                            <td>
                                @if($datas['adhb'][1]!=null)
                                    {{ $datas['adhb'][1]->{'c_'.str_replace('.', '', $data2['no_komponen'])} }}
                                @endif
                            </td>
                        @endif
                        
                        @if($triwulan>=3)
                            <td>
                                @if($datas['adhb'][2]!=null)
                                    {{ $datas['adhb'][2]->{'c_'.str_replace('.', '', $data2['no_komponen'])} }}
                                @endif
                            </td>
                        @endif
                    </tr>
                @endforeach
            @endforeach
                
            <tr>
                <td><b>PDRB</b></td>

                <td>
                    @if($datas['adhb'][0]!=null)
                        {{ $datas['adhb'][0]->{'c_pdrb'} }}
                    @endif
                </td>
                
                @if($triwulan>=2)
                    <td>
                        @if($datas['adhb'][1]!=null)
                            {{ $datas['adhb'][1]->{'c_pdrb'} }}
                        @endif
                    </td>
                @endif
                
                @if($triwulan>=3)
                    <td>
                        @if($datas['adhb'][2]!=null)
                            {{ $datas['adhb'][2]->{'c_pdrb'} }}
                        @endif
                    </td>
                @endif
            </tr>
        </tbody>
    @endif
</table>