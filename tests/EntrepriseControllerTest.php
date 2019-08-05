<?php

namespace App\Tests;

use App\Entity\Compte;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class EntrepriseControllerTest extends WebTestCase
{
    public function testlisterok1()
    {//lister tous
        $client = static::createClient([],[ 
                'PHP_AUTH_USER' => 'Abdou' ,
                'PHP_AUTH_PW'   => 'azerty'
            ]);
        $client->request('GET', '/entreprises/liste');
        $rep=$client->getResponse();
        var_dump($rep);
        $this->assertSame(200,$client->getResponse()->getStatusCode());
    }

    public function testajoutPartenaireok()
    {//ajouter un partenaire
        $client = static::createClient([],[ 
                'PHP_AUTH_USER' => 'Abdou' ,
                'PHP_AUTH_PW'   => 'azerty'
            ]);
        $random=rand(100000,1500000);
        $client->request('POST', '/partenaires/add',[],[],['CONTENT_TYPE'=>"application/json"],
            '{
                "raisonSociale":"testPart2",
                "ninea": "'.$random.'",
                "adresse": "yoff",
                "emailEntreprise":"entr2@gmail.com",
                "telephoneEntreprise": "'.$random.'",
                "nom":"adminPTest2",
                "username": "adminPTest2",
                "password": "azerty",
                "confirmPassword": "azerty",
                "email":"adminPTest2@gmail.com",
                "telephone": "'.$random.'",
                "nci":"'.$random.'"
            }'
        );

        $rep=$client->getResponse();
        var_dump($rep);
        $this->assertSame(201,$client->getResponse()->getStatusCode());
    }
    public function testajoutPartenaireko1()
    {//ajouter un partenaire erreur raisonSocialee
        $client = static::createClient([],[ 
                'PHP_AUTH_USER' => 'Abdou' ,
                'PHP_AUTH_PW'   => 'azerty'
            ]);
        $random=rand(100000,1500000);
        $client->request('POST', '/partenaires/add',[],[],['CONTENT_TYPE'=>"application/json"],
            '{
                	"raisonSocialee":"testPartenaire",
                    "ninea": "'.$random.'",
                    "adresse": "yoff",
                    "emailEntreprise":"entr2@gmail.com",
                    "telephoneEntreprise": "'.$random.'",
                    "nom":"adminPTest2",
                    "username": "adminPTest2",
                    "password": "azerty",
                    "confirmPassword": "azerty",
                    "email":"adminPTest2@gmail.com",
                    "telephone": "'.$random.'",
                    "nci":"'.$random.'"
            }'
        );

        $rep=$client->getResponse();
        var_dump($rep);
        $this->assertSame(500,$client->getResponse()->getStatusCode());
    }
    public function testajoutPartenaireko1_2()
    {//ajouter un partenaire erreur usernamee
        $client = static::createClient([],[ 
                'PHP_AUTH_USER' => 'Abdou' ,
                'PHP_AUTH_PW'   => 'azerty'
            ]);
        $random=rand(100000,1500000);
        $client->request('POST', '/partenaires/add',[],[],['CONTENT_TYPE'=>"application/json"],
            '{
                	"raisonSociale":"testPartenaire",
                    "ninea": "'.$random.'",
                    "adresse": "yoff",
                    "emailEntreprise":"entr2@gmail.com",
                    "telephoneEntreprise": "'.$random.'",
                    "nom":"adminPTest2",
                    "usernamee": "adminPTest2",
                    "password": "azerty",
                    "confirmPassword": "azerty",
                    "email":"adminPTest2@gmail.com",
                    "telephone": "'.$random.'",
                    "nci":"'.$random.'"
            }'
        );

        $rep=$client->getResponse();
        var_dump($rep);
        $this->assertSame(500,$client->getResponse()->getStatusCode());
    }
    public function testlisterok2()
    {//lister une
        $client = static::createClient([],[ 
                'PHP_AUTH_USER' => 'Abdou' ,
                'PHP_AUTH_PW'   => 'azerty'
            ]);
        $client->request('GET', '/entreprise/2');
        $rep=$client->getResponse();
        var_dump($rep);
        $this->assertSame(200,$client->getResponse()->getStatusCode());
    }
    public function testlisterok3()
    {//l entreprise n'existe pasS!!
        $client = static::createClient([],[ 
                'PHP_AUTH_USER' => 'Abdou' ,
                'PHP_AUTH_PW'   => 'azerty'
            ]);
        $client->request('GET', '/entreprise/1000');
        $rep=$client->getResponse();
        var_dump($rep);
        $this->assertSame(404,$client->getResponse()->getStatusCode());
    }
    public function testInscriptionCaissier()
    {//ajout caissier
        $client = static::createClient([],[ 
                'PHP_AUTH_USER' => 'Abdou' ,
                'PHP_AUTH_PW'   => 'azerty'
            ]);
        $client->request('POST', '/inscription',[],[],['CONTENT_TYPE'=>"application/json"],
            '{
                	"nom":"caissierTest2",
                    "username": "caissierTest2",
                    "password": "azerty",
                    "confirmPassword": "azerty",
                    "email":"caissierTest2@gmail.com",
                    "telephone": 77212222,
                    "nci":"7721222",
                    "profil": 2
            }'
        );

        $rep=$client->getResponse();
        var_dump($rep);
        $this->assertSame(201,$client->getResponse()->getStatusCode());
    }
    public function testDepotok()
    {//depot remplir le numero de compte!!!!!!!!!!!!!!!
        $client = static::createClient([],[ 
                'PHP_AUTH_USER' => 'caissierTest2' ,
                'PHP_AUTH_PW'   => 'azerty'
            ]);
        $client->request('POST', '/nouveau/depot',[],[],['CONTENT_TYPE'=>"application/json"],
            '{
                "compte":"1908 0520 4722",
	            "montant":5000000
            }'
        );

        $rep=$client->getResponse();
        var_dump($rep);
        $this->assertSame(201,$client->getResponse()->getStatusCode());
    }
    public function testDepotko1()
    {//depot dans SAT 
        $client = static::createClient([],[ 
                'PHP_AUTH_USER' => 'caissierTest2' ,
                'PHP_AUTH_PW'   => 'azerty'
            ]);
        $client->request('POST', '/nouveau/depot',[],[],['CONTENT_TYPE'=>"application/json"],
            '{
                "compte":"1910 1409 0043",
	            "montant":5000000
            }'
        );

        $rep=$client->getResponse();
        var_dump($rep);
        $this->assertSame(403,$client->getResponse()->getStatusCode());
    }
    public function testDepotko2()
    {//depot dans compte inexistant
        $client = static::createClient([],[ 
                'PHP_AUTH_USER' => 'caissierTest2' ,
                'PHP_AUTH_PW'   => 'azerty'
            ]);
        $client->request('POST', '/nouveau/depot',[],[],['CONTENT_TYPE'=>"application/json"],
            '{
                "compte":"123456789",
	            "montant":5000000
            }'
        );

        $rep=$client->getResponse();
        var_dump($rep);
        $this->assertSame(404,$client->getResponse()->getStatusCode());
    }
    public function testDepotko3()
    {//depot dans compte inexistant remplir le numero de compte!!!!!!!!!!!!!!!
        $client = static::createClient([],[ 
                'PHP_AUTH_USER' => 'caissierTest2' ,
                'PHP_AUTH_PW'   => 'azerty'
            ]);
        $client->request('POST', '/nouveau/depot',[],[],['CONTENT_TYPE'=>"application/json"],
            '{
                "compte":"1908 0520 4722",
	            "montant":5000
            }'
        );
        $rep=$client->getResponse();
        var_dump($rep);
        $this->assertSame(200,$client->getResponse()->getStatusCode());
    }
    public function testbloquerok1()
    {//bloquer l'entreprise 2
        $client = static::createClient([],[ 
                'PHP_AUTH_USER' => 'Abdou' ,
                'PHP_AUTH_PW'   => 'azerty'
            ]);
        $client->request('GET', '/bloque/entreprises/2');
        $rep=$client->getResponse();
        var_dump($rep);
        $this->assertSame(200,$client->getResponse()->getStatusCode());
    }
    public function testbloquerok2()
    {//debloquer l'entreprise 2
        $client = static::createClient([],[ 
                'PHP_AUTH_USER' => 'Abdou' ,
                'PHP_AUTH_PW'   => 'azerty'
            ]);
        $client->request('GET', '/bloque/entreprises/2');
        $rep=$client->getResponse();
        var_dump($rep);
        $this->assertSame(200,$client->getResponse()->getStatusCode());
    }
    public function testbloquerko1()
    {//bloquer entreprise inexistante
        $client = static::createClient([],[ 
                'PHP_AUTH_USER' => 'Abdou' ,
                'PHP_AUTH_PW'   => 'azerty'
            ]);
        $client->request('GET', '/bloque/entreprises/1000');
        $rep=$client->getResponse();
        var_dump($rep);
        $this->assertSame(404,$client->getResponse()->getStatusCode());
    }
    public function testbloquerko2()
    {//bloquer SAT
        $client = static::createClient([],[ 
                'PHP_AUTH_USER' => 'Abdou' ,
                'PHP_AUTH_PW'   => 'azerty'
            ]);
        $client->request('GET', '/bloque/entreprises/1');
        $rep=$client->getResponse();
        var_dump($rep);
        $this->assertSame(403,$client->getResponse()->getStatusCode());
    }
    
}
