<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('images')->orderBy('sort_order')->paginate(15);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0'],
            'is_available' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'gallery.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        // Handle primary image upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            // TODO(security): Consider malware scanning for uploaded images
            $extension = $file->getClientOriginalExtension();
            $allowedExtensions = ['jpeg', 'jpg', 'png', 'webp'];
            if (! in_array(strtolower($extension), $allowedExtensions)) {
                return back()->withErrors(['image' => 'Format file tidak diperbolehkan.']);
            }
            $filename = Str::uuid() . '.' . strtolower($extension);
            $file->storeAs('products', $filename, 'public');
            $validated['image'] = 'products/' . $filename;
        }

        $product = Product::create($validated);

        // Handle gallery images
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $index => $galleryFile) {
                $extension = $galleryFile->getClientOriginalExtension();
                $allowedExtensions = ['jpeg', 'jpg', 'png', 'webp'];
                if (! in_array(strtolower($extension), $allowedExtensions)) {
                    continue;
                }
                $filename = Str::uuid() . '.' . strtolower($extension);
                $galleryFile->storeAs('products', $filename, 'public');
                $product->images()->create([
                    'path' => 'products/' . $filename,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil dibuat.');
    }

    public function edit(Product $product)
    {
        $product->load('images');
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0'],
            'is_available' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'gallery.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['integer', 'exists:product_images,id'],
        ]);

        // Handle primary image
        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $allowedExtensions = ['jpeg', 'jpg', 'png', 'webp'];
            if (! in_array(strtolower($extension), $allowedExtensions)) {
                return back()->withErrors(['image' => 'Format file tidak diperbolehkan.']);
            }
            $filename = Str::uuid() . '.' . strtolower($extension);
            $file->storeAs('products', $filename, 'public');
            $validated['image'] = 'products/' . $filename;
        }

        // Remove gallery images if requested
        if (! empty($validated['remove_images'])) {
            $imagesToRemove = ProductImage::whereIn('id', $validated['remove_images'])
                ->where('product_id', $product->id)
                ->get();
            foreach ($imagesToRemove as $img) {
                Storage::disk('public')->delete($img->path);
                $img->delete();
            }
        }
        unset($validated['remove_images']);

        $product->update($validated);

        // Handle new gallery images
        if ($request->hasFile('gallery')) {
            $maxSort = $product->images()->max('sort_order') ?? 0;
            foreach ($request->file('gallery') as $index => $galleryFile) {
                $extension = $galleryFile->getClientOriginalExtension();
                $allowedExtensions = ['jpeg', 'jpg', 'png', 'webp'];
                if (! in_array(strtolower($extension), $allowedExtensions)) {
                    continue;
                }
                $filename = Str::uuid() . '.' . strtolower($extension);
                $galleryFile->storeAs('products', $filename, 'public');
                $product->images()->create([
                    'path' => 'products/' . $filename,
                    'sort_order' => $maxSort + $index + 1,
                ]);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        // Delete images from storage
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        foreach ($product->images as $img) {
            Storage::disk('public')->delete($img->path);
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}
