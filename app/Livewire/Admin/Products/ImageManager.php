<?php

namespace App\Livewire\Admin\Products;

use Livewire\Component;

use App\Models\ProductVariant;
use App\Models\ProductImage;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Storage;



class ImageManager extends Component
{
    use WithFileUploads;

    public $isOpen = false;
    public $variant;
    public $newImages = []; // Para la subida temporal
    public $allVariants = []; // Nueva propiedad


    #[On('openImageManager')]
    public function openModal($variantId)
    {
        $this->variant = ProductVariant::with(['template.variants', 'images'])->find($variantId);

        if ($this->variant) {
            // Cargamos todas las variantes de ese producto para el selector
            $this->allVariants = $this->variant->template->variants;
            $this->isOpen = true;
        }
    }

    // Método para cambiar de variante sin cerrar el modal
    public function switchVariant($id)
    {
        $this->variant = ProductVariant::with(['template.variants', 'images'])->find($id);
        $this->reset('newImages'); // Limpiar subidas pendientes al cambiar
    }




    public function closeModal()
    {
        $this->isOpen = false;
        $this->reset(['newImages', 'variant']);
    }

    /**
     * Guarda las imágenes y las asocia a la variante
     */
    public function saveImages()
    {
        $this->validate([
            'newImages.*' => 'image|max:2048', // 2MB Max
        ]);

        foreach ($this->newImages as $image) {
            $path = $image->store('products', 'public');

            $this->variant->images()->create([
                'path' => $path,
                'is_main' => $this->variant->images()->count() === 0, // Primera es main
                'sort_order' => $this->variant->images()->max('sort_order') + 1,
            ]);
        }

        $this->reset('newImages');
        $this->variant->load('images'); // Refrescar galería
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Imágenes subidas.']);
    }

    public function setMain($imageId)
    {
        $this->variant->images()->update(['is_main' => false]);
        ProductImage::find($imageId)->update(['is_main' => true]);
        $this->variant->load('images');
    }

    public function deleteImage($imageId)
    {
        $image = ProductImage::find($imageId);
        Storage::disk('public')->delete($image->path);
        $image->delete();
        $this->variant->load('images');
    }

    public function render()
    {
        return view('livewire.admin.products.image-manager');
    }
}
