<?php
namespace App\Controller;

use App\Entity\Card;
use App\Entity\Product;
use App\Entity\User;
use App\Service\UserSessionManage;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Service\AppSecurity;

// use symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @isGranted("ROLE_USER")
 */
class CardController extends AbstractController
{
    use AppSecurity;
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(ValidatorInterface $validator, LoggerInterface $logger)
    {
        $this->validator = $validator;
        $this->logger = $logger;
    }

    /**
     * @Route("/card", name="card", methods={"get"})
     */
    public function index(): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $cardItems = $user->getCards();

        return $this->render('card/index.html.twig', [
            'items' => $cardItems
        ]);
    }

    /**
     * @Route("/card/{id}", methods={"post"}, name="add_to_card")
     */
    public function add(Product $product, EntityManagerInterface $em, Request $request, UserSessionManage $sessionManage, RequestStack $rs)
    {
        $this->checkCsrfToken('user-add-card-item', $request);

        $validationResult = $this->validateCardRequest($request);
        if ($validationResult !== true) {
            $this->addFlash('message', $validationResult);
            return $this->redirect($this->generateUrl('product_show', ['id' => $product->getId()]));
        }

        /** @var User $user*/
        $user = $this->getUser();

        if (
            $user->getCards()->exists(function ($key, Card $c) use ($product) {
                return $c->getProduct()->getId() == $product->getId();
            })
        ) {
            $this->addFlash('message', 'You already added this item to your card.');
            return $this->redirect($this->generateUrl('product_show', ['id' => $product->getId()]));
        }

        $count = $request->request->get('count');

        if ($count > $product->getCount()) {
            $this->addFlash('message', 'We have just ' . $product->getCount() . ' items from product '
                . $product->getName() . '. Please change the item and trye again');
            return $this->redirect($this->generateUrl('product_show', ['id' => $product->getId()]));
        }

        $card = new Card();
        $card->setCount($count)->setUser($user)->setProduct($product);

        $em->persist($card);
        $em->flush();

        $em->refresh($user);
        $sessionManage->rebuildSessionItem(UserSessionManage::USER_SESSION_KEY_CARD);

        $this->addFlash('message', 'Added to your card');
        return $this->redirect($this->generateUrl('product_show', ['id' => $product->getId()]));
    }

    /**
     * @Route("/updateCard/{id}", methods={"post"}, name="update_card")
     * @isGranted("CARD_UPDATE_ITEM", subject="card")
     */
    public function update(Card $card, EntityManagerInterface $em, Request $request, UserSessionManage $sessionManage)
    {
        $this->checkCsrfToken('user-update-card-item', $request);

        $validationResult = $this->validateCardRequest($request);
        if ($validationResult !== true) {
            $this->addFlash('message', $validationResult);
            return $this->redirect($this->generateUrl('card'));
        }

        $count = $request->request->get('count');

        $card->setCount($count);

        $em->flush();

        $sessionManage->rebuildSessionItem(UserSessionManage::USER_SESSION_KEY_CARD);

        $this->addFlash('message', 'Updated!');
        return $this->redirect($this->generateUrl('card'));
    }

    /**
     * @Route("deleteCard/{id}", methods={"post"}, name="delete_from_card")
     * @isGranted("CARD_DELETE_ITEM", subject="card")
     * 
     */
    public function remove(Card $card, EntityManagerInterface $em, UserSessionManage $sessionManage, Request $request)
    {
        $this->checkCsrfToken('user-delete-card-item', $request);
        
        $em->remove($card);
        $em->flush();

        $sessionManage->rebuildSessionItem(UserSessionManage::USER_SESSION_KEY_CARD);

        $this->addFlash('message', 'Deleted!');
        return $this->redirect($this->generateUrl('card'));
    }

    /**
     * @return bool
     */
    private function userHasAccessToCard(User $user, Card $card)
    {
        return $user->getId() == $card->getUser()->getId();
    }

    /**
     * @return bool|string
     */
    private function validateCardRequest(Request $request)
    {
        $constraints = new Assert\Collection([
            'count' => [new Assert\Regex("/^\d*$/"), new Assert\Positive()],
            'token' => [new Assert\Type('string')]
        ]);

        $violations = $this->validator->validate($request->request->all(), $constraints);
        if (count($violations) > 0) {
            return $violations[0]->getMessage();
        }

        return true;
    }
}
