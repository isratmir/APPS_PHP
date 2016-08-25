<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrap3View;

use AppBundle\Entity\Website;
use AppBundle\Form\WebsiteType;

use AppBundle\Form\WebsiteFilterType;

/**
 * Website controller.
 *
 * @Route("/host/website")
 */
class WebsiteController extends Controller
{
    /**
     * Lists all Website entities.
     *
     * @Route("/", name="website")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:Website')->createQueryBuilder('e');
        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);

        list($websites, $pagerHtml) = $this->paginator($queryBuilder, $request);
        
        return $this->render('website/index.html.twig', array(
            'websites' => $websites,
            'pagerHtml' => $pagerHtml,
            'filterForm' => $filterForm->createView(),

        ));
    }

    
    /**
    * Create filter form and process filter request.
    *
    */
    protected function filter($queryBuilder, $request)
    {
        $session = $request->getSession();
        $filterForm = $this->createForm('AppBundle\Form\WebsiteFilterType');

        // Reset filter
        if ($request->get('filter_action') == 'reset') {
            $session->remove('WebsiteControllerFilter');
        }

        // Filter action
        if ($request->get('filter_action') == 'filter') {
            // Bind values from the request
            $filterForm->submit($request->query->get($filterForm->getName()));

            if ($filterForm->isValid()) {
                // Build the query from the given form object
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filterForm, $queryBuilder);
                // Save filter to session
                $filterData = $filterForm->getData();
                $session->set('WebsiteControllerFilter', $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has('WebsiteControllerFilter')) {
                $filterData = $session->get('WebsiteControllerFilter');
                $filterForm = $this->createForm('AppBundle\Form\WebsiteFilterType', $filterData);
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filterForm, $queryBuilder);
            }
        }

        return array($filterForm, $queryBuilder);
    }

    /**
    * Get results from paginator and get paginator view.
    *
    */
    protected function paginator($queryBuilder, $request)
    {
        // Paginator
        $adapter = new DoctrineORMAdapter($queryBuilder);
        $pagerfanta = new Pagerfanta($adapter);
        $currentPage = $request->get('page', 1);
        $pagerfanta->setCurrentPage($currentPage);
        $entities = $pagerfanta->getCurrentPageResults();

        // Paginator - route generator
        $me = $this;
        $routeGenerator = function($page) use ($me)
        {
            return $me->generateUrl('website', array('page' => $page));
        };

        // Paginator - view
        $view = new TwitterBootstrap3View();
        $pagerHtml = $view->render($pagerfanta, $routeGenerator, array(
            'proximity' => 3,
            'prev_message' => 'previous',
            'next_message' => 'next',
        ));

        return array($entities, $pagerHtml);
    }
    
    

    /**
     * Displays a form to create a new Website entity.
     *
     * @Route("/new", name="website_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
    
        $website = new Website();
        $form   = $this->createForm('AppBundle\Form\WebsiteType', $website);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($website);
            $em->flush();

            return $this->redirectToRoute('website_show', array('id' => $website->getId()));
        }
        return $this->render('website/new.html.twig', array(
            'website' => $website,
            'form'   => $form->createView(),
        ));
    }
    
    

    
    /**
     * Finds and displays a Website entity.
     *
     * @Route("/{id}", name="website_show")
     * @Method("GET")
     */
    public function showAction(Website $website)
    {
        $deleteForm = $this->createDeleteForm($website);
        return $this->render('website/show.html.twig', array(
            'website' => $website,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Displays a form to edit an existing Website entity.
     *
     * @Route("/{id}/edit", name="website_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Website $website)
    {
        $deleteForm = $this->createDeleteForm($website);
        $editForm = $this->createForm('AppBundle\Form\WebsiteType', $website);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($website);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Edited Successfully!');
            return $this->redirectToRoute('website_edit', array('id' => $website->getId()));
        }
        return $this->render('website/edit.html.twig', array(
            'website' => $website,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Deletes a Website entity.
     *
     * @Route("/{id}", name="website_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Website $website)
    {
    
        $form = $this->createDeleteForm($website);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($website);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.delete.success');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'flash.delete.error');
        }
        
        return $this->redirectToRoute('website');
    }
    
    /**
     * Creates a form to delete a Website entity.
     *
     * @param Website $website The Website entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Website $website)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('website_delete', array('id' => $website->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * Delete Website by id
     *
     * @param mixed $id The entity id
     * @Route("/delete/{id}", name="website_by_id_delete")
     * @Method("GET")
     */
    public function deleteById($id){

        $em = $this->getDoctrine()->getManager();
        $website = $em->getRepository('AppBundle:Website')->find($id);
        
        if (!$website) {
            throw $this->createNotFoundException('Unable to find Website entity.');
        }
        
        try {
            $em->remove($website);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.delete.success');
        } catch (Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', 'flash.delete.error');
        }

        return $this->redirect($this->generateUrl('website'));

    }
    
    
    
    /**
    * Bulk Action
    * @Route("/bulk-action/", name="website_bulk_action")
    * @Method("POST")
    */
    public function bulkAction(Request $request)
    {
        $ids = $request->get("ids", array());
        $action = $request->get("bulk_action", "delete");

        if ($action == "delete") {
            try {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository('AppBundle:Website');

                foreach ($ids as $id) {
                    $website = $repository->find($id);
                    $em->remove($website);
                    $em->flush();
                }

                $this->get('session')->getFlashBag()->add('success', 'websites was deleted successfully!');

            } catch (Exception $ex) {
                $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the websites ');
            }
        }

        return $this->redirect($this->generateUrl('website'));
    }
    
    
}
