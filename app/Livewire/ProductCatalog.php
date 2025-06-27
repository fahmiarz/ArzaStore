<?php
declare(strict_types=1);

namespace App\Livewire;

use App\Data\ProductCollectionData;
use App\Data\ProductData;
use App\Models\Product;
use App\Models\Tag;
use Livewire\Component;
use Livewire\WithPagination;

class ProductCatalog extends Component
{
    //pagination
    use WithPagination;

    //query string url
    public $queryString = [
        "select_collections" => ["except" => []],
        "search" => ["except" => []],
        "sort_by" => ["except" => "newest"],
    ];

    //selected collections
    public array $select_collections = [];

    //search
    public string $search = "";

    //sort by
    public string $sort_by = "newest"; // latest, price_asc, price_desc

    public function mount()
    {
        $this->validate();
    }

    //validation rules
    protected function rules()
    {
        return [
            "select_collections" => "array",
            "select_collections.*" => "integer|exists:tags,id",
            "search" => "nullable|string|max:50|min:3",
            "sort_by" => "in:newest,latest,price_asc,price_desc",
        ];
    }

    //apply filters
    public function applyFilters()
    {
        $this->validate();
        $this->resetPage();
        // dd($this->selected_collections, $this->search, $this->sort_by);
    }

    public function resetFilters()
    {
        $this->select_collections = [];
        $this->search = "";
        $this->sort_by = "newest";

        $this->resetErrorBag();
        $this->resetPage();
    }

    public function render()
    {
        $collections = ProductCollectionData::collect([]);
        $products = ProductData::collect([]);
        //early return
        if ($this->getErrorBag()->isNotEmpty()) {
            return view(
                "livewire.product-catalog",
                compact("collections", "products")
            );
        }

        $collection_result = Tag::query()
            ->withType("collection")
            ->withCount("products")
            ->get();
        // $result = Product::paginate(1); //ORM //Data base query

        $query = Product::query();
        //search
        if ($this->search) {
            $query->where("name", "LIKE", "%{$this->search}%");
        }

        //selections
        if (!empty($this->select_collections)) {
            $query->whereHas("tags", function ($query) {
                $query->whereIn("id", $this->select_collections);
            });
        }

        switch ($this->sort_by) {
            case "latest":
                $query->oldest();
                break;
            case "price_asc":
                $query->orderBy("price", "asc");
                break;
            case "price_desc":
                $query->orderBy("price", "desc");
            default:
                $query->latest();
                break;
        }

        $products = ProductData::collect($query->paginate(10));
        $collections = ProductCollectionData::collect($collection_result);
        return view(
            "livewire.product-catalog",
            compact("products", "collections")
        );
    }
}
