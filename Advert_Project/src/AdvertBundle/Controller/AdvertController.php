<?php

namespace AdvertBundle\Controller;

use AdvertBundle\Entity\Advert;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Advert controller.
 *
 * @Route("advert")
 */
class AdvertController extends Controller
{
    /**
     * Lists all advert entities.
     *
     * @Route("/", name="advert_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $adverts = $em->getRepository('AdvertBundle:Advert')->findAll();
        $categories = $this->getAllCategories();

        return $this->render('advert/index.html.twig', array(
            'adverts' => $adverts,
            'categories' => $categories
        ));
    }

    /**
     * @Route("/category", name="advert_category")
     * @Method("GET")
     */
    public function byCategoryAction(Request $req){
        $category_id = $req->query->get('category');

        $em = $this->getDoctrine()->getManager();

        if($category_id == 'null'){
            return $this->redirectToRoute('advert_index');
        }
        $category = $em->getRepository('AdvertBundle:Category')->find($category_id);
        $adverts = $em->getRepository('AdvertBundle:Advert')->findBy(['category' => $category]);
        $categories = $this->getAllCategories();

        return $this->render('advert/index.html.twig', array(
            'adverts' => $adverts,
            'categories' => $categories
        ));
    }
    /**
     * Creates a new advert entity.
     *
     * @Route("/new", name="advert_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_USER')")
     */
    public function newAction(Request $request)
    {
        $advert = new Advert();
        $form = $this->createForm('AdvertBundle\Form\AdvertType', $advert);
        $user = $this->getUser();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $advert->setUser($user);
            $em->persist($advert);
            $em->flush();

            return $this->redirectToRoute('advert_show', array('id' => $advert->getId()));
        }

        return $this->render('advert/new.html.twig', array(
            'advert' => $advert,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a advert entity.
     *
     * @Route("/{id}", name="advert_show")
     * @Method("GET")
     */
    public function showAction(Advert $advert)
    {
        $deleteForm = $this->createDeleteForm($advert);
        $comments = $this->getCommentsByAdvert($advert);

        return $this->render('advert/show.html.twig', array(
            'advert' => $advert,
            'delete_form' => $deleteForm->createView(),
            'comments' => $comments
        ));
    }

    /**
     * Displays a form to edit an existing advert entity.
     *
     * @Route("/{id}/edit", name="advert_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Advert $advert)
    {
        $deleteForm = $this->createDeleteForm($advert);
        $editForm = $this->createForm('AdvertBundle\Form\AdvertType', $advert);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('advert_edit', array('id' => $advert->getId()));
        }

        return $this->render('advert/edit.html.twig', array(
            'advert' => $advert,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a advert entity.
     *
     * @Route("/{id}", name="advert_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Advert $advert)
    {
        $form = $this->createDeleteForm($advert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($advert);
            $em->flush();
        }

        return $this->redirectToRoute('advert_index');
    }

    /**
     * Creates a form to delete a advert entity.
     *
     * @param Advert $advert The advert entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Advert $advert)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('advert_delete', array('id' => $advert->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Return all comments by the advert
     *
     * @param Advert $advert
     * @return array
     */
    protected function getCommentsByAdvert(Advert $advert){

        $em = $this->getDoctrine()->getManager();

        $comments = $em->getRepository('AdvertBundle:Comment')->findBy([
            'advert' => $advert
        ]);

        return $comments;
    }

    /**
     * Return all categories
     *
     * @return array
     *
     */
    protected function getAllCategories(){

        $em = $this->getDoctrine()->getManager();

        $categories = $em->getRepository('AdvertBundle:Category')->findAll();

        return $categories;
    }
}
