@forelse ($inventory as $i)
<tr>
    <td>
        {{ $i->product->id ?? '' }}
    </td>
    <td>
        {{ $i->product->hsn_code ?? '' }}
    </td>
    <td>{{ $i->product->product_description ?? '' }}</td>
    <td>
        {{ $i->available_stock ?? '' }}
    </td>
    <!-- <td>
        {{ $i->buying_price }}
    </td> -->
    <td>
        {{ \Carbon\Carbon::parse($i->created_at)->timezone('Asia/Kolkata')->format('d-m-Y H:i:s') }}        
    </td>

  </tr>
    
@empty
    <tr>
        <td colspan="6">No records found    </td>
    </tr>
@endforelse
