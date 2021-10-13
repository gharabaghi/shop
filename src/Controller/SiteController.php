<?php
namespace App\Controller;

use App\Repository\ProductRepository;
use App\Service\AppCache;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SiteController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(ProductRepository $productRepo, PaginatorInterface $paginator, Request $request, AppCache $appCache): Response
    {
        $search = $request->query->get('search');
        $sort = $request->query->get('sort');

        $direction = $request->query->get('direction');
        $direction = in_array($direction, ['DESC', 'ASC']) ? $direction : 'DESC';

        $target = $productRepo->initailizeQueryBuilderInstance()->qbFindAllAvailableProducts();

        //search products
        if ($search) {
            $target = $target->qbSearch($search);
        }

        //sort products
        $sortArray = ['price', 'creaeteAt', 'name', 'visit'];
        if (in_array($sort, $sortArray)) {
            $target = $target->qbOrderBy($sort, $direction);
        }

        $target = $target->qbGetResult();
        $pagination = $paginator->paginate(
            $target,
            $request->query->getInt('page', 1),
            $this->getParameter('app.index_page.per_page')
        );

        $latests = $appCache->getCacheItem(AppCache::APP_CACHE_KEY_RECENT_PRODUCTS);
        $populars = $appCache->getCacheItem(AppCache::APP_CACHE_KEY_MOST_VIEWED_PRODUCTS);

        return $this->render('index.html.twig', [
            'pagination' => $pagination,
            'latests' => $latests,
            'populars' => $populars,
        ]);
    }

    /**
     * @Route("/about-us", name="about_us")
     */
    public function aboutUs()
    {
        return $this->render('about-us.html.twig');
    }
}
