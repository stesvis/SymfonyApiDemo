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
        //get persons service
        $personsManager = $this->get('persons.manager');

        return $this->render('person/index.html.twig', array(
            'people' => $personsManager->getAllPersons()
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
        $personsManager = $this->get('persons.manager');

        $person = $personsManager->getOnePersonById($id);
        if (null == $person) {
            throw $this->createNotFoundException(sprintf("Person with id: %s not found!", $id));
        }

        $deleteForm = $this->createDeleteForm($person);
        $editForm = $this->createForm(PersonType::class, $person);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $person = $editForm->getData();

            $personsManager->update($request, $person);

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
        $personsManager = $this->get('persons.manager');
        $form = $personsManager->createPersonForm(new Person());
        
        if ($request->isMethod("POST")) {
            $person = $personsManager->create($request);
            return $this->redirectToRoute('person_show', array('id' => $person->getId()));
        }

        return $this->render('person/new.html.twig', array(
            'form' => $form->createView()
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
        $personsManager = $this->get('persons.manager');
        $person = $personsManager->getOnePersonById($id);
        if ($person == null) {
            throw $this->createNotFoundException(sprintf("Person with id: %s not found!", $id));
        }

        $deleteForm = $this->createDeleteForm($person);

        return $this->render('person/show.html.twig', array(
            'person' => $person,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a person entity.
     *
     * @Route("/delete/{id}", name="person_delete")
     * @Method("DELETE")
     *
     * @param $request Request
     * @param $id int
     * @return Response
     */
    public function deleteAction(Request $request, int $id)
    {
        $personsManager = $this->get('persons.manager');
        $person = $personsManager->getOnePersonById($id);
        if ($person == null) {
            throw $this->createNotFoundException(sprintf("Person with id: %s not found!", $id));
        }
        
        $personsManager->delete($person);

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
}
