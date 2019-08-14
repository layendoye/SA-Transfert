<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Routing\Annotation\Route;
// Include Dompdf required namespaces
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class PdfController extends Controller
{
    /**
    * @Route("/pdf/{action}")
    * @IsGranted({"ROLE_Super-admin"}, statusCode=403, message="Vous n'avez pas accès à cette page !")
    */
    public function pdf($action)
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        if($action=='contrat'){
            // Retrieve the HTML generated in our twig file
            $html = $this->renderView('test/index.html.twig', [
                'title' => "Welcome to our PDF Test"
            ]);
        }
        elseif($action=='recu'){
            $html = $this->renderView('recu/index.html.twig', [
                'title' => "Welcome to our PDF Test"
            ]);
        }
        else{
            throw new HttpException(404,'Resource non trouvée !!');
        }
        
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => true
        ]);
    }
}