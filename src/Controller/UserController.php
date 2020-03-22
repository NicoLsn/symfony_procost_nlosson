<?php

namespace App\Controller;

use App\Entity\Profession;
use App\Entity\User;
use App\Entity\UserProject;
use App\Form\UserProjectType;
use App\Form\UserType;
use App\Repository\ProfessionRepository;
use App\Repository\ProjectRepository;
use App\Repository\UserProjectRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user", name="user_")
 */
class UserController extends AbstractController
{
    
    private $userRepository;
    private $professionRepository;
    private $em;
    private $encoder;
    private $projectRepository;
    private $userProjectRepository;

    public function __construct(UserRepository $userRepository, ProfessionRepository $professionRepository, UserProjectRepository $userProjectRepository, EntityManagerInterface $em,UserPasswordEncoderInterface $encoder) {
        $this->userRepository = $userRepository;
        $this->professionRepository = $professionRepository;
        $this->userProjectRepository = $userProjectRepository;
        $this->em = $em;
        $this->encoder=$encoder;
    }

    /**
     * @Route("", name="home")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function home(Request $request, PaginatorInterface $paginator )
    {
        $queryBuilder = $this->userRepository->Paginator();
        $userPagination = $paginator->paginate($queryBuilder,$request->query->getInt('page', 1),10);

        return $this->render('User/listUser.html.twig', ['listUser' => $userPagination]);
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
        $user = $this->userRepository->find($id);
        if (null === $user) { throw new NotFoundHttpException(); }
        else {
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $encoded = $this->encoder->encodePassword($user, $user->getPassword());
                $user->setPassword($encoded);

                $this->em->persist($user);
                $this->em->flush();

                return $this->redirectToRoute('user_home');
            }

            return $this->render('User/formUser.html.twig', ['createFormular' => $form->createView()]);
        }
    }

    /**
     * @Route("/info/{id}", name="info", requirements={"id" = "\d+"})
     * @param int $id
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     * @throws \Exception
     */
    public function info($id, Request $request, PaginatorInterface $paginator ): Response
    {
        $userProject = new UserProject();

        $form = $this->createForm(UserProjectType::class, $userProject);
        $form->handleRequest($request);

        $queryBuilder = $this->userProjectRepository->findByUser($id);
        $userProjectPagination = $paginator->paginate($queryBuilder,$request->query->getInt('page', 1),10);

        if ($form->isSubmitted() && $form->isValid()) {
            $userProject->setAddedAt(new \DateTime());
            $userProject->setUser($this->userRepository->find($id));

            $this->em->persist($userProject);
            $this->em->flush();
            
            return $this->redirectToRoute('user_info',[
                'id' => $id,
            ]);
        }
        
        return $this->render('User/detailUser.html.twig', [
            'user' => $this->userRepository->find($id),
            'userProjects'=> $userProjectPagination,
            'createFormular' => $form->createView(),
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
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($user);
            $this->em->flush();

            return $this->redirectToRoute('user_home');
        }

        return $this->render('User/formUser.html.twig', [
            'createFormular' => $form->createView()
        ]);
    }
}
