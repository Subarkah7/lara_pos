<?php

namespace App\Http\Livewire;

use App\Models\Product as ModelsProduct;
use App\Models\ProductTransaction;
use App\Models\Transaction;
use Carbon\Carbon;
use Darryldecode\Cart\CartCondition;
use Livewire\Component;
use Livewire\WithPagination;
use DB;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Support\Facades\Auth;

class Cart extends Component
{
    public $tax = '0%';
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search;
    public $payment = 0;

    public function updatingSearch()
    {
        $this->resetPage();
    }


    public function render()
    {

        $products = ModelsProduct::where('name', 'like', '%'.$this->search.'%')
            ->orderBy('id', 'desc')->paginate(8);

        $condition = new CartCondition([
            'name'  => 'pajak',
            'type'  => 'tax',
            'target'=> 'total',
            'value' => $this->tax,
            'order' => 1
        ]);

        \Cart::session(Auth()->id())->condition($condition);
        $items = \Cart::session(Auth()->id())->getContent()->sortBy(function ($cart) {
            return $cart->attributes->get('added_at');
        });

        if(\Cart::isEmpty()) {
            $cartData = [];
        } else {
            foreach($items as $item) {
                $cart[] = [
                    'rowId'         => $item->id,
                    'name'          => $item->name,
                    'qty'           => $item->quantity,
                    'pricesingle'   => $item->price,
                    'price'         => $item->getPriceSum()
                ];
            }

            $cartData = collect($cart);
        }

        $sub_total = \Cart::session(Auth()->id())->getSubTotal();
        $total = \Cart::session(Auth()->id())->getTotal();

        $newCondition = \Cart::session(Auth()->id())->getCondition('pajak');
        $pajak = $newCondition->getCalculatedValue($sub_total);

        $summary = [
            'sub_total' => $sub_total,
            'pajak'     => $pajak,
            'total'     => $total
        ];

        return view('livewire.cart', [
            'products'  => $products,
            'cart'      => $cartData,
            'summary'   => $summary
        ]);
    }

    public function addItem($id)
    {
        $rowId = 'Cart'.$id;
        $cart  = \Cart::session(Auth()->id())->getContent();
        $cekItemId = $cart->whereIn('id', $rowId);

        $product = ModelsProduct::findOrFail($id);

        if($cekItemId->isNotEmpty()) {
            \Cart::session(Auth()->id())->update($rowId, [
                'quantity'  => [
                    'relative'  => true,
                    'value'     => 1
                ]
                ]);
        } else {

            if($product->qty === 0) {
                return session()->flash('error', 'Jumlah item '.$product->name.' kurang');
            
            } else {
                \Cart::session(Auth()->id())->add([
                    'id'        => 'Cart'.$product->id,
                    'name'      =>  $product->name,
                    'price'     =>  $product->price,
                    'quantity'  =>  1,
                    'attributes'=> [
                        'added_at' => Carbon::now()
                    ]
                ]);
            }
        }


    }


    public function enableTax()
    {
        $this->tax = '+10%';
    }

    public function disableTax()
    {
        $this->tax = '0%';
    }


    public function increaseItem($rowId)
    {   
        $product_id = substr($rowId, 4);        
        $product = ModelsProduct::find($product_id);

        $cart = \Cart::session(Auth()->id())->getContent();
        $checkItem = $cart->whereIn('id', $rowId);

        if($product->qty == $checkItem[$rowId]->quantity) {
            session()->flash('error', 'Jumlah item '.$product->name.' kurang');
        } else {

            if($product->qty === 0) {
                return session()->flash('error', 'Jumlah item '.$product->name.' kurang');
            } else {
                \Cart::session(Auth()->id())->update($rowId, [
                    'quantity'  => [
                        'relative'  => true,
                        'value'     => 1
                    ]
                    ]);
            } 

           
        }

       
        
    }


    public function decreaseItem($rowId)
    {   
        $cart = \Cart::session(Auth()->id())->getContent();
        $checkItem = $cart->whereIn('id', $rowId);

        if($checkItem[$rowId]->quantity == 1) {
            $this->removeItem($rowId);
        } else {
            \Cart::session(Auth()->id())->update($rowId, [
                'quantity'  => [
                    'relative'  => true,
                    'value'     => -1
                ]
                ]); 
        }
    }

    public function removeItem($rowId) {
        \Cart::session(Auth()->id())->remove($rowId);
    }

    public function handleSubmit() {
        $cartTotal = \Cart::session(Auth()->id())->getTotal();
        $pay = $this->payment;
        $casBack = (int) $pay - (int) $cartTotal;

        if($casBack >= 0) {
            DB::beginTransaction();

            try {
                
                $allCart = \Cart::session(Auth()->id())->getContent();

                $filterCart = $allCart->map(function($item) {
                    return [
                        'id'        => substr($item->id, 4),
                        'quantity'  => $item->quantity
                    ];
                });

                foreach($filterCart as $cart) {
                    $product = ModelsProduct::find($cart['id']);

                    if($product->qty === 0) {
                        return session()->flash('error', 'Jumlah item '.$product->name.' kurang');
                    } 

                    $product->decrement('qty', $cart['quantity']);
                }

                $id = IdGenerator::generate([
                    'table'     => 'transactions',
                    'length'    => 10,
                    'prefix'     => 'INV-',
                    'field'     => 'invoice_number'
                ]);

                Transaction::create([
                    'invoice_number'=> $id,
                    'user_id'       => Auth()->id(),
                    'pay'           => $pay,
                    'total'         => $cartTotal
                ]);


                foreach($filterCart as $cart) {
                    ProductTransaction::create([
                        'product_id'    => $cart['id'],
                        'invoice_number'=> $id,
                        'qty'           => $cart['quantity']
                    ]);
                }

                \Cart::session(Auth()->id())->clear();
                $this->payment = 0;

                DB::commit();
            } catch (\Throwable $th) {
                DB::rollback();
                return session()->flash('error', $th->getMessage());

            }
            
        }

    }

}
