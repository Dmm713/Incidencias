<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\FormularioRegistrarType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/', name: 'index')]
    public function index(){
        return $this->redirectToRoute('app_login');
    }
    
    #[Route('/registrar', name: 'registrarUsuario')]
public function register(Request $request, EntityManagerInterface $entityManager,AuthorizationCheckerInterface $authorizationChecker): Response
    {
        
        $user = new User();
        $form = $this->createForm(FormularioRegistrarType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $fotoFile */
            $fotoFile = $form['foto']->getData();
            $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);
            if ($fotoFile) {
                $newFilename = md5(uniqid()) . '.' . $fotoFile->guessExtension();

                // Move the file to the desired directory
                $fotoFile->move(
                    "imagenes/",
                    $newFilename
                );

                // Update the User entity with the filename
                $user->setFoto($newFilename);
                $user->setRol("ROLE_USER");
            }
            $entityManager->persist($user);

            // actually executes the queries (i.e. the INSERT query)
            $entityManager->flush();
            // ... (Existing user creation logic)
            $this->addFlash('ss', '¡Se ha registrado correctamente!');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/index.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
