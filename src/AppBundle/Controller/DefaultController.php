<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $routeAndPermission = $this->hasPermission($request);

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            'pageTitle' => $routeAndPermission['name']
        ));
    }
    /**
     * @Route("/section/{slug}", name="section", defaults={"slug"="home"})
     */
    public function pageAction(Request $request, $slug)
    {
        $routeAndPermission = $this->hasPermission($request);

        if (!$routeAndPermission) {
            throw new AccessDeniedException();
        }

        return $this->render('default/index.html.twig', array(
            'pageTitle' => $routeAndPermission['name']
        ));
    }

    /**
     * @Route("/menu", name="menu")
     */
    public function menuAction(Request $request)
    {
        $pages = $this->getDoctrine()
            ->getRepository('AppBundle:Page')
            ->findAllByUser(
                $this->get('security.token_storage')->getToken()->getUser(),
                $this->container->get('router'),
                $request
            );
        $result = [];
        foreach ($pages as $page) {
            if ($page['visible']) {
                $result[] = $page;
            }
        }
        return new Response(json_encode($result));
    }

    private function hasPermission(Request $request)
    {
        $hasPermission = false;
        $pages = $this->getDoctrine()
            ->getRepository('AppBundle:Page')
            ->findAllByUser(
                $this->get('security.token_storage')->getToken()->getUser(),
                $this->container->get('router'),
                $request
            );

        if ($pages) {
            $routeName = $request->get('_route');
            $hasPermission = false;
            foreach ($pages as $page) {
                if (stripos($page['routeName'], $routeName) !== false
                    || ($page['url']
                        && strripos($page['url'], $request->getPathInfo()) !== false)
                ) {
                    return $page;
                }
            }
        }

        return false;
    }
}
