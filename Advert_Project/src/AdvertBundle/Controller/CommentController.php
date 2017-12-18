<?php

namespace AdvertBundle\Controller;

use AdvertBundle\Entity\Advert;
use AdvertBundle\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CommentController
 * @package AdvertBundle\Controller
 * @Route("comment")
 */
class CommentController extends Controller
{
    /**
     * @Route("/{id}", name="comment_new")
     */
    public function newAction(Request $req, $id){
        $em = $this->getDoctrine()->getManager();
        $advert = $em->getRepository('AdvertBundle:Advert')->find($id);

        $comment = new Comment();
        $comment->setAdvert($advert);

        $form = $this->getFictionForm($comment,$id);

        $form->handleRequest($req);
        if($form->isSubmitted()){
            $data = $form->getData();
            $em->persist($data);
            $em->flush();

            return $this->redirectToRoute('advert_show',['id' => $id]);
        }

        return $this->render('comment/comment.html.twig',['form' => $form->createView()]);
    }

    protected function getFictionForm(Comment $comment, $id){
        $form = $this->createFormBuilder($comment)
            ->setAction("")
            ->setMethod('GET')
            ->add('text', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Add Comment'])
            ->getForm();

        return $form;
    }
}
