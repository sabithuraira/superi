<table>
    <thead>
        <tr>
            <th><b>{{ $judul }}</b></th>
        </tr>

        <tr>
            <th></th>
        </tr>

        <tr>
            @foreach ($pdrb[0] as $key => $val)
            <th style="border: 1px solid black;"><b>{{ $key }}</b></th>
            @endforeach
        </tr>
    </thead>

    <tbody>
        @foreach ($pdrb as $pdrb_item)
        <tr>
            @if (str_contains($pdrb_item->Komponen, 'BOLD'))
                @foreach ($pdrb_item as $item)
                    @if ($item == 'CENTER<div class="text-success">▲</div>')
                    <td style="border: 1px solid black;text-align: center;color: #28a745;background-color: #f2f2f2;">▲</td>
                    @elseif ($item == 'CENTER<div class="text-danger">▼</div>')
                    <td style="border: 1px solid black;text-align: center;color: #dc3545;background-color: #f2f2f2;">▼</td>
                    @elseif ($item == 'CENTER<div class="text-warning">═</div>')
                    <td style="border: 1px solid black;text-align: center;color: #ffc107;background-color: #f2f2f2;">═</td>
                    @elseif (str_contains($item, 'WARNING'))
                    <td style="border: 1px solid black;background-color: #ffc107;"><b>{{ str_replace('WARNING', '', $item) }}</b></td>
                    @else
                    <td style="border: 1px solid black;background-color: #f2f2f2;"><b>{{ str_replace('BOLD', '', $item) }}</b></td>
                    @endif
                @endforeach
            @else
                @foreach ($pdrb_item as $item)
                    @if ($item == 'CENTER<div class="text-success">▲</div>')
                    <td style="border: 1px solid black;text-align: center;color: #28a745;">▲</td>
                    @elseif ($item == 'CENTER<div class="text-danger">▼</div>')
                    <td style="border: 1px solid black;text-align: center;color: #dc3545;">▼</td>
                    @elseif ($item == 'CENTER<div class="text-warning">═</div>')
                    <td style="border: 1px solid black;text-align: center;color: #ffc107;">═</td>
                    @elseif (str_contains($item, 'WARNING'))
                    <td style="border: 1px solid black;background-color: #ffc107;">{{ str_replace('WARNING', '', $item) }}</td>
                    @else
                    <td style="border: 1px solid black;">{{ $item }}</td>
                    @endif
                @endforeach
            @endif
        </tr>
        @endforeach
    </tbody>
</table>
