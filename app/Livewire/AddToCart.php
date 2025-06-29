<?php

namespace App\Livewire;

use App\Contract\CartServiceInterface;
use App\Data\CartItemData;
use App\Data\ProductData;
use Livewire\Component;

class AddToCart extends Component
{
    public int $quantity;

    public string $sku;

    public float $price;

    public int $stock;

    public int $weight;

    public string $label = "Add To Cart ";

    public function mount(ProductData $product, CartServiceInterface $cart)
    {
        $this->sku = $product->sku;
        $this->price = $product->price;
        $this->stock = $product->stock;
        $this->weight = $product->weight;
        $this->quantity = $cart->getItemsBySku($product->sku)->quantity ?? 1;

        $this->validate();

        if ($cart->getItemsBySku($this->sku)) {
            $this->label = "Update Cart";
        }
    }

    public function rules()
    {
        return [
            "quantity" => [
                "required",
                "integer",
                "min:1",
                "max:{$this->stock}",
            ],
        ];
    }

    public function addToCart(CartServiceInterface $cart)
    {
        $this->validate();
        // dd($this->quantity);
        $cart->addOrUpdate(
            new CartItemData(
                sku: $this->sku,
                price: $this->price,
                quantity: $this->quantity,
                weight: $this->weight
            )
        );

        session()->flash("success", "Product added to cart successfully!");

        $this->dispatch("cart-updated");

        return redirect()->route("cart");
    }

    public function render()
    {
        return view("livewire.add-to-cart");
    }
}
