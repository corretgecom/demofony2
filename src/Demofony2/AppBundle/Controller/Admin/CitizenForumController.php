<?php

namespace Demofony2\AppBundle\Controller\Admin;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CitizenForumController extends Controller
{
    public function showPublicPageAction()
    {
//        $id = $this->get('request')->get($this->admin->getIdParameter());
//
//        $object = $this->admin->getObject($id);
//
//        if (!$object) {
//            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
//        }
//
//       $url = $this->generateUrl('demofony2_front_participation_discussions_edit', array('id' => $object->getId(), 'discussion' => $object->getTitleSlug()));
//
//        return new RedirectResponse($url);
    }
}
