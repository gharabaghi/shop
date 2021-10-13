<?php
namespace App\Service;

use App\Repository\ProductRepository;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Config\Definition\Exception\UnsetKeyException;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class AppCache
{
    /**
     * @va ProductRepository $productRepo
     */
    private $productRepo;

    /**
     *
     * @var ContainerBagInterface $params
     */
    private $params;

    public const APP_CACHE_KEY_RECENT_PRODUCTS = 'RECENT_PRODUCTS';
    public const APP_CACHE_KEY_MOST_VIEWED_PRODUCTS = 'MOST_VIEWED_PRODUCTS';

    public function __construct(ProductRepository $productRepo, ContainerBagInterface $params)
    {
        $this->productRepo = $productRepo;

        $this->params = $params;
    }

    /**
     * Looking for cache item. if doesn't exist will rebuild the cache item. finally returns cache item.
     *
     * @param string $key
     * @return Product[]
     */
    public function getCacheItem(string $key)
    {
        switch ($key) {
            case self::APP_CACHE_KEY_RECENT_PRODUCTS:
                return $this->cacheRecentProducts();
                break;

                case self::APP_CACHE_KEY_MOST_VIEWED_PRODUCTS:
                    return $this->cacheMostViewedProducts();
                    break;

                default:
                    throw new UnsetKeyException('The key is incorrect');
                    break;
        }
    }

    /**
     * Deleting cache item and the rebuild that. finally returns cache item.
     *
     * @param string $key
     * @return Product[]
     */
    public function reCacheItem(string $key)
    {
        switch ($key) {
            case self::APP_CACHE_KEY_RECENT_PRODUCTS:
                return $this->recacheRecentProducts();
            break;

            case self::APP_CACHE_KEY_MOST_VIEWED_PRODUCTS:
                return $this->recacheMostViewedProducts();
            break;

            default:
                throw new UnsetKeyException('The key is incorrect');
            break;
        }
    }

    /**
     * Checks if cache items exists. if not rebuilds the item. finally returns cache item.
     *
     * @return Product[]
     */
    private function cacheRecentProducts()
    {
        $count = $this->params->get('app.index_page.recent_products_count');

        $cache = new FilesystemAdapter();
        $recentProducts = $cache->getItem(self::APP_CACHE_KEY_RECENT_PRODUCTS);
        if (!$recentProducts->isHit()) {
            $products = $this->productRepo->initailizeQueryBuilderInstance()->qbFindAllAvailableProducts()->
                qbLimit($count)->qbOrderBy('createdAt', 'DESC')->qbGetResult();

            $recentProducts->set($products);
            $cache->save($recentProducts);
        }

        return $cache->getItem(self::APP_CACHE_KEY_RECENT_PRODUCTS)->get();
    }

    /**
    * Deletes the cache item and rebuilds it. Then return the cached item.
    *
    * @return Product[]
    */
    private function recacheRecentProducts()
    {
        $cache = new FilesystemAdapter();
        $cache->deleteItem(self::APP_CACHE_KEY_RECENT_PRODUCTS);
        return $this->cacheRecentProducts();
    }

    /**
    * Checks if cache items exists. if not rebuilds the item. finally returns cache item.
    *
    * @return Product[]
    */
    private function cacheMostViewedProducts()
    {
        $count = $this->params->get('app.index_page.newest_products_count');

        $cache = new FilesystemAdapter();
        $recentProducts = $cache->getItem(self::APP_CACHE_KEY_MOST_VIEWED_PRODUCTS);
        if (!$recentProducts->isHit()) {
            $products = $this->productRepo->initailizeQueryBuilderInstance()->qbFindAllAvailableProducts()->
                qbLimit($count)->qbOrderBy('visit', 'DESC')->qbGetResult();

            $recentProducts->set($products);
            $cache->save($recentProducts);
        }

        return $cache->getItem(self::APP_CACHE_KEY_MOST_VIEWED_PRODUCTS)->get();
    }

    /**
    * Deletes the cache item and rebuilds it. Then return the cached item.
    *
    * @return Product[]
    */
    private function recacheMostViewedProducts()
    {
        $cache = new FilesystemAdapter();
        $cache->deleteItem(self::APP_CACHE_KEY_MOST_VIEWED_PRODUCTS);
        return $this->cacheRecentProducts();
    }
}
