<?php
namespace App\Service;

use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class UserSessionManage
{
    const USER_SESSION_KEY_CARD = 'CARD';

    /**
     * @var Security scurity
     */
    private $seurity;

    /**
     * @var RequestStack scurity
     */
    private $requestStack;

    public function __construct(Security $security, RequestStack $requestStack)
    {
        $this->security = $security;
        $this->requestStack = $requestStack;
    }

    public function rebuildSessionItem(string $key)
    {
        switch ($key) {
            case self::USER_SESSION_KEY_CARD:
                $this->rebuildCard();
                break;
        }
    }

    private function rebuildCard()
    {
        $user = $this->security->getUser();

        $session = $this->requestStack->getSession();
        $session->remove(self::USER_SESSION_KEY_CARD);
        $session->set(self::USER_SESSION_KEY_CARD, $user->getCards()->toArray());

        return true;
    }
}
