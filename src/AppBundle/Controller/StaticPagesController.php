<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class StaticPagesController extends Controller {

    /**
     * @Route("/", name="home_page")
     */
    public function homeAction (Request $request) {
        // replace this example code with whatever you need
        return $this->render('static_pages/home.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/help", name="help_page")
     */
    public function helpAction (Request $request) {
        // replace this example code with whatever you need
        return $this->render('static_pages/help.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/about", name="about_page")
     */
    public function aboutAction (Request $request) {
        // replace this example code with whatever you need
        return $this->render('static_pages/about.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/contact", name="contact_page")
     */
    public function contactAction (Request $request) {
        // replace this example code with whatever you need
        return $this->render('static_pages/contact.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ]);
    }

}