<div>
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h2 class="font-weight-bold mb-3">Product List</h2>
                    <table class="table table-bordered table-hovered table-striped">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Name</th>
                                <th>Image</th>
                                <th>Qty</th>
                                <th>Description</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $key => $value)                                
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td>{{ $value->name }}</td>
                                <td width="20%"><img src="{{ asset('storage/images/'.$value->image)  }}" alt="" class="img-fluid"></td>
                                <td>{{ $value->qty }}</td>
                                <td>{{ $value->description }}</td>
                                <td>{{ $value->price }}</td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">            
                <div class="card-body">
                <h2 class="font-weight-bold mb-3">Create Product</h2>
                <form wire:submit.prevent="store">
                    <div class="form-group mb-3">
                        <label for="name">Product Name</label>
                        <input type="text" wire:model="name" class="form-control">
                        @error('name') <small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="form-group mb-3">
                        <label for="name">Product Image</label>
                        <div class="custom-file">
                            <input type="file" wire:model="image" class="custom-file-input" id="customFile">
                            <label class="custom-file-label" for="customFile">Choose file</label>
                            @error('image') <small class="text-danger">{{ $message }}</small>@enderror
                            
                        </div>
                       
                        @if($image)
                            <label class="mt-2">Image Preview</label>
                            <img src="{{$image->temporaryUrl() }}" class="img-fluid" alt="Preview Image">
                        @endif
                    </div>
                    <div class="form-group mb-3">
                        <label for="name">Product Price</label>
                        <input type="text" wire:model="price" class="form-control">
                        @error('price') <small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="form-group">
                        <label for="name">Product Qty</label>
                        <input type="number" wire:model="qty" class="form-control">
                        @error('qty') <small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="form-group mb-3">
                        <label for="name">Product Description</label>
                        <textarea  wire:model="desc" class="form-control"></textarea>
                        @error('desc') <small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary w-100" type="submit">Submit</button>
                    </div>

                </form>
                </div>
            </div>
        </div>
    </div>
</div>
