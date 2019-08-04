<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EventSubscriberTest extends WebTestCase
{
    public function testeventSub1()
    {//test eventSubscriber not found
        $client = static::createClient([],[ 
                'PHP_AUTH_USER' => 'Abdou' ,
                'PHP_AUTH_PW'   => 'azerty'
            ]);
        $client->request('GET', '/lien');
        $rep=$client->getResponse();
        var_dump($rep);
        $this->assertSame(404,$client->getResponse()->getStatusCode());
    }
    public function testeventSub2()
    {//erreur 500
        $client = static::createClient([],[ 
                'PHP_AUTH_USER' => 'Abdou' ,
                'PHP_AUTH_PW'   => 'azerty'
            ]);
        $client->request('POST', '/inscription',[],[],['CONTENT_TYPE'=>"application/json"],
            '{
                	"nom":"caissierTest1",
                    "username": "caissierTest1",
                    "password": "azerty",
                    "confirmPassword": "azerty",
                    "email":"caissierTest1@gmail.com",
                    "telephone": 7721,
                    "nci":"7721"
            }'
        );

        $rep=$client->getResponse();
        var_dump($rep);
        $this->assertSame(500,$client->getResponse()->getStatusCode());
    }
}
