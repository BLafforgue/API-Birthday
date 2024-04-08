<?php

namespace App\Controller;

use App\Entity\Birthday;
use App\Repository\BirthdayRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class BirthdayController extends AbstractController
{
    #[Route('/birthday', name: 'app_birthday')]
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome',
        ]);
    }

    # Récupérer toutes les dates
    #[Route('/birthday', name: 'app_birthday', methods: ['GET'])]
    public function getBirthdayList(BirthdayRepository $birthdayRepository, SerializerInterface $serializer): JsonResponse
    {
        $birthdayList = $birthdayRepository->findAll();
        $jsonBirthdayList = $serializer->serialize($birthdayList, 'json');
        return new JsonResponse($jsonBirthdayList, Response::HTTP_OK, [], true);
    }

    # Récupérer une date en particulier
    #[Route('/birthday/{id}', name: 'detailBirthday', methods: ['GET'])]
    public function getDetailBirthday(Birthday $birthday, SerializerInterface $serializer): JsonResponse {

        $jsonBirthday = $serializer->serialize($birthday, 'json');
        return new JsonResponse($jsonBirthday, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    # Supprimer une date
    #[Route('/birthday/{id}', name: 'deleteBirthday', methods: ['DELETE'])]
    public function deleteBook(Birthday $birthday, EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse
    {
        $jsonBirthday = $serializer->serialize($birthday, 'json');

        $em->remove($birthday);
        $em->flush();

        return new JsonResponse($jsonBirthday, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    # Créer une date
    #[Route('/birthday', name:"createBirthday", methods: ['POST'])]
    public function createBook(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator): JsonResponse
    {

        $birthday = $serializer->deserialize($request->getContent(), Birthday::class, 'json');
        $em->persist($birthday);
        $em->flush();

        $jsonBirthday = $serializer->serialize($birthday, 'json');

        $location = $urlGenerator->generate('detailBirthday', ['id' => $birthday->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonBirthday, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    # Mettre à jour une date
    #[Route('/birthday/{id}', name:"updateBook", methods:['PUT'])]

    public function updateBirthday(Request $request, SerializerInterface $serializer, Birthday $birthday, EntityManagerInterface $em): Response
    {
        $updatedBirthday = $serializer->deserialize($request->getContent(),
            Birthday::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $birthday]);
        $content = $request->toArray();
        if ($content['name']) {
            $updatedBirthday->setName($content['name']);
        } elseif ($content['birthday']) {
            $updatedBirthday->setBirthday($content['birthday']->format('Y-m-d'));
        }

        $em->persist($updatedBirthday);
        $em->flush();

        $jsonBirthday = $serializer->serialize($updatedBirthday, 'json');

        return new Response($jsonBirthday, Response::HTTP_OK);
    }
}
