<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testInscriptionUtilisateurok1()
    {//ajout caissier
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
        var_dump($rep);
        $this->assertSame(201,$client->getResponse()->getStatusCode());
    }
    public function testUpdateUtilisateurok1()
    {//update abdou
        $client = static::createClient([],[ 
                'PHP_AUTH_USER' => 'Abdou' ,
                'PHP_AUTH_PW'   => 'azerty'
            ]);
        $client->request('POST', '/user/update/1',[],[],['CONTENT_TYPE'=>"application/json"],
            '{
                	"nom":"Abdoulaye Ndoye",
                    "username": "abdou",
                    "password": "azerty",
                    "confirmPassword": "azerty",
                    "email":"layendoyesn1@gmail.com",
                    "telephone": "77 105 0106",
                    "nci":"362388369",
                    "profil": 1
            }'
        );

        $rep=$client->getResponse();
        var_dump($rep);
        $this->assertSame(200,$client->getResponse()->getStatusCode());
    }
    public function testUpdateUtilisateurko1()
    {//update erreur username
        $client = static::createClient([],[ 
                'PHP_AUTH_USER' => 'Abdou' ,
                'PHP_AUTH_PW'   => 'azerty'
            ]);
        $client->request('POST', '/user/update/1',[],[],['CONTENT_TYPE'=>"application/json"],
            '{
                	"nom":"Abdoulaye Ndoye",
                    "usernamee": "abdou",
                    "password": "azerty",
                    "confirmPassword": "azerty",
                    "email":"layendoyesn1@gmail.com",
                    "telephone": "77 105 0106",
                    "nci":"362388369",
                    "profil": 1
            }'
        );

        $rep=$client->getResponse();
        var_dump($rep);
        $this->assertSame(500,$client->getResponse()->getStatusCode());
    }
    public function testUpdateUtilisateurko2()
    {//update user n existe pas
        $client = static::createClient([],[ 
                'PHP_AUTH_USER' => 'Abdou' ,
                'PHP_AUTH_PW'   => 'azerty'
            ]);
        $client->request('POST', '/user/update/1000',[],[],['CONTENT_TYPE'=>"application/json"],
            '{
                	"nom":"Abdoulaye Ndoye",
                    "username": "abdou",
                    "password": "azerty",
                    "confirmPassword": "azerty",
                    "email":"layendoyesn1@gmail.com",
                    "telephone": "77 105 0106",
                    "nci":"362388369",
                    "profil": 1
            }'
        );

        $rep=$client->getResponse();
        var_dump($rep);
        $this->assertSame(404,$client->getResponse()->getStatusCode());
    }
    public function testInscriptionUtilisateurko1()
    {//le profil n existe pas
        $client = static::createClient([],[ 
                'PHP_AUTH_USER' => 'Abdou' ,
                'PHP_AUTH_PW'   => 'azerty'
            ]);
        $client->request('POST', '/inscription',[],[],['CONTENT_TYPE'=>"application/json"],
            '{
                	"nom":"Test",
                    "username": "Test",
                    "password": "azerty",
                    "confirmPassword": "azerty",
                    "email":"Test@gmail.com",
                    "telephone": 7722,
                    "nci":"7722",
                    "profil": 8
            }'
        );

        $rep=$client->getResponse();
        var_dump($rep);
        $this->assertSame(404,$client->getResponse()->getStatusCode());
    }
    public function testInscriptionUtilisateurko2()
    {//le super admin ajout admin simple
        $client = static::createClient([],[ 
                'PHP_AUTH_USER' => 'Abdou' ,
                'PHP_AUTH_PW'   => 'azerty'
            ]);
        $client->request('POST', '/inscription',[],[],['CONTENT_TYPE'=>"application/json"],
            '{
                	"nom":"Test",
                    "username": "Test",
                    "password": "azerty",
                    "confirmPassword": "azerty",
                    "email":"Test@gmail.com",
                    "telephone": 77223,
                    "nci":"77223",
                    "profil": 4
            }'
        );

        $rep=$client->getResponse();
        var_dump($rep);
        $this->assertSame(403,$client->getResponse()->getStatusCode());
    }
    public function testajoutPartenaireok()
    {//ajouter un partenaire et un admin principal on en a besoin pour la suite 
        $client = static::createClient([],[ 
                'PHP_AUTH_USER' => 'Abdou' ,
                'PHP_AUTH_PW'   => 'azerty'
            ]);
        $random=rand(100000,1500000);
        $client->request('POST', '/partenaires/add',[],[],['CONTENT_TYPE'=>"application/json"],
            '{
                "raisonSociale":"testPart",
                "ninea": "'.$random.'",
                "adresse": "yoff",
                "emailEntreprise":"maimou@gmail.com",
                "telephoneEntreprise": "'.$random.'",
                "nom":"adminPTest1",
                "username": "adminPTest1",
                "password": "azerty",
                "confirmPassword": "azerty",
                "email":"adminPTest1@gmail.com",
                "telephone": "'.$random.'",
                "nci":"'.$random.'"
            }'
        );
        $rep=$client->getResponse();
        var_dump($rep);
        $this->assertSame(201,$client->getResponse()->getStatusCode());
    }
    public function testInscriptionUtilisateurko3()
    {//erreur form
        $client = static::createClient([],[ 
                'PHP_AUTH_USER' => 'adminPTest1' ,
                'PHP_AUTH_PW'   => 'azerty'
            ]);
        $client->request('POST', '/inscription',[],[],['CONTENT_TYPE'=>"application/json"],
            '{
                	"nomm":"Test",
                    "username": "Test",
                    "password": "azerty",
                    "confirmPassword": "azerty",
                    "email":"Test@gmail.com",
                    "telephone": 7722,
                    "nci":"7722",
                    "profil": 5
            }'
        );

        $rep=$client->getResponse();
        var_dump($rep);
        $this->assertSame(200,$client->getResponse()->getStatusCode());
    }

}
    /*
        Pour le créer faire : php bin/console make:functional-test
    */