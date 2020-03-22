<?php

namespace App\Controller;

use App\Repository\ProjectRepository;
use App\Repository\UserProjectRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{

    private $projectRepository;
    private $userRepository;
    private $userProjectRepository;

    public function __construct(ProjectRepository $projectRepository, UserRepository $userRepository, UserProjectRepository $userProjectRepository) {
        $this->projectRepository = $projectRepository;
        $this->userRepository = $userRepository;
        $this->userProjectRepository = $userProjectRepository;
    }

    /**
     * @Route("", name="main_home")
     */
    public function home(): Response
    {
        $arrayUsers = [];
        $total =0;
        
        $listUser = $this->userRepository->findAll();
        $projects = $this->projectRepository->getLastProjects();

        foreach ($listUser as $user){
            array_push($arrayUsers,[$this->userProjectRepository->getTopEmployee($user->getId()),$user->getId()]);
        }

        $max = max($arrayUsers);
        $id = $max[1];

        return $this->render('Main/index.html.twig', [
            'listProjects' => $projects,
            'users' => $this->userRepository->findAll(),
            'topEmployee'=> $this->userRepository->findBy(array('id' => $id)),
            'topEmployeeValue' => $max,
            'lastTimes'=>$this->userProjectRepository->getLastTime(),
            'totalTime'=>$this->userProjectRepository->getTotalTime(),
            'delProjects' => $this->projectRepository->getDelProjects(),
            'notDelProjects'=>$this->projectRepository->getNotDelProjects()]);
    }
}
