<?php

namespace AppBundle\Services;

use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Person;

/**
 * Class PeronsManager
 */
class PersonsManager
{
    const PERSONS_REPO_NAME = 'AppBundle:Person';
    /**
     * @var object EntityManager
     */
    protected $em;

    /**
     * @var object Container
     */
    protected $container;

    /**
     * @var object EntityRepository
     */
    protected $personsRepo;

    /**
     * Constructor.
     *
     * @param EntityManager $em
     * @param Container     $container
     */
    public function __construct(
        EntityManager $em,
        Container $container
    ) {
        $this->em = $em;
        $this->container = $container;
        $this->personsRepo = $em->getRepository(self::PERSONS_REPO_NAME);
    }

    /**
     * Return all persons
     * @return array
     */
    public function getAllPersons()
    {
        return $this->personsRepo->findAll();
    }

    /**
     * Return one person
     * @param  integer $id
     * @return mixed null|Person
     */
    public function getOnePersonById(int $id)
    {
        return $this->personsRepo->findOneById($id);
    }

    /**
     * Create person
     * @param  Request $request
     * @return object Person
     */
    public function create(Request $request)
    {
        return $this->processForm($request, new Person());
    }

    /**
     * Update person
     * @param  Request $request
     * @param  Person $person
     * @return object Person
     */
    public function update(Request $request, Person $person)
    {
        return $this->processForm($request, $person);
    }

    /**
     * Delete person
     * @param  Person $person
     * @return void
     */
    public function delete(Person $person)
    {
        $this->em->remove($person);
        $this->em->flush();
    }

    /**
     * Save person
     * @param  Person $person
     * @return object Person
     */
    public function save(Person $person)
    {
        $this->em->persist($person);
        $this->em->flush();

        return $person;
    }

    /**
     * Create a form for person entity
     * @param  Person $person
     * @return object \Symfony\Component\Form\Form
     */
    public function createPersonForm(Person $person)
    {
        return $this->container->get('form.factory')->create(\AppBundle\Form\PersonType::class, $person);
    }

    /**
     * Process person form
     * @param  Request $request
     * @param  Person $person
     * @return object Person
     */
    private function processForm(Request $request, Person $person)
    {
        $form = $this->createPersonForm($person);
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            $data = json_decode($request->getContent(), true);
            $form->submit($data);
        }

        $person = $form->getData();
        
        return $this->save($person);
    }
}