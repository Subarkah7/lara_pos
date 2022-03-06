<div>
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="row justify-content-between mb-3">
                        <div class="col-md-4">
                            <h2 class="font-weight-bold">Product List</h2>
                        </div>
                        <div class="col-md-6">
                            <input wire:model="search" type="text" class="form-control" placeholder="Search Products...">
                        </div>
                    </div>
                    
                    
                    <div class="row">
                        @forelse($products as $value)
                            <div class="col-md-3 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <img src="{{ asset('storage/images/'.$value->image)  }}" alt="" class="img-fluid">
                                    </div>
                                    <div class="card-footer">
                                        <h6 class="text-center">{{ $value->name }}</h6>
                                        <button wire:click="addItem({{$value->id}})" class="btn btn-primary btn-sm w-100">Add To Cart</button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center">
                                <h4>No Product Found !</h4>
                            </div>
                        @endforelse
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            {{ $products->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h2 class="font-weight-bold">Cart</h2>
                    @if (session()->has('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    <table class="table table-sm table-bordered table-hovered">
                        <thead class="bg-secondary text-light">
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($cart as $key => $value)
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td>{{ $value['name'] }} </td>
                                <td>
                                    <button wire:click="decreaseItem('{{$value['rowId']}}')" class="btn btn-warning btn-sm"> - </button>  
                                        {{ $value['qty'] }} 
                                    <button wire:click="increaseItem('{{$value['rowId']}}')" class="btn btn-success btn-sm"> + </button>
                                </td>
                                <td>{{ rupiah($value['price']) }}</td>
                                <td>
                                    <button wire:click="removeItem('{{$value['rowId']}}')" class="btn btn-danger btn-sm">X</button>
                                </td>
                            </tr>
                        @empty
                        <tr>
                            <td colspan="3"><h6 class="text-center">Empty Cart</h6></td>
                        </tr>
                        @endforelse
                        </tbody>

                    </table>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="font-weight-bold">Cart Summary</h4>
                    <h5>Sub Total: {{ rupiah($summary['sub_total']) }}</h5>
                    <h5>Tax: {{ rupiah($summary['pajak']) }}</h5>
                    <h5>Total: {{ rupiah($summary['total']) }}</h5>
                    <div>
                        <button wire:click="enableTax" class="btn btn-info btn-block w-100 mb-2">Add Tax</button>
                        <button wire:click="disableTax" class="btn btn-danger btn-block w-100 mb-4">Remove Tax</button>
                    </div>
                    <div>
                        <button class="btn btn-success btn-block w-100">Save Transaction</button>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
