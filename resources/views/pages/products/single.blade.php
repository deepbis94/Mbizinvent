@forelse ($products as $p)
<tr>
    <td>
        {{ $p->id }}
    </td>
    <td>{{ $p->hsn_code }}</td>
    <td>
        {{ $p->product_description }}
    </td>
    <td>
        {{ $p->rate }}
    </td>
    <td>
        {{ $p->gst_percentage }}
    </td>    
    <td>
        {{ \Carbon\Carbon::parse($p->created_at)->timezone('Asia/Kolkata')->format('d-m-Y H:i:s') }}        
    </td>

    <td>
        <button class="btn btn-primary" data-url="{{route('products.show', ['product' => $p->id])}}" data-storeurl="{{route('products.update', ['product' => $p->id])}}" onclick="editProduct(event)">Edit</button>
        <button class="btn btn-danger" data-url="{{ route('products.destroy',['product'=>$p->id]) }}" data-id="{{ $p->id }}" onclick="deleteProduct(event)">Delete</button>
    </td>
  </tr>
    
@empty
    <tr>
        <td colspan="6">No records found    </td>
    </tr>
@endforelse
