@forelse ($customers as $c)
<tr>
    <td>
        {{ $c->id }}
    </td>
    <td>{{ $c->customer_name }}</td>
    <td>
        {{ $c->gstin_number }}
    </td>
    <td>
        {{ $c->phone }}
    </td>
    <td>
        {{ \Carbon\Carbon::parse($c->created_at)->timezone('Asia/Kolkata')->format('d-m-Y H:i:s') }}        
    </td>

    <td>
        <button class="btn btn-primary" data-url="{{route('customers.show', ['customer' => $c->id])}}" data-storeurl="{{route('customers.update', ['customer' => $c->id])}}" onclick="editCustomer(event)">Edit</button>
        <button class="btn btn-danger" data-id="{{ $c->id }}" data-url="{{ route('customers.destroy',['customer'=>$c->id]) }}" onclick="deleteCustomer(event)">Delete</button>
    </td>
  </tr>
    
@empty
    <tr>
        <td colspan="6">No records found    </td>
    </tr>
@endforelse
