<?php
namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Service\AppSecurity;

/**
 * @Security("is_granted('ROLE_ADMIN') and is_granted('ROLE_ADMIN_PRODUCT')")
 */
class AdminProductController extends AbstractController
{
    use AppSecurity;

    /**
     * @Route("/admin/products", name="admin_products")
     */
    public function index(ProductRepository $pr, PaginatorInterface $paginator, Request $request): Response
    {
        $pagination = $paginator->paginate(
            $pr->findAll(),
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('admin_product/index.html.twig', [
            'pagination' => $pagination,
            'controller_name' => 'AdminProductController',
        ]);
    }

    /**
     * @Route("/admin/products/{id}", name="admin_product")
     */
    public function show(Product $product)
    {
        return $this->render('admin_product/show.html.twig', [
            'product' => $product
        ]);
    }

    /**
     * @Route("/admin/deleteProduct/{id}",name="admin_delete_product")
     */
    public function remove(Product $product, EntityManagerInterface $em, Request $request)
    {
        $this->checkCsrfToken('admin-delete-product', $request);

        $em->remove($product);
        $em->flush();

        $this->addFlash('message', 'Deleted!');
        return $this->redirect($this->generateUrl('admin_products'));
    }
}
