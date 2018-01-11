<?php

namespace AppBundle\Services;

use AppBundle\Entity\Shortener;
use Doctrine\ORM\EntityManager;

class ShortenerServices
{

    public function __construct()
    {

    }


    public function checkResponse($origin_url)
    {
        // get response code
        $ch = curl_init($origin_url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $retcode;
    }

    public function checkExistUrl($short_url, EntityManager $entityManager)
    {
        //check short url is exist
       return $exist_short_url = $entityManager->getRepository(Shortener::class)->findOneBy([
            'shortUrl' => $short_url,
        ]);
    }

}