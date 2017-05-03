<?php

namespace ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PersonController
 *
 * @Route("/api/persons")
 *
 * @package ApiBundle\Controller
 */
class PersonController extends Controller
{
    /**
     * @Route("/", name="api_persons_list")
     * @Method("GET")
     */
    public function getAllAction()
    {

    }

    /**
     * @Route("/{id}", name="api_persons_show")
     * @Method("GET")
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getOneAction(int $id)
    {
        return new JsonResponse('Found person', JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/{id}")
     * @Method("PUT")
     *
     * @param Request $request
     * @param int $id
     */
    public function editAction(Request $request, int $id)
    {

    }

    /**
     * @Route("/new", name="api_persons_new")
     * @Method("POST")
     *
     * @param Request $request
     */
    public function createAction(Request $request)
    {

    }

    /**
     * @Route("/{id}", name="api_persons_delete")
     * @Method("DELETE")
     *
     * @param int $id
     */
    public function deleteAction(int $id)
    {

    }
}
