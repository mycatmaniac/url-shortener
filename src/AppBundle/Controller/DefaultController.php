<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\Shortener;
use AppBundle\Services\ShortenerServices;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;

class DefaultController extends Controller
{

    public function indexAction(Request $request)
    {

        $url = new Shortener();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createFormBuilder($url)
            ->add('originUrl', TextType::class)
            ->add('shortUrl', TextType::class, [
                'required' => false,
            ])
            ->add('save', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $shortener_service = new ShortenerServices($data->getOriginUrl(), $data->getShortUrl(), $em);
            $retcode = $shortener_service->checkResponse();
            $exist_short_url = $shortener_service->checkExistUrl();

            // check response code
            if ($retcode != 200) $form->addError(new FormError('Link broken, check available'));

            // set short url
            if ($exist_short_url != null) {

                $url->setShortUrl(null);

                // persist to get an id
                $em->persist($url);
                $em->flush();

                $url->setShortUrl(base64_encode($url->getId()));

            } else {
                $url->setShortUrl($data->getShortUrl());
            }

            $em->persist($url);
            $em->flush();

            return $this->render('@App/homepage/index.html.twig', [
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

        if ($url){
            $amount = $url->getAmount();
            $url->setAmount($amount + 1);

            $em->persist($url);
            $em->flush();
        } else {
            return $this->render('@App/show/index.html.twig');
        }

        return $this->redirect($url->getOriginUrl());
    }

    public function apiAction($short_url)
    {

        $em = $this->getDoctrine()->getManager();
        $url = $em->getRepository(Shortener::class)->findOneBy(['shortUrl' => $short_url ]);

        $serializer = $this->container->get('jms_serializer');
        $reports = $serializer->serialize($url, 'json');
        return new Response($reports);

    }

    public function apiViewAction($short_url)
    {

        $em = $this->getDoctrine()->getManager();
        $url = $em->getRepository(Shortener::class)->findOneBy(['shortUrl' => $short_url ]);

        if ($url == null) return $this->render('@App/show/index.html.twig');

        return $this->render('@App/api/index.html.twig', [
            'short_url' => $url->getShortUrl(),
            'origin_url' => $url->getOriginUrl(),
            'amount' => $url->getAmount(),
            'id' => $url->getId(),
        ]);
    }

}
