<?php

namespace App\Controller;

use App\Entity\Profession;
use App\Entity\User;
use App\Form\ProfessionType;
use App\Form\UserType;
use App\Repository\ProfessionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profession", name="profession_")
 */
class ProfessionController extends AbstractController
{

    private $professionRepository;
    private $em;
    private $userRepository;

    public function __construct(ProfessionRepository $professionRepository, EntityManagerInterface $em, UserRepository $userRepository)
    {
        $this->professionRepository = $professionRepository;
        $this->userRepository = $userRepository;
        $this->em = $em;
    }

    /**
     * @Route("", name="home")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function home(Request $request, PaginatorInterface $paginator )
    {
        $queryBuilder = $this->professionRepository->Paginator();

        $profPagination = $paginator->paginate($queryBuilder,$request->query->getInt('page', 1),10);

        return $this->render('Profession/listProfession.html.twig', ['listProfession' => $profPagination]);
    }

    /**
     * @Route("/delete/{id}", name="delete", requirements={"id" = "\d+"})
     * @param int $id
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function delete($id, Request $request, PaginatorInterface $paginator, EntityManagerInterface $em): Response
    {
        $prof = $this->professionRepository->find($id);

        $em->remove($prof);
        $em->flush();

        $queryBuilder = $this->professionRepository->Paginator();

        $profPagination = $paginator->paginate($queryBuilder,$request->query->getInt('page', 1),10);

        return $this->render('Profession/listProfession.html.twig', ['listProfession' => $profPagination]);
    }

    /**
     * @Route("/edit/{id}", name="edit", requirements={"id" = "\d+"})
     * @param int $id
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function edit(int $id, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $prof = $this->professionRepository->find($id);

        if (null === $prof) { throw new NotFoundHttpException(); } else {}

        $form = $this->createForm(ProfessionType::class, $prof);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($prof);
            $this->em->flush();

            return $this->redirectToRoute('profession_home');
        }

        return $this->render('Profession/formProfession.html.twig', [
            'userProfession' => $this->userRepository->findByProfession($id),
            'createFormular' => $form->createView(),
            'prof' => $prof
        ]);
    }

    /**
     * @Route("/insert", name="insert", requirements={"id" = "\d+"})
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function insert(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $prof = new Profession();

        $form = $this->createForm(ProfessionType::class, $prof);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($prof);
            $this->em->flush();

            return $this->redirectToRoute('profession_home');
        }

        return $this->render('Profession/formProfession.html.twig', ['createFormular' => $form->createView(),'userProfession' => [1]]);
    }
}