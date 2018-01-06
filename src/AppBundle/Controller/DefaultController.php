<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\Shortener;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{

    public function indexAction(Request $request)
    {

        $url = new Shortener();
        $em = $this->getDoctrine()->getManager();

        $form = $this->createFormBuilder($url)
            ->add('originUrl', TextType::class)
            ->add('shortUrl', TextType::class,[
                'required' => false
            ])
            ->add('save', SubmitType::class, ['label' => 'Generate short link'])
            ->getForm();
        $form->handleRequest($request);

        return $this->render('@App/homepage/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
