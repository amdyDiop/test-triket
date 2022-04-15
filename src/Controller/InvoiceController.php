<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\InvoiceLines;
use App\Form\InvoiceFormType;
use App\Form\InvoicelinesFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InvoiceController extends AbstractController
{
    #[Route('/', name: 'app_invoice')]
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $tva = 18;
        $invoice = new Invoice();
        $invoiceLine = new Invoicelines();
        $invoice->getInvoicelines()->add($invoiceLine);
        $form = $this->createForm(InvoiceFormType::class, $invoice);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $lines = $invoice->getInvoicelines();
            $last_id_record =  $em->getRepository(Invoice::class)->findOneBy([], ['id' => 'desc']);
            $invoice->setInvoiceNumber($last_id_record ?  (int)$last_id_record->getId() + 1 : 1);
            $em->persist($invoice);
            foreach($lines as $singleLine ){
                $amount_without_vat = $singleLine->getQuantity() * $singleLine->getAmount();
                $amount_vat = ($amount_without_vat * $tva) / 100;
                $total = $amount_vat + $amount_without_vat;
                $singleLine->setTotalWithVat($total);
                $singleLine->setVatAmount($amount_vat);
                $singleLine->setInvoice($invoice);
                $em->persist($singleLine);
            }

            $em->flush();
            unset($form);
            unset($invoice);
            unset($line);
            $invoice = new Invoice();
            $line = new Invoicelines();
            $invoice->getInvoiceLines()->add($line);
            $form = $this->createForm(InvoiceFormType::class, $invoice);
        }

        return $this->render('invoice/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
