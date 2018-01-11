<?php

namespace AppBundle\Services;

use AppBundle\Entity\Shortener;
use Doctrine\ORM\EntityManager;

class ShortenerServices
{

    protected $origin_url;
    protected $short_url;
    protected $em;
    protected $obj;
    protected $form;


    public function __construct($origin_url, $short_url, EntityManager $entityManager)
    {
        $this->origin_url = $origin_url;
        $this->short_url = $short_url;
        $this->em = $entityManager;
//        $this->obj = $obj;
//        $this->form = $form;
    }


    public function checkResponse()
    {
        // get response code
        $ch = curl_init($this->origin_url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $retcode;
    }

    public function checkExistUrl()
    {
        //check short url is exist
       return $exist_short_url = $this->em->getRepository(Shortener::class)->findOneBy([
            'shortUrl' => $this->short_url,
        ]);
    }

}