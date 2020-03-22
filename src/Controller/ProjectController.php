<?php

namespace App\Controller;

use App\Entity\Profession;
use App\Entity\Project;
use App\Entity\UserProject;
use App\Form\ProfessionType;
use App\Form\ProjectType;
use App\Form\UserProjectType;
use App\Repository\ProjectRepository;
use App\Repository\UserProjectRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/project", name="project_")
 */
class ProjectController extends AbstractController
{

    private $projectRepository;
    private $userProjectRepository;
    private $userRepository;

    private $em;

    public function __construct(ProjectRepository $projectRepository, EntityManagerInterface $em, UserProjectRepository $userProjectRepository, UserRepository $userRepository)
    {
        $this->em = $em;
        $this->userRepository = $userRepository;
        $this->projectRepository = $projectRepository;
        $this->userProjectRepository = $userProjectRepository;
    }

    /**
     * @Route("", name="home")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function home(Request $request, PaginatorInterface $paginator )
    {
        $queryBuilder = $this->projectRepository->Paginator();

        $projectPagination = $paginator->paginate($queryBuilder,$request->query->getInt('page', 1),10);

        return $this->render('Project/listProject.html.twig', ['listProject' => $projectPagination]);
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

        $project = new Project();

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $project->setCreationDate(new \DateTime());

            $this->em->persist($project);
            $this->em->flush();

            return $this->redirectToRoute('project_home');
        }

        return $this->render('Project/formProject.html.twig', ['createFormular' => $form->createView()]);
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

        $project = $this->projectRepository->find($id);

        if (null === $project) {
            throw new NotFoundHttpException();
        } else {}

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->em->persist($project);
            $this->em->flush();

            return $this->redirectToRoute('project_home');
        }

        return $this->render('Project/formProject.html.twig', ['createFormular' => $form->createView()]);
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
        $users = $this->userRepository->findAll();
        $total =0;

        foreach ($users as $user){
            $cost = $this->userProjectRepository->getTotalCost($user->getId(), $id);
            $total += $cost['totalCost'];
        }

        $userProject = new UserProject();

        $form = $this->createForm(UserProjectType::class, $userProject);
        $form->handleRequest($request);

        $queryBuilder = $this->userProjectRepository->findByProject($id);

        $userProjectPagination = $paginator->paginate($queryBuilder,$request->query->getInt('page', 1),10);

        return $this->render('Project/detailProject.html.twig', [
            'projet' => $this->projectRepository->find($id),
            'uProjets'=> $userProjectPagination,
            'nbUsers' => $this->userProjectRepository->getDetailsProject($id),
            'totalCost'=>$total]);
    }

    /**
     * @Route("/delivery/{id}", name="delivery", requirements={"id" = "\d+"})
     * @param int $id
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     * @throws \Exception
     */
    public function delivery($id, Request $request, PaginatorInterface $paginator): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $project = $this->projectRepository->find($id);

        if($project->getDeliveryDate()==null){

            $project->setDeliveryDate(new \DateTime());
            
            $this->em->persist($project);
            $this->em->flush();
        }

        $queryBuilder = $this->projectRepository->Paginator();

        $projectPagination = $paginator->paginate($queryBuilder,$request->query->getInt('page', 1),10);

        return $this->render('Project/listProject.html.twig', ['listProject' => $projectPagination,]);
    }
}
