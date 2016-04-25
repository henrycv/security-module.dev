<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Page;
use AppBundle\Entity\PageAction;
use AppBundle\Form\PageType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Page controller.
 *
 * @Route("/page")
 */
class PageController extends Controller
{
    /**
     * Lists all Page entities.
     *
     * @Route("/", name="page_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        if (!$this->hasPermission($request)) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        $pages = $em->getRepository('AppBundle:Page')->findAll();

        return $this->render('page/index.html.twig', array(
            'pages' => $pages,
        ));
    }

    /**
     * Creates a new Page entity.
     *
     * @Route("/new", name="page_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        if (!$this->hasPermission($request)) {
            throw new AccessDeniedException();
        }

        $page = new Page();
        $form = $this->createForm('AppBundle\Form\PageType', $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (isset($request->request->get('page')['visible'])
                && $request->request->get('page')['visible'] == 1
            ) {
                $page->setVisible(1);
            } else {
                $page->setVisible(0);
            }
            $page->setPageParentId($request->request->get('page')['pageParentId'] * 1);
            $em = $this->getDoctrine()->getManager();
            $em->persist($page);
            $em->flush();

            // Save the Page Actions
            $this->savePageActions($request->request->get('page'), $page);

            return $this->redirectToRoute('page_show', array('id' => $page->getIdPage()));
        }

        return $this->render('page/new.html.twig', array(
            'page' => $page,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Page entity.
     *
     * @Route("/{id}", name="page_show")
     * @Method("GET")
     */
    public function showAction(Request $request, Page $page)
    {
        if (!$this->hasPermission($request)) {
            throw new AccessDeniedException();
        }

        $deleteForm = $this->createDeleteForm($page);

        return $this->render('page/show.html.twig', array(
            'page' => $page,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Page entity.
     *
     * @Route("/{id}/edit", name="page_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Page $page)
    {
        if (!$this->hasPermission($request)) {
            throw new AccessDeniedException();
        }

        $deleteForm = $this->createDeleteForm($page);
        if ($request->isMethod('POST')) {
            $requestArgs = $request->request->get('page');
            if (!isset($requestArgs['actions'])) {
                $request->request->set('page', array_merge(
                    $requestArgs,
                    array('actions' => [])
                ));
            }
            $page->setActions($request->request->get('page')['actions']);
            $page->setPageParentId($request->request->get('page')['pageParentId'] * 1);
        }
        $editForm = $this->createForm('AppBundle\Form\PageType', $page);
        $editForm->handleRequest($request);

        $actionsToPage = $this->getDoctrine()
            ->getRepository('AppBundle:PageAction')
            ->findByPageId($page->getIdPage());

        // Collection of Actions associated to this Page
        $actionsToPageData = [];
        foreach ($actionsToPage as $action) {
            $actionsToPageData[] = [
                'actionId' => $action->getActionId()
            ];
        }

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $page->setActions($request->request->get('page')['actions']);
            $page->setPageParentId($request->request->get('page')['pageParentId'] * 1);

            $em = $this->getDoctrine()->getManager();
            $em->persist($page);
            $em->flush();

            // Save the Page Actions
            $this->savePageActions($request->request->get('page'), $page);

            // return $this->redirectToRoute('page_edit', array('id' => $page->getIdPage()));
            return $this->redirectToRoute('page_show', array('id' => $page->getIdPage()));
        }

        return $this->render('page/edit.html.twig', array(
            'page' => $page,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'actionsToPageData' => json_encode($actionsToPageData),
        ));
    }

    /**
     * Deletes a Page entity.
     *
     * @Route("/{id}", name="page_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Page $page)
    {
        $form = $this->createDeleteForm($page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($page);
            $em->flush();

            $pageActions = $this->getDoctrine()
                ->getRepository('AppBundle:PageAction')
                ->findByPageId($page->getIdPage());

            $em = $this->getDoctrine()->getManager();

            if ($pageActions) {
                foreach ($pageActions as $pageAction) {
                    $em->remove($pageAction);
                }
                $em->flush();
            }

        }

        return $this->redirectToRoute('page_index');
    }

    /**
     * Creates a form to delete a Page entity.
     *
     * @param Page $page The Page entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Page $page)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('page_delete', array('id' => $page->getIdPage())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    private function savePageActions($pageData, $page)
    {
        $pageActions = $this->getDoctrine()
            ->getRepository('AppBundle:PageAction')
            ->findByPageId($page->getIdPage());

        $em = $this->getDoctrine()->getManager();

        if ($pageActions) {
            foreach ($pageActions as $pageAction) {
                $em->remove($pageAction);
            }
            $em->flush();
        }

        foreach ($pageData['actions'] as $action) {
            $pageAction = new PageAction();
            $pageAction->setPageId($page->getIdPage());
            $pageAction->setActionId($action);
            $em->persist($pageAction);
        }
        $em->flush();
    }

    private function hasPermission(Request $request)
    {
        $pages = $this->getDoctrine()
            ->getRepository('AppBundle:Page')
            ->findAllByUser(
                $this->get('security.token_storage')->getToken()->getUser(),
                $this->container->get('router'),
                $request
            );

        if ($pages) {
            $routeName = $request->get('_route');

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
