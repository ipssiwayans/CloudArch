<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Intl\Countries;

class AccessController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(): Response
    {
        return $this->render('access/login.html.twig', [
        ]);
    }

    #[Route('/signup', name: 'app_signup')]
    public function signup(Request $request,EntityManagerInterface $entityManager,UserPasswordHasherInterface $passwordHasher): Response
    {
        $countries = Countries::getNames();
        if($request->isMethod('post')){
            $user = new User();

            $firstname = $request->request->get('firstname');
            if (null === $firstname) {
                throw new \Exception('Firstname is required');
            }
            $user->setFirstname($firstname);

            $lastname = $request->request->get('lastname');
            if (null === $lastname) {
                throw new \Exception('Lastname is required');
            }
            $user->setLastname($lastname);

            $email = $request->request->get('email');
            if (null === $email) {
                throw new \Exception('Email is required');
            }
            $user->setEmail($email);

            $password = $request->request->get('password');
            if (null === $password) {
                throw new \Exception('Password is required');
            }
            $hashedPassword = $passwordHasher->hashPassword($user, $password);
            $user->setPassword($hashedPassword);

            $streetNumber = $request->request->get('streetnumber');
            if (null === $streetNumber) {
                throw new \Exception('Street number is required');
            }
            $user->setStreetNumber($streetNumber);

            $streetAddress = $request->request->get('streetname');
            if (null === $streetAddress) {
                throw new \Exception('Street address is required');
            }
            $user->setStreetAddress($streetAddress);

            $zipCode = $request->request->get('zipcode');
            if (null === $zipCode) {
                throw new \Exception('Zip code is required');
            }
            $user->setPostalCode($zipCode);

            $city = $request->request->get('city');
            if (null === $city) {
                throw new \Exception('City is required');
            }
            $user->setCity($city);

            $country = $request->request->get('country');
            if (null === $country) {
                throw new \Exception('Country is required');
            }
            $user->setCountry($country);

            $user->setTotalStorage(20);
            $user->setRegistrationDate(new \DateTime('now'));

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }
        return $this->render('access/sign_up.html.twig', [
            'countries' => $countries,
        ]);
    }
}
