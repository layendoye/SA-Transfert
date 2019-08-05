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
    public function testInscriptionUtilisateurko4()
    {//404 car il n y pas de compte definit
        $client = static::createClient([],[ 
                'PHP_AUTH_USER' => 'adminPTest1' ,
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
                    "profil": 5
            }'
        );

        $rep=$client->getResponse();
        var_dump($rep);
        $this->assertSame(404,$client->getResponse()->getStatusCode());
    }
    public function testInscriptionUtilisateurko5()
    {//le compte n'est pas celui de l'entreprise
        $client = static::createClient([],[ 
                'PHP_AUTH_USER' => 'adminPTest1' ,
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
                    "compte":1,
                    "profil": 5
            }'
        );

        $rep=$client->getResponse();
        var_dump($rep);
        $this->assertSame(403,$client->getResponse()->getStatusCode());
    }
    public function testInscriptionUtilisateurko6()
    {//impossible de lui assigner un compte
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
                    "compte":1,
                    "profil": 2
            }'
        );

        $rep=$client->getResponse();
        var_dump($rep);
        $this->assertSame(403,$client->getResponse()->getStatusCode());
    }
    public function testInscriptionUtilisateurko7()
    {//compte inexistant
        $client = static::createClient([],[ 
                'PHP_AUTH_USER' => 'adminPTest1' ,
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
                    "compte":1000,
                    "profil": 5
            }'
        );

        $rep=$client->getResponse();
        var_dump($rep);
        $this->assertSame(404,$client->getResponse()->getStatusCode());
    }
    public function testInscriptionUtilisateurko8()
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
                    "compte":2,
                    "profil": 5
            }'
        );

        $rep=$client->getResponse();
        var_dump($rep);
        $this->assertSame(200,$client->getResponse()->getStatusCode());
    }
    public function testInscriptionUtilisateurko9()
    {//impossible de lui assigner un compte
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
                    "compte":1,
                    "profil": 3
            }'
        );

        $rep=$client->getResponse();
        var_dump($rep);
        $this->assertSame(403,$client->getResponse()->getStatusCode());
    }
}
    /*
        Pour le créer faire : php bin/console make:functional-test
    */