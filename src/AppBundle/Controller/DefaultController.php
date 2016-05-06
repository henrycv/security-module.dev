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

    /**
     * @Route("/testmail", name="testmail")
     */
    public function testmailAction(Request $request)
    {
phpinfo();die;
        $arrayTo = array();
        for ($i=1; $i <= 2000; $i++) {
            // $arrayTo[] = 'user.test.job.one+' . $i . '@gmail.com';
        }
        $arrayTo[] = 'user.test.job.one@gmail.com';
        $arrayTo[] = 'booshoe@mailinator.com';
        $arrayTo[] = 'zippitysir@mailinator.com ';

        $transport = \Swift_MailTransport::newInstance();
        $mailer = \Swift_Mailer::newInstance($transport);

        foreach ($arrayTo as $i => $mail) {
            // Create a message
            $message = \Swift_Message::newInstance('Test mail ' . date('h:i:s'))
                ->setFrom(array(
                    'john@doe.com' => 'John Doe'
                ))
                ->setTo(
                    $mail
                )
                ->setCharset('UTF-8')
                ->setContentType("text/html")
                ->setBody(
                    '<h1>
                        Test body: Iñtërnâtiônàlizætiøn
                    </h1><br />
                    Test mail ' . date('h:i:s')
                )
            ;

        // Send the message
        $result = $mailer->send($message);
var_dump(array(
    'testmailAction',
    // $mailer,
    $mail,
    $result,
));
        }
// die;
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            'pageTitle' => 'testmail'
        ));
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
