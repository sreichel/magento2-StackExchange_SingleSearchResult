<?php
declare(strict_types=1);

namespace StackExchange\SingleSearchResult\Observer;

use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\LayoutInterface;

class RedirectToProduct implements ObserverInterface
{
    /**
     * the product list block name in layout
     */
    public const RESULT_BLOCK_NAME = 'search_result_list';

    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    /**
     * RedirectToProduct constructor.
     * @param LayoutInterface $layout
     * @param ResponseFactory $responseFactory
     */
    public function __construct(
        LayoutInterface $layout,
        ResponseFactory $responseFactory
    ) {
        $this->layout = $layout;
        $this->responseFactory = $responseFactory;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /** @var ListProduct $block */
        $block = $this->layout->getBlock(self::RESULT_BLOCK_NAME);
        if ($block) {
            $collection = $block->getLoadedProductCollection();
            if ($collection && $collection->getSize() === 1) {
                /** @var Product $product */
                $product = $collection->getFirstItem();
                $url = $product->getProductUrl();
                if ($url) {
                    $this->responseFactory->create()->setRedirect($url)->sendResponse();
                    exit;
                }
            }
        }
    }
}
