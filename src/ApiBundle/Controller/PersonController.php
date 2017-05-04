<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\Person;
use AppBundle\Form\PersonType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        try {
            $personsManager = $this->get('persons.manager');
            $persons = $personsManager->getAllPersons();

            $serializer = $this->get('jms_serializer');

            $response = new JsonResponse($serializer->toArray($persons), JsonResponse::HTTP_OK);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
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
        try {
            $personsManager = $this->get('persons.manager');
            $person = $personsManager->getOnePersonById($id);
            
            // Check if it exists
            if (!$person) {
                return new JsonResponse('No person found with Id = ' . $id, JsonResponse::HTTP_NO_CONTENT);
            }

            $serializer = $this->get('jms_serializer');
            $response = new JsonResponse($serializer->toArray($person), JsonResponse::HTTP_OK);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/edit/{id}")
     * @Method("PUT")
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function editAction(Request $request, int $id)
    {
        try {
            $personsManager = $this->get('persons.manager');
            $person = $personsManager->getOnePersonById($id);

            if (!$person) {
                return new JsonResponse('No person found with Id = ' . $id, JsonResponse::HTTP_NO_CONTENT);
            }

            $person = $personsManager->update($request, $person);
            return new JsonResponse(sprintf("Successfully update person with id: %s", $person->getId()), JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/new", name="api_persons_new")
     * @Method("POST")
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function createAction(Request $request)
    {
        try {
            $personsManager = $this->get('persons.manager');
            $person = $personsManager->create($request);

            return new JsonResponse(sprintf("Successfully created person with id: %s", $person->getId()), JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/{id}", name="api_persons_delete")
     * @Method("DELETE")
     *
     * @param int $id
     * @return Response
     */
    public function deleteAction(int $id)
    {
        try {
            $personsManager = $this->get('persons.manager');
            $person = $personsManager->getOnePersonById($id);

            if (!$person) {
                return new JsonResponse('No person found with Id = ' . $id, JsonResponse::HTTP_NO_CONTENT);
            }

            // Safe to remove
            $personsManager->delete($person);

            $response = new JsonResponse("Person deleted", JsonResponse::HTTP_OK);
            $response->headers->set('Location', $this->generateUrl('person_index'));

            return $response;
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
