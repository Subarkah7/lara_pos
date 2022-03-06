<div>
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white">
                    <div class="row justify-content-between">
                        <div class="col-md-4">
                            <h5 class="font-weight-bold">Product List</h5>
                        </div>
                        <div class="col-md-6">
                            <input wire:model="search" type="text" class="form-control" placeholder="Search Products...">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    
                    
                    
                    <div class="row">
                        @forelse($products as $value)
                            <div class="col-md-3 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <img src="{{ asset('storage/images/'.$value->image)  }}" alt="" style="object-fit: contain; width:100%;height:140px">
                                    </div>
                                    <div class="card-footer text-center">
                                        <h6>{{ $value->name }}</h6>
                                        <span>{{ rupiah($value->price) }}</span>
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
                <div class="card-header bg-white">
                    <h5 class="font-weight-bold">Cart</h5>
                </div>
                <div class="card-body">
                    @if (session()->has('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                    @endif
                    <table class="table table-sm table-bordered table-hovered">
                        <thead class="bg-white">
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Qty</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($cart as $key => $value)
                            <tr>
                                <td style="text-align: center">{{ ++$key }}
                                <br>
                                <button wire:click="removeItem('{{$value['rowId']}}')" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>

                                </td>
                                <td>{{ $value['name'] }} </td>
                                <td style="text-align: center">
                                    <div class="row justify-content-center"><button wire:click="decreaseItem('{{$value['rowId']}}')" class="btn btn-warning btn-sm mb-2"> - </button>  
                                        {{ $value['qty'] }} 
                                    <button wire:click="increaseItem('{{$value['rowId']}}')" class="btn btn-success btn-sm mt-2"> + </button></div>
                                </td>
                                <td>{{ rupiah($value['price']) }}</td>
                                
                            </tr>
                        @empty
                        <tr>
                            <td colspan="4"><h6 class="text-center">Empty Cart</h6></td>
                        </tr>
                        @endforelse
                        </tbody>

                    </table>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    
                    <h5 class="font-weight-bold">Cart Summary</h5>
                    <table class="table">
                        <tr>
                            <td><h5>Sub Total</h5></td>
                            <td><h5>:{{ rupiah($summary['sub_total']) }}</h5></td>
                        </tr>
                        <tr>
                            <td width="40%"><h5>Tax</h5></td>
                            <td><h5>: {{ rupiah($summary['pajak']) }}</h5></td>
                        </tr>
                        <tr>
                            <td> <h5>Total</h5></td>
                            <td><h5>: {{ rupiah($summary['total']) }}</h5></td>
                        </tr>

                    </table>
                    <div>
                        <div class="row">
                            <div class="col-md-6">
                                <button wire:click="enableTax" class="btn btn-info btn-sm w-100">Add Tax</button>
                            </div>
                            <div class="col-md-6">
                                 <button wire:click="disableTax" class="btn btn-danger btn-sm w-100">Remove Tax</button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-4 mb-2">
                        <input type="number" wire:model="payment" class="form-control" id="payment" placeholder="Input customer Payment">
                        <input type="hidden" id="total" value="{{ $summary['total']}}">
                    </div>
                    <form wire:submit.prevent="handleSubmit">
                        <div>
                            <label >Payment</label>
                            <h1 id="paymentText" wire:ignore>Rp. 0</h1>
                        </div>

                        <div>
                            <label> Kembalian</label>
                            <h1 id="kembalianText" wire:ignore>Rp. 0</h1>
                        </div>

                        <div>
                            <button wire:ignore disabled class="btn btn-success btn-block w-100 mt-4" id="saveButton">Save Transaction</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>



@push('scripts-custom')
<script>
    payment.oninput = () => {
        const paymentAmount = document.getElementById("payment").value
        const totalAmount = document.getElementById("total").value
        const kembalian = paymentAmount - totalAmount
        document.getElementById("kembalianText").innerHTML = `Rp ${rupiah(kembalian)} ,00`
        document.getElementById("paymentText").innerHTML = `Rp ${rupiah(paymentAmount)} ,00`
        const saveButton =  document.getElementById("saveButton")
        if(kembalian < 0){
            saveButton.disabled = true
        }else{
            saveButton.disabled = false
        }
    }


    const rupiah = (angka) => {
        const numberString = angka.toString()
        const split = numberString.split(',')
        const sisa = split[0].length % 3
        let rupiah = split[0].substr(0, sisa)
        const ribuan = split[0].substr(sisa).match(/\d{1,3}/gi)
        if(ribuan){
            const separator = sisa ? '.' : ''
            rupiah += separator + ribuan.join('.')
        }
        return split[1] != undefined ? rupiah + ',' + split[1] : rupiah
    }

    saveButton.onclick = () => {
        document.getElementById("kembalianText").innerHTML = `Rp 0`
        document.getElementById("paymentText").innerHTML = `Rp 0`
    }
</script>
    
@endpush
