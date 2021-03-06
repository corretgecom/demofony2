<?php

namespace Demofony2\AppBundle\Controller\Admin;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * CitizenInitiativeController.
 */
class CitizenInitiativeController extends Controller
{
    /**
     * @return RedirectResponse
     */
    public function showPublicPageAction()
    {
        $id = $this->get('request')->get($this->admin->getIdParameter());

        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        $url = $this->generateUrl('demofony2_front_participation_citizen_initiative_detail', array('id' => $object->getId()));

        return new RedirectResponse($url);
    }
}
