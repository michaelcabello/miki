<?php

namespace App\Livewire\Admin\Products\Images;

use Livewire\Component;

use App\Models\ProductImage;
use App\Models\ProductTemplate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Intervention\Image\Laravel\Facades\Image;
use App\Models\Company;

class Index extends Component
{
    use WithPagination;
    use WithFileUploads;

    protected $paginationTheme = 'tailwind';

    public ProductTemplate $productTemplate;

    public string $search = '';
    public int $perPage = 10;
    public string $sortField = 'id';
    public string $sortDirection = 'asc';

    public ?int $selectedVariantId = null;

    /**
     * @var array<int, \Livewire\Features\SupportFileUploads\TemporaryUploadedFile>
     */
    public array $newImages = [];

    public function mount(ProductTemplate $productTemplate): void
    {
        $this->productTemplate = $productTemplate;

        $firstVariant = $this->productTemplate
            ->variants()
            ->orderBy('id')
            ->first();

        $this->selectedVariantId = $firstVariant?->id;
    }

    public function rules(): array
    {
        return [
            'newImages' => ['required', 'array', 'min:1', 'max:20'],
            'newImages.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    public function validationAttributes(): array
    {
        return [
            'newImages' => 'imágenes',
            'newImages.*' => 'imagen',
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function updatedNewImages(): void
    {
        $this->validateOnly('newImages');
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function selectVariant(int $variantId): void
    {
        $this->selectedVariantId = $variantId;
        $this->reset('newImages');
        $this->resetErrorBag();
    }

    public function getSelectedVariantProperty()
    {
        if (!$this->selectedVariantId) {
            return null;
        }

        return $this->productTemplate
            ->variants()
            ->with(['images' => function ($query) {
                $query->orderBy('sort_order')->orderBy('id');
            }])
            ->whereKey($this->selectedVariantId)
            ->first();
    }

    public function saveImages(): void
    {
        $this->validate();

        $variant = $this->selectedVariant;

        if (!$variant) {
            $this->addError('selectedVariantId', 'Debes seleccionar una variante.');
            return;
        }

        if (empty($this->newImages)) {
            $this->addError('newImages', 'Debes seleccionar al menos una imagen.');
            return;
        }

        DB::transaction(function () use ($variant) {
            $maxSortOrder = (int) $variant->images()->max('sort_order');
            $hasPrimary = $variant->images()->where('is_primary', true)->exists();

            foreach ($this->newImages as $index => $uploadedFile) {
                $uuid = (string) Str::uuid();
                $originalExtension = strtolower($uploadedFile->getClientOriginalExtension()) ?: 'jpg';

                $baseDirectory = "products/{$this->productTemplate->id}/variants/{$variant->id}/images";

                $originalPath = "{$baseDirectory}/original/{$uuid}.{$originalExtension}";
                $largePath = "{$baseDirectory}/large/{$uuid}.webp";
                $mediumPath = "{$baseDirectory}/medium/{$uuid}.webp";
                $thumbPath = "{$baseDirectory}/thumb/{$uuid}.webp";

                // Guardar original sin marca de agua
                $storedOriginal = Storage::disk('s3_public')->putFileAs(
                    "{$baseDirectory}/original",
                    $uploadedFile,
                    "{$uuid}.{$originalExtension}",
                    'public'
                );

                if (!$storedOriginal) {
                    throw new \RuntimeException('No se pudo guardar la imagen original en S3.');
                }

                $sourceImage = Image::read($uploadedFile->getRealPath());

                $width = $sourceImage->width();
                $height = $sourceImage->height();

                // LARGE
                $largeImage = Image::read($uploadedFile->getRealPath())
                    ->scaleDown(width: 1600);

                $this->applyWatermark($largeImage);

                $largeEncoded = $largeImage->encodeByExtension('webp', quality: 82);

                Storage::disk('s3_public')->put(
                    $largePath,
                    (string) $largeEncoded,
                    ['visibility' => 'public', 'ContentType' => 'image/webp']
                );

                // MEDIUM
                $mediumImage = Image::read($uploadedFile->getRealPath())
                    ->scaleDown(width: 900);

                $this->applyWatermark($mediumImage);

                $mediumEncoded = $mediumImage->encodeByExtension('webp', quality: 80);

                Storage::disk('s3_public')->put(
                    $mediumPath,
                    (string) $mediumEncoded,
                    ['visibility' => 'public', 'ContentType' => 'image/webp']
                );

                // THUMB
                $thumbImage = Image::read($uploadedFile->getRealPath())
                    ->coverDown(420, 420, 'center');

                $this->applyWatermark($thumbImage, 18, 12, 12);

                $thumbEncoded = $thumbImage->encodeByExtension('webp', quality: 78);

                Storage::disk('s3_public')->put(
                    $thumbPath,
                    (string) $thumbEncoded,
                    ['visibility' => 'public', 'ContentType' => 'image/webp']
                );

                ProductImage::create([
                    'product_variant_id' => $variant->id,
                    'disk' => 's3_public',
                    'path' => $mediumPath,
                    'original_path' => $originalPath,
                    'large_path' => $largePath,
                    'medium_path' => $mediumPath,
                    'thumb_path' => $thumbPath,
                    'original_name' => $uploadedFile->getClientOriginalName(),
                    'mime_type' => $uploadedFile->getMimeType(),
                    'size' => $uploadedFile->getSize(),
                    'width' => $width,
                    'height' => $height,
                    'is_primary' => !$hasPrimary && $index === 0,
                    'sort_order' => $maxSortOrder + $index + 1,
                ]);
            }
        });

        $this->reset('newImages');

        $this->dispatch(
            'notify',
            title: 'TICOM',
            text: 'Las imágenes fueron cargadas y marcadas correctamente.',
            icon: 'success'
        );
    }

    /**
     * Aplica una marca de agua a una imagen usando:
     * 1) logo de la empresa activa
     * 2) fallback local public/img/marcadeagua.png
     *
     * @param  mixed  $image
     */
    protected function applyWatermark($image, int $widthPercent = 22, int $opacity = 20, int $offset = 20): void
    {
        $watermarkBinary = $this->getWatermarkBinary();

        if (!$watermarkBinary) {
            return;
        }

        $watermark = Image::read($watermarkBinary);

        $targetWidth = max(80, (int) round($image->width() * ($widthPercent / 100)));

        $watermark->scaleDown(width: $targetWidth);

        $image->place(
            $watermark,
            'bottom-right',
            $offset,
            $offset,
            $opacity
        );
    }

    /**
     * Obtiene el binario de la marca de agua.
     * Prioridad:
     * 1) companies.logo de la empresa activa
     * 2) public/img/marcadeagua.png
     */
    protected function getWatermarkBinary(): ?string
    {
        $company = Company::query()
            ->where('state', 1)
            ->orderBy('id')
            ->first();

        if ($company && !empty($company->logo)) {
            $logo = trim($company->logo);

            // Si viene URL completa
            if (filter_var($logo, FILTER_VALIDATE_URL)) {
                try {
                    $content = @file_get_contents($logo);
                    if ($content !== false) {
                        return $content;
                    }
                } catch (\Throwable $e) {
                    // seguir al fallback
                }
            }

            // Si viene path guardado en S3
            try {
                if (Storage::disk('s3_public')->exists($logo)) {
                    return Storage::disk('s3_public')->get($logo);
                }
            } catch (\Throwable $e) {
                // seguir probando
            }

            // Si viene path relativo a /public
            $publicLogoPath = public_path(ltrim($logo, '/'));
            if (is_file($publicLogoPath)) {
                $content = @file_get_contents($publicLogoPath);
                if ($content !== false) {
                    return $content;
                }
            }

            // Si viene path absoluto
            if (is_file($logo)) {
                $content = @file_get_contents($logo);
                if ($content !== false) {
                    return $content;
                }
            }
        }

        // Fallback local
        $fallbackPath = public_path('img/marcadeagua.png');

        if (is_file($fallbackPath)) {
            $content = @file_get_contents($fallbackPath);
            if ($content !== false) {
                return $content;
            }
        }

        return null;
    }

    public function deleteImage(int $imageId): void
    {
        $image = ProductImage::query()
            ->whereHas('variant', fn($q) => $q->where('product_template_id', $this->productTemplate->id))
            ->findOrFail($imageId);

        $paths = array_filter([
            $image->original_path,
            $image->large_path,
            $image->medium_path,
            $image->thumb_path,
            $image->path,
        ]);

        foreach (array_unique($paths) as $path) {
            if (Storage::disk($image->disk)->exists($path)) {
                Storage::disk($image->disk)->delete($path);
            }
        }

        $wasPrimary = $image->is_primary;
        $variantId = $image->product_variant_id;

        $image->delete();

        if ($wasPrimary) {
            $nextImage = ProductImage::where('product_variant_id', $variantId)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->first();

            if ($nextImage) {
                $nextImage->update(['is_primary' => true]);
            }
        }

        $this->dispatch(
            'notify',
            title: 'TICOM',
            text: 'La imagen fue eliminada correctamente.',
            icon: 'success'
        );
    }

    public function setPrimaryImage(int $imageId): void
    {
        $variant = $this->selectedVariant;

        if (!$variant) {
            return;
        }

        ProductImage::where('product_variant_id', $variant->id)
            ->update(['is_primary' => false]);

        ProductImage::where('product_variant_id', $variant->id)
            ->whereKey($imageId)
            ->update(['is_primary' => true]);

        $this->dispatch(
            'notify',
            title: 'TICOM',
            text: 'La imagen principal fue actualizada.',
            icon: 'success'
        );
    }

    public function updateImageOrder(array $orderedIds): void
    {
        $variant = $this->selectedVariant;

        if (!$variant) {
            return;
        }

        foreach ($orderedIds as $index => $imageId) {
            ProductImage::where('product_variant_id', $variant->id)
                ->whereKey($imageId)
                ->update(['sort_order' => $index + 1]);
        }

        $this->dispatch(
            'notify',
            title: 'TICOM',
            text: 'El orden de las imágenes fue actualizado.',
            icon: 'success'
        );
    }

    public function render()
    {
        $variants = $this->productTemplate
            ->variants()
            ->when($this->search, function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('sku', 'like', '%' . $this->search . '%');
                });
            })
            ->withCount('images')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        if (!$this->selectedVariantId && $variants->count() > 0) {
            $this->selectedVariantId = $variants->first()->id;
        }

        return view('livewire.admin.products.images.index', [
            'variants' => $variants,
            'selectedVariant' => $this->selectedVariant,
        ]);
    }
}
