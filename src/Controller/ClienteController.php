<?php

namespace App\Controller;

use App\Entity\Cliente;
use App\Form\FormularioClienteType;
use App\Repository\ClienteRepository;
use App\Repository\IncidenciaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ClienteController extends AbstractController
{
    #[IsGranted('ROLE_USER', message: 'You are not allowed to access the admin dashboard.')]
    #[Route(path: '/cliente/nuevo', name: 'nuevoCliente')]
    public function nuevo(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cliente = new Cliente();
        $form = $this->createForm(FormularioClienteType::class, $cliente);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($cliente);
            $entityManager->flush();

            // Redirige o muestra un mensaje de éxito
            return $this->redirectToRoute('clientes');
        }

        return $this->render('cliente/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[IsGranted('ROLE_USER', message: 'You are not allowed to access the admin dashboard.')]
    #[Route(path: '/cliente/todos', name: 'clientes')]
    public function verClientes(EntityManagerInterface $entityManager): Response
    {
        $clientes = $entityManager->getRepository(Cliente::class)->findAll();

        return $this->render('cliente/todosLosClientes.html.twig', [
            'clientes' => $clientes,
        ]);
    }
    #[IsGranted('ROLE_USER', message: 'You are not allowed to access the admin dashboard.')]
    #[Route(path: '/cliente/eliminar/{id}', name: 'eliminarCliente')]
    public function eliminar(Request $request, $id, EntityManagerInterface $entityManager): Response
{
    $cliente = $entityManager->getRepository(Cliente::class)->find($id);

    if (!$cliente) {
        throw $this->createNotFoundException('No se encontró el cliente con ID '.$id);
    }

    $entityManager->remove($cliente);
    $entityManager->flush();

    // Mensaje de confirmación
    $this->addFlash('success', 'Cliente eliminado con éxito.');

    return $this->redirectToRoute('clientes');
}
#[IsGranted('ROLE_USER', message: 'You are not allowed to access the admin dashboard.')]
#[Route(path: '/cliente/verIncidencias/{id}', name: 'verIncidenciasCliente')]
public function incidenciasPorCliente(int $id, IncidenciaRepository $incidenciaRepository, ClienteRepository $clienteRepository): Response
{
    $cliente = $clienteRepository->find($id);

    if (!$cliente) {
        // Opción para manejar el caso de que el cliente no se encuentre
        $this->addFlash('error', 'El cliente no se encontró.');
        return $this->redirectToRoute('clientes'); // Asegúrate de reemplazar por la ruta correcta
    }

    $incidencias = $incidenciaRepository->findBy(['cliente' => $cliente]);

    return $this->render('incidencia/incidenciasPorCliente.html.twig', [
        'incidencias' => $incidencias,
        'cliente' => $cliente,
    ]);
}
}
