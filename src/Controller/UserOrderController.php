<?php
namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Card;
use App\Service\UserSessionManage;
use Symfony\Component\HttpFoundation\Request;
use App\Service\AppSecurity;

class UserOrderController extends AbstractController
{
    use AppSecurity;

    /**
     * @Route("/user/order", name="user_make_order", methods={"post"})
     */
    public function order(EntityManagerInterface $em, UserSessionManage $sm, Request $request)
    {
        $this->checkCsrfToken('user-make-order', $request);

        /** @var User  */
        $user = $this->getUser();

        /** @var Card[] */
        $card = $user->getCards();

        $em->getConnection()->beginTransaction();

        $order = new Order();
        $price = 0;
        $order->setUser($user);
        $order->setPrice($price);
        foreach ($card as $c) {
            $itemCount = $c->getCount();
            $product = $c->getProduct();

            if ($itemCount > $product->getCount()) {
                $em->getConnection()->rollBack();
                $this->addFlash('message', 'We have just ' . $product->getCount() . ' items from product ' . $product->getName() . '. Please change the item and trye again');
                return $this->redirect($this->generateUrl('card'));
            }

            $orderItem = new OrderItem();
            $orderItem->setOrder($order)->setProduct($product)->setCount($itemCount);

            $price += $product->getPrice() * $itemCount;
            $product->decreaseCount($itemCount);

            $em->remove($c);
            $em->persist($orderItem);
        }

        $order->setPrice($price);

        $em->getConnection()->commit();

        $em->persist($order);
        $em->flush();

        $sm->rebuildSessionItem(UserSessionManage::USER_SESSION_KEY_CARD);

        $this->addFlash('message', 'Order Completed');
        return $this->redirect($this->generateUrl('index'));
    }

    /**
     * @Route("/user/order", name="user_orders", methods={"get"})
     */
    public function index()
    {
        /**
         * @var Card[]
         */
        $orders = $this->getUser()->getOrders();

        return $this->render('user_order/index.html.twig', [
            'orders' => $orders]);
    }

    /**
    * @Route("/user/order/{id}", name="user_order", methods={"get"})
    */
    public function show(Order $order)
    {
        $user = $this->getUser();
        if ($user->getId() != $order->getUser()->getId()) {
            $this->logger->critical('User: ' . $user->getId() . ' tries to access order belong to user:' . $order->getUser()->getId());
            throw $this->createAccessDeniedException('You don\' have access to this resource.');
        }

        return $this->render('user_order/show.html.twig', [
            'order' => $order]);
    }
}
