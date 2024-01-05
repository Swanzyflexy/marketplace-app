<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\EmailVerifier;
use App\Security\LoginAuthenticator;
use App\Service\CountryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    public function __construct(
        private EmailVerifier $emailVerifier,
        private UserPasswordHasherInterface $passwordHasher,
        private UserAuthenticatorInterface $userAuthenticator,
        private LoginAuthenticator $authenticator,
        private EntityManagerInterface $em,
        private ValidatorInterface $validator,
        private CountryService $countryService
    ) {
        // $this->emailVerifier = $emailVerifier;
    }

    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(Request $request): Response
    {
        // dd($this->getParameter('kernel.project_dir'));
        // Parse JSON data from the request body
        $user = new User();

        if ($request->isMethod('POST')) {
            $user->setFullName($request->request->get('full_name'));
            $user->setUsername($request->request->get('username'));
            $user->setEmail($request->request->get('email'));
            $user->setPhone($request->request->get('phone'));
            // You may adjust the default status as needed
            $user->setAddress($request->request->get('address'));
            $user->setCity($request->request->get('city'));
            $user->setState($request->request->get('state'));
            $user->setZipcode($request->request->get('zipcode'));
            $user->setCountry($request->request->get('country'));
            $user->setPassword($this->passwordHasher->hashPassword(
                $user,
                $request->request->get('password')
            ));
            // You may adjust the default roles as needed
            $user->setStatus('Inactive');
            $user->setRoles(['ROLE_USER']);
            $user->setIsVerified(false);
            $user->setCreatedAt();
            $user->setUpdatedAt();
            $profileImage = $request->files->get('profile_image');

            if ($profileImage instanceof UploadedFile) {
                $uploadDirectory = $this->getParameter('kernel.project_dir').'/public/uploads/profile_images';

                // Create the directory if it doesn't exist
                if (!is_dir($uploadDirectory)) {
                    mkdir($uploadDirectory, 0777, true);
                }

                $originalFilename = pathinfo($profileImage->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$profileImage->guessExtension();

                try {
                    $profileImage->move(
                        $uploadDirectory,
                        $newFilename
                    );

                    // Save only the image URL to the database
                    $profileImageUrl = '/uploads/profile_images/'.$newFilename;
                    $user->setProfileImage($profileImageUrl);
                } catch (FileException $e) {
                    // Handle file upload error
                    $this->addFlash('error', 'System Encountered Error while Uploading Profile Image!');
                    $this->addFlash('error', 'Your profile can\'t be created!');

                    return $this->redirectToRoute('app_register_new');
                }
            }

            $errors = $this->validator->validate($user);

            if (count($errors) > 0) {
                $validationErrors = [];
                foreach ($errors as $error) {
                    $propertyPath = $error->getPropertyPath();
                    $validationErrors[$propertyPath] = $error->getMessage();
                }

                $request->attributes->set('_validation_errors', $validationErrors);
                $request->attributes->set('_old_values', $request->request->all());

                return $this->render('security/registration/sign-up.html.twig', [
                    '_validation_errors' => $request->attributes->get('_validation_errors'),
                    '_old_values' => $request->attributes->get('_old_values'),
            ]);
            }

            $activitylog = $user->getFullName().' Created an Account '.$user->getUsername().' at:'.$user->getCreatedAt()->format('l dS F Y');
            $user->logActivity((string) $activitylog);
            $this->em->persist($user);
            $this->em->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                ->from(new Address('no-reply@simplemarketplace.app', 'Simple Market Place App'))
                ->to($user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('security/registration/confirmation_email.html.twig')->context(['user' => $user, 'site_name' => $this->getParameter('site_name')]));

            // do anything else you need here, like send an email

            return $this->userAuthenticator->authenticateUser(
                $user,
                $this->authenticator,
                $request
            );
        }

        return $this->render('security/registration/sign-up.html.twig', [
            '_validation_errors' => $request->attributes->get('_validation_errors'),
            '_old_values' => $request->attributes->get('_old_values')]);
    }

    #[Route('/verify/email', name: 'app_verify_email', methods: ['GET', 'POST'])]
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }
        $user = $this->getUser();
        $user->setStatus('active');
        $activitylog = $user->getFullName().' Verified Account at:'.$user->getCreatedAt()->format('l dS F Y');
        $user->logActivity((string) $activitylog);
        $this->em->persist($user);
        $this->em->flush();

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_dashboard');
    }

    #[Route('/reset-password', name: 'app_pass_reset')]
    public function resetPassword(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        return $this->render('security/registration/reset_password.html.twig', [
            'token' => $request->get('token'),
            'email' => $request->get('email'),
        ]);
    }
}