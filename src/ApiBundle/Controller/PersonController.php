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
            $em = $this->getDoctrine()->getManager();
            $persons = $em->getRepository('AppBundle:Person')
                ->findAll();

            $serializer = $this->get('jms_serializer');

            $response = new JsonResponse($serializer->toArray($persons), JsonResponse::HTTP_OK);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        } catch (Exception $e) {
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
            $em = $this->getDoctrine()->getManager();

            $person = $em->getRepository('AppBundle:Person')
                ->findOneBy([
                    'id' => $id,
                ]);

            // Check if it exists
            if (!$person) {
                return new JsonResponse('No person found with Id = ' . $id, JsonResponse::HTTP_OK);
                //            throw $this->createNotFoundException(sprintf('No person found with Id = ' . $id));
            }

            $serializer = $this->get('jms_serializer');
            $response = new JsonResponse($serializer->toArray($person), JsonResponse::HTTP_OK);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        } catch (Exception $e) {
            return new JsonResponse($e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/{id}")
     * @Method("PUT")
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function editAction(Request $request, int $id)
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $person = $em->getRepository('AppBundle:Person')
                ->findOneBy([
                    'id' => $id,
                ]);

            if (!$person) {
                return new JsonResponse('No person found with Id = ' . $id, JsonResponse::HTTP_OK);
                //            throw $this->createNotFoundException(sprintf('No person found with Id = ' . $id));
            }

            return $this->processForm($request, $person);
        } catch (Exception $e) {
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
            return $this->processForm($request, new Person());
        } catch (Exception $e) {
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
            $em = $this->getDoctrine()->getManager();
            $person = $em->getRepository('AppBundle:Person')
                ->findOneBy([
                    'id' => $id,
                ]);

            if (!$person) {
                return new JsonResponse('No person found with Id = ' . $id, JsonResponse::HTTP_OK);
                //            throw $this->createNotFoundException(
                //                'No person found with Id = ' . $id
                //            );
            }

            // Safe to remove
            $em->remove($person);
            $em->flush();

            $response = new JsonResponse("Person deleted", JsonResponse::HTTP_OK);
            $response->headers->set('Location', $this->generateUrl('person_show', [
                'id' => $person->getId()
            ]));

            return $response;
        } catch (Exception $e) {
            return new JsonResponse($e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function processForm(Request $request, Person $person)
    {
        $data = json_decode($request->getContent(), true);
        $form = $this->createForm(PersonType::class, $person);
        $form->submit($data);

        $em = $this->getDoctrine()->getManager();
        $em->persist($person);
        $em->flush();

        $serializer = $this->get('jms_serializer');
        $response = new JsonResponse($serializer->toArray($person), JsonResponse::HTTP_CREATED);
        $response->headers->set('Location', $this->generateUrl('person_show', [
            'id' => $person->getId()
        ]));

        return $response;
    }

}
