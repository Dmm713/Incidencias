<?php

namespace App\Controller;

use App\Entity\Cliente;
use App\Entity\Incidencia;
use App\Form\FormularioIncidenciaSoloClienteType;
use App\Form\FormularioIncidenciaType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class IncienciaController extends AbstractController
{
    #[IsGranted('ROLE_USER', message: 'You are not allowed to access the admin dashboard.')]
    #[Route('/inciencia', name: 'app_inciencia')]
    public function index(): Response
    {
        return $this->render('inciencia/index.html.twig', [
            'controller_name' => 'IncienciaController',
        ]);
    }
    #[IsGranted('ROLE_USER', message: 'You are not allowed to access the admin dashboard.')]
    #[Route('/inciencia/nueva', name: 'nuevaIncidencia')]
    public function nueva(Request $request, EntityManagerInterface $entityManager): Response
    {
        $incidencia = new Incidencia();
        $form = $this->createForm(FormularioIncidenciaType::class, $incidencia);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $incidencia->setUser($this->getUser());
            $entityManager->persist($incidencia);
            $entityManager->flush();

            $this->addFlash('success', 'Incidencia creada con éxito.');

            return $this->redirectToRoute('incidencias');
        }

        return $this->render('incidencia/nueva.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[IsGranted('ROLE_USER', message: 'You are not allowed to access the admin dashboard.')]
   #[Route('/incidencia/nuevaPorCliente/{id}', name: 'nuevaIncidenciaPorCliente')]
   public function nuevaIncidenciaCliente(Request $request, $id ,EntityManagerInterface $entityManager): Response
   {
       $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
       $incidencia = new Incidencia();
       $form = $this->createForm(FormularioIncidenciaSoloClienteType::class, $incidencia);

       $form->handleRequest($request);

       if ($form->isSubmitted() && $form->isValid()) {
           $cliente = $entityManager->getRepository(Cliente::class)->find($id);
           $incidencia->setCliente($cliente);
          
           $incidencia->setUser($this->getUser());
           $entityManager->persist($incidencia);
           $entityManager->flush();

           $this->addFlash('success', 'Incidencia creada con éxito.');

           return $this->redirectToRoute('verIncidenciasCliente',['id' => $incidencia->getCliente()->getId() ]);
       }

       return $this->render('incidencia/nueva.html.twig', [
           'form' => $form->createView(),
       ]);
   }

    #[IsGranted('ROLE_USER', message: 'You are not allowed to access the admin dashboard.')]
    #[Route(path: '/incidencia/todas', name: 'incidencias')]
    public function verIncidencias(EntityManagerInterface $entityManager): Response
    {
        $incidencias = $entityManager->getRepository(Incidencia::class)->findAll();

        return $this->render('incidencia/todasLasIncidencias.html.twig', [
            'incidencias' => $incidencias,
        ]);
    }
    #[IsGranted('ROLE_USER', message: 'You are not allowed to access the admin dashboard.')]
    #[Route(path: '/incidencia/eliminar/{id}', name: 'eliminarIncidencia')]
    public function eliminar(Request $request, $id, EntityManagerInterface $entityManager): Response
{
    $incidencia = $entityManager->getRepository(Incidencia::class)->find($id);
    
    if (!$incidencia) {
        throw $this->createNotFoundException('No se encontró la Incidencia con ID '.$id);
    }

    $entityManager->remove($incidencia);
    $entityManager->flush();

    // Mensaje de confirmación
    $this->addFlash('success', 'Incidencia eliminada con éxito.');

    return $this->redirectToRoute('incidencias');
}

#[IsGranted('ROLE_USER', message: 'You are not allowed to access the admin dashboard.')]
    #[Route(path: '/incidencia/eliminarCliente/{id}', name: 'eliminarIncidenciaPorCliente')]
    public function eliminarCliente(Request $request, $id, EntityManagerInterface $entityManager): Response
{
    $incidencia = $entityManager->getRepository(Incidencia::class)->find($id);
    $clienteId= $incidencia->getCliente()->getId();
    if (!$incidencia) {
        throw $this->createNotFoundException('No se encontró la Incidencia con ID '.$id);
    }

    $entityManager->remove($incidencia);
    $entityManager->flush();

    // Mensaje de confirmación
    $this->addFlash('success', 'Incidencia eliminada con éxito.');

    return $this->redirectToRoute('verIncidenciasCliente', ['id'=>$clienteId]);
}
#[IsGranted('ROLE_USER', message: 'You are not allowed to access the admin dashboard.')]
#[Route(path: '/incidencia/editar/{id}', name: 'editarIncidencia')]
public function editar(Request $request, Incidencia $incidencia, EntityManagerInterface $entityManager): Response
{
    $form = $this->createForm(FormularioIncidenciaType::class, $incidencia);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $incidencia->setUser($this->getUser());
        $entityManager->persist($incidencia);
        $entityManager->flush();

        $this->addFlash('success', 'Incidencia actualizado con éxito.');

        return $this->redirectToRoute('incidencias');
    }

    return $this->render('incidencia/editar.html.twig', [
        'form' => $form->createView(),
    ]);
}

#[IsGranted('ROLE_USER', message: 'You are not allowed to access the admin dashboard.')]
#[Route(path: '/incidencia/editarPorCliente/{id}', name: 'editarPorCliente')]
public function editarPorCliente(Request $request, Incidencia $incidencia, EntityManagerInterface $entityManager): Response
{
    $form = $this->createForm(FormularioIncidenciaType::class, $incidencia);

    $form->handleRequest($request);
    $clienteId= $incidencia->getCliente()->getId();
    if ($form->isSubmitted() && $form->isValid()) {
        $incidencia->setUser($this->getUser());
        $entityManager->persist($incidencia);
        $entityManager->flush();

        $this->addFlash('success', 'Incidencia actualizado con éxito.');

        return $this->redirectToRoute('verIncidenciasCliente', ['id'=>$clienteId]);
    }

    return $this->render('incidencia/editar.html.twig', [
        'form' => $form->createView(),
    ]);
}
}
