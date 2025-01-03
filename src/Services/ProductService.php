<?php

namespace PcbPlus\PcbCpq\Services;

use Illuminate\Support\Facades\DB;
use PcbPlus\PcbCpq\Concerns\HasProduct;
use PcbPlus\PcbCpq\Concerns\HasVersion;
use PcbPlus\PcbCpq\Exceptions\RuntimeException;
use PcbPlus\PcbCpq\Models\Product;
use PcbPlus\PcbCpq\Validators\CreateProductValidator;
use PcbPlus\PcbCpq\Validators\MultisortProductsValidator;
use PcbPlus\PcbCpq\Validators\UpdateProductValidator;

class ProductService
{
    use HasVersion, HasProduct;

    /**
     * @param int $versionId
     * @param array $data
     * @return \PcbPlus\PcbCpq\Models\Product
     * @throws \Illuminate\Validation\ValidationException
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function createProduct($versionId, $data)
    {
        // Validate the input data
        $validator = new CreateProductValidator();
        $validated = $validator->validate($data);

        // Retrieve the necessary models
        $version = $this->findVersionOrAbort($versionId);

        // Validate version is editable
        $this->validateVersionIsEditable($version);

        // Check if the product already exists
        if ($this->isProductExists($version->id, $validated['code'])) {
            throw new RuntimeException('Product already exists');
        }

        // Create product
        return Product::create(array_merge($validated, [
            'version_id' => $version->id,
        ]));
    }

    /**
     * @param int $productId
     * @param array $data
     * @return \PcbPlus\PcbCpq\Models\Product
     * @throws \Illuminate\Validation\ValidationException
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function updateProduct($productId, $data)
    {
        // Validate the input data
        $validator = new UpdateProductValidator();
        $validated = $validator->validate($data);

        // Retrieve the necessary models
        $product = $this->findProductOrAbort($productId);
        $version = $this->findVersionOrAbort($product->version_id);

        // Validate version is editable
        $this->validateVersionIsEditable($version);

        // Check if the product already exists
        if ($this->isProductExists($version->id, $validated['code'], $product->id)) {
            throw new RuntimeException('Product already exists');
        }

        // Update the product
        $product->update($validated);

        return $product;
    }

    /**
     * @param int $productId
     * @return bool
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function deleteProduct($productId)
    {
        // Retrieve the necessary models
        $product = $this->findProductOrAbort($productId);
        $version = $this->findVersionOrAbort($product->version_id);

        // Validate version is editable
        $this->validateVersionIsEditable($version);

        // Begin transaction to delete the product
        return DB::transaction(function () use ($product) {
            return $this->deleteProductAbsolutely($product);
        });
    }

    /**
     * @param int $versionId
     * @param array $data
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     * @throws \PcbPlus\PcbCpq\Exceptions\RuntimeException
     */
    public function multisortProducts($versionId, $data)
    {
        // Validate the input data
        $validator = new MultisortProductsValidator();
        $validated = $validator->validate($data);

        // Retrieve the necessary models
        $version = $this->findVersionOrAbort($versionId);

        // Validate version is editable
        $this->validateVersionIsEditable($version);

        // Multi sort products
        foreach ($validated as $item) {
            $product = Product::where('version_id', $version->id)
                ->where('id', $item['id'])
                ->first();

            if ($product) {
                $product->sort_order = $item['sort_order'];

                $product->save();
            }
        }
    }
}
