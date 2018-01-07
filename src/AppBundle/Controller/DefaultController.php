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
            ->add('originUrl', TextType::class, [
                'label' => 'Paste link'
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Generate short link'
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $em->persist($url);
            $em->flush();

            if ($url->getShortUrl() == null) {
                $url->setShortUrl(base64_encode($url->getId()));
            }

            $em->persist($url);
            $em->flush();

            return $this->render('@App/homepage/index.html.twig',[
                'form' => $form->createView(),
                'new_url' => $url->getShortUrl(),
                'amount' => $url->getAmount(),
            ]);

        }

        return $this->render('@App/homepage/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function showAction($short_url)
    {

        $em = $this->getDoctrine()->getManager();

        $url = $em->getRepository(Shortener::class)->findOneBy(['shortUrl' => $short_url ]);
        $amount = $url->getAmount();
        $url->setAmount($amount + 1);

        $em->persist($url);
        $em->flush();

        return $this->redirect($url->getOriginUrl());
    }
}
