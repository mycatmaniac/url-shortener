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

            $shortener_service = new ShortenerServices();
            $retcode = $shortener_service->checkResponse($data->getOriginUrl());
            $exist_short_url = $shortener_service->checkExistUrl($data->getShortUrl(), $em);

            // check response code
            if (in_array($retcode, [404,0])) {
                $form->addError(new FormError('Link broken, check available.'));
            } else {

                // set short url
                if ($exist_short_url == null) {
                    $url->setShortUrl($data->getShortUrl());
                } else {
                    $url->setShortUrl(null);

                    // persist to get an id
                    $em->persist($url);
                    $em->flush();

                    $url->setShortUrl(base64_encode($url->getId()));
                }

                $em->persist($url);
                $em->flush();

                return $this->render('@App/homepage/index.html.twig', [
                    'form' => $form->createView(),
                    'new_url' => $url->getShortUrl(),
                    'amount' => $url->getAmount(),
                ]);
            }
        }

        return $this->render('@App/homepage/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function showAction($short_url)
    {

        $em = $this->getDoctrine()->getManager();

        $url = $em->getRepository(Shortener::class)->findOneBy(['shortUrl' => $short_url ]);

        if ($url == null){
               return $this->render('@App/show/index.html.twig',[],new Response('', 404));
        } else {
            $amount = $url->getAmount();
            $url->setAmount($amount + 1);

            $em->persist($url);
            $em->flush();
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
    public function apiCreateAction(Request $request)
    {

        // get GET parameters
        $origin_url = $request->query->get('originUrl');
        $short_url = $request->query->get('shortUrl');

        if ($origin_url == null) return 'Set GET parameters "originUrl" and "shortUrl" (shortUrl optional)';

        $em = $this->getDoctrine()->getManager();

        $shortener_service = new ShortenerServices();
        $retcode = $shortener_service->checkResponse($origin_url);
        ($short_url == null )? $exist_short_url = null: $exist_short_url = $shortener_service->checkExistUrl($short_url, $em);

        if (in_array($retcode, [404,0])) return 'Link broken, check available';

        $url = new Shortener();
        $url->setOriginUrl($origin_url);
        $url->setAmount(0);


        if ($exist_short_url == null) {
            // persist to get an id
            $em->persist($url);
            $em->flush();

            $url->setShortUrl(base64_encode($url->getId()));
        } else {
            $url->setShortUrl($short_url);
        }


        $em->persist($url);
        $em->flush();

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
