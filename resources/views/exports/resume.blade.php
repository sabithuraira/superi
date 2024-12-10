<table>
    <thead>
        <tr>
            <th><b>{{ $judul }}</b></th>
        </tr>

        <tr>
            <th></th>
        </tr>

        <tr>
            @foreach ($columns as $column)
                @if ($column == 'kd_kab')
                <th style="border: 1px solid black;"><b>Kabupaten/Kota</b></th>
                @else
                <th style="border: 1px solid black;"><b>{{ $column }}</b></th>
                @endif
            @endforeach
        </tr>
    </thead>

    <tbody>
        @foreach ($pdrb as $pdrb_item)
        <tr>
            @foreach ($pdrb_item as $item)
            <td style="border: 1px solid black;">{{ $item }}</td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>
