<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\User;
use AppBundle\Entity\UserRole;
use AppBundle\Form\UserType;
use AppBundle\Repository\UserRoleRepository;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * User controller.
 *
 * @Route("/user")
 */
class UserController extends Controller
{
    /**
     * Flag to maintain user's password stored in the DB
     */
    const FLAG_OLD_PASS = '-do-not-update-pass-on-edit-action-';

    /**
     * Lists all User entities.
     *
     * @Route("/", name="user_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $routeAndPermission = $this->hasPermission($request);

        if (!$routeAndPermission) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('AppBundle:User')->findAll();

        return $this->render('user/index.html.twig', array(
            'users' => $users,
            'pageTitle' => $routeAndPermission['name']
        ));
    }

    /**
     * Creates a new User entity.
     *
     * @Route("/new", name="user_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $routeAndPermission = $this->hasPermission($request);

        if (!$routeAndPermission) {
            throw new AccessDeniedException();
        }

        $user = new User();
        $form = $this->createForm('AppBundle\Form\UserType', $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user->setEnabled(true);
            $em->persist($user);
            $em->flush();

            // Save the User Roles
            $this->saveUserRoles($request->request->get('user'), $user);

            return $this->redirectToRoute('user_show', array('id' => $user->getId()));
        }

        return $this->render('user/new.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
            'pageTitle' => $routeAndPermission['name']
        ));
    }

    /**
     * Finds and displays a User entity.
     *
     * @Route("/{id}", name="user_show")
     * @Method("GET")
     */
    public function showAction(Request $request, User $user)
    {
        $routeAndPermission = $this->hasPermission($request);

        if (!$routeAndPermission) {
            throw new AccessDeniedException();
        }

        $deleteForm = $this->createDeleteForm($user);

        return $this->render('user/show.html.twig', array(
            'user' => $user,
            'delete_form' => $deleteForm->createView(),
            'pageTitle' => $routeAndPermission['name'],
            'roles' => $user->getRoles()
        ));
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/{id}/edit", name="user_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, User $user)
    {
        $routeAndPermission = $this->hasPermission($request);

        if (!$routeAndPermission) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($user);
        $editForm = $this->createForm('AppBundle\Form\UserType', $user);
        $userRolesPreExistents = $this->getDoctrine()
            ->getRepository('AppBundle:UserRole')
            ->findByUserId($user->getId());

        $userRolesData = [];
        foreach ($userRolesPreExistents as $userRole) {
            $userRolesData[] = [
                'roleId' => $userRole->getRoleId()
            ];
        }
        $dataSubmitted = $request->request->get('user');

        // When user dosn't provide a password, then itsn't updated on the DB
        $maintainOldPass = false;
        if ($request->isMethod('POST') && !$dataSubmitted['plainPassword']['first']
            && !$dataSubmitted['plainPassword']['second']) {
            $dataSubmitted['plainPassword'] = array(
                'first' => self::FLAG_OLD_PASS,
                'second' => self::FLAG_OLD_PASS,
            );
            $request->request->set('user', $dataSubmitted);
            $maintainOldPass = true;
        }

        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if ($maintainOldPass === false) {
                $password = $this->get('security.password_encoder')
                    ->encodePassword($user, $user->getPlainPassword());
                $user->setPassword($password);
            }
            $em->persist($user);
            $em->flush();

            // Save the User Roles
            $this->saveUserRoles($dataSubmitted, $user);

            // return $this->redirectToRoute('user_edit', array('id' => $user->getId()));
            return $this->redirectToRoute('user_index');
        }

        $roles = $em->getRepository('AppBundle:Role')->findAll();

        return $this->render('user/edit.html.twig', array(
            'user' => $user,
            'roles' => $roles,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'userRolesData' => json_encode($userRolesData),
            'pageTitle' => $routeAndPermission['name']
        ));
    }

    /**
     * Deletes a User entity.
     *
     * @Route("/{id}", name="user_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, User $user)
    {
        $routeAndPermission = $this->hasPermission($request);

        if (!$routeAndPermission) {
            throw new AccessDeniedException();
        }

        $form = $this->createDeleteForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();

            $userRoles = $this->getDoctrine()
                ->getRepository('AppBundle:UserRole')
                ->findByUserId($user->getId());

            $em = $this->getDoctrine()->getManager();

            if ($userRoles) {
                foreach ($userRoles as $userRole) {
                    $em->remove($userRole);
                }
                $em->flush();
            }
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * Creates a form to delete a User entity.
     *
     * @param User $user The User entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(User $user)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('user_delete', array('id' => $user->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    private function hasPermission(Request $request, $alias = null)
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
            foreach ($pages as $value) {
                if ($value['routeName'] === $routeName
                    || stripos($value['url'], $alias) !== false) {
                    return $value;
                }
            }
        }

        return false;
    }

    private function saveUserRoles($userData, $user)
    {
        $userRoles = $this->getDoctrine()
            ->getRepository('AppBundle:UserRole')
            ->findByUserId($user->getId());

        $em = $this->getDoctrine()->getManager();

        if ($userRoles) {
            foreach ($userRoles as $userRole) {
                $em->remove($userRole);
            }
            $em->flush();
        }

        foreach ($userData['roles'] as $role) {
            $userRole = new UserRole();
            $userRole->setUserId($user->getId());
            $userRole->setRoleId($role);
            $em->persist($userRole);
        }
        $em->flush();
    }

    private function getCrudPermissions()
    {

    }
}
