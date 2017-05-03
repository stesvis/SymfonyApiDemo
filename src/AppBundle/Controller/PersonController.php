<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Person;
use AppBundle\Form\PersonType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Person controller.
 *
 * @Route("persons")
 */
class PersonController extends Controller
{
    /**
     * Lists all person entities.
     *
     * @Route("/", name="person_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        //************************************************
        //***** Here i want to use GET /api/persons/ *****
        //************************************************

        //------------------ Why does this not work? ------------------//
//        $personController = new \ApiBundle\Controller\PersonController();
//        $people = $personController->getAllAction();

        $response = $this->forward('ApiBundle:Person:getAll');
        $content = $response->getContent();

        $people = json_decode($content, true);

        // it gives me this array
//        array:2 [▼
//              0 => array:4 [
//                "id" => 1
//                "first_name" => "Robert"
//                "last_name" => "De Niro"
//                "born_at" => "2012-01-01T00:00:00+0100"
//              ]
//              1 => array:4 [
//                "id" => 2
//                "first_name" => "Julia"
//                "last_name" => "Roberts"
//                "born_at" => "2017-07-12T00:00:00+0200"
//              ]
//        ]

        //------------------ Using Doctrine ------------------//
        $people = $em->getRepository('AppBundle:Person')->findAll();

        return $this->render('person/index.html.twig', array(
            'people' => $people, //???
        ));
    }

    /**
     * Displays a form to edit an existing person entity.
     *
     * @Route("/{id}/edit", name="person_edit")
     * @Method({"GET", "POST"})
     *
     * @param $request Request
     * @param $id int
     *
     * @return Response
     */
    public function editAction(Request $request, int $id)
    {
        $em = $this->getDoctrine()->getManager();

        //if i get a Person with this line, when i save it it creates a new row instead of updating the existing one
//        $person = $this->getPerson($id);

        $person = $em->getRepository('AppBundle:Person')
            ->findOneBy([
                'id' => $id
            ]);

        $deleteForm = $this->createDeleteForm($person);
        $editForm = $this->createForm(PersonType::class, $person);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $person = $editForm->getData();

            //****************************************************
            //***** Here i want to use PUT /api/persons/{id} *****
            //****************************************************

            //------------------ Why does this not work? ------------------//
//        $personController = new \ApiBundle\Controller\PersonController();
//        $people = $personController->getAllAction();

            //------------------ How do I pass the $person to that action? ------------------//
//            $response = $this->forward('ApiBundle:Person:edit', [
//                'request' => $request,
//                'id' => $id
//            ]);

            //------------------ Using Doctrine ------------------//
            $em->persist($person);
            $em->flush();

            return $this->redirectToRoute('person_edit', array('id' => $person->getId()));
        }

        return $this->render('person/edit.html.twig', array(
            'person' => $person,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a new person entity.
     *
     * @Route("/new", name="person_new")
     * @Method({"GET", "POST"})
     *
     * @param $request Request
     * @return Response
     */
    public function newAction(Request $request)
    {
        $person = new Person();
        $form = $this->createForm('AppBundle\Form\PersonType', $person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //****************************************************
            //***** Here i want to use POST /api/persons/new *****
            //****************************************************

            //------------------ Using Doctrine ------------------//
            $em = $this->getDoctrine()->getManager();
            $em->persist($person);
            $em->flush();

            return $this->redirectToRoute('person_show', array('id' => $person->getId()));
        }

        return $this->render('person/new.html.twig', array(
            'person' => $person,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a person entity.
     *
     * @Route("/{id}", name="person_show")
     * @Method("GET")
     *
     * @param $id int
     * @return Response
     */
    public function showAction(int $id)
    {
        //***** Here i want to use GET /api/persons/{id}
        // this solution seems to work
        $person = $this->getPerson($id);

        //------------------ Why does this not work? ------------------//
//        $personController = new \ApiBundle\Controller\PersonController();
//        $response = $personController->getOneAction($id);
//        $content = $response->getContent();
//        $serializer = $this->get('jms_serializer');
//        $person = $serializer->deserialize($content, Person::class, 'json');

        $deleteForm = $this->createDeleteForm($person);

        return $this->render('person/show.html.twig', array(
            'person' => $person,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a person entity.
     *
     * @Route("/{id}", name="person_delete")
     * @Method("DELETE")
     *
     * @param $request Request
     * @param $id int
     * @return Response
     */
    public function deleteAction(Request $request, int $id)
    {
        $person = $this->getPerson($id);

        $form = $this->createDeleteForm($person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //***** Here i want to use DELETE /api/persons/{id}
            // this solution seems to work
            $response = $this->forward('ApiBundle:Person:delete', [
                'id' => $id
            ]);

            //------------------ Why does this not work? ------------------//
//            $personController = new \ApiBundle\Controller\PersonController();
//            $response = $personController->deleteAction($id);

            //------------------ Using Doctrine ------------------//
//            $em = $this->getDoctrine()->getManager();
//            $em->remove($person);
//            $em->flush();
        }

        return $this->redirectToRoute('person_index');
    }

    /**
     * Creates a form to delete a person entity.
     *
     * @param Person $person The person entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Person $person)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('person_delete', array('id' => $person->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * @param int $id
     * @return array|\JMS\Serializer\scalar|mixed|object
     */
    private function getPerson(int $id)
    {
        $response = $this->forward('ApiBundle:Person:getOne', [
            'id' => $id
        ]);
        $content = $response->getContent();
        $serializer = $this->get('jms_serializer');
        $person = $serializer->deserialize($content, Person::class, 'json');

        return $person;
    }
}
