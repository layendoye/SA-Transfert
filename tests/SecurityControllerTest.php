<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testInscriptionUtilisateurok1()
    {
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
                    "nci":"7721",
                    "profil": 2
            }'
        );

        $rep=$client->getResponse();
        $this->assertSame(201,$client->getResponse()->getStatusCode());
    }
}
    /*
        Pour le créer faire : php bin/console make:functional-test
    */