<?php

namespace Illarra\BlogBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Illarra\BlogBundle\Entity\Label;
use Illarra\BlogBundle\Form\LabelType;

/**
 * @Route("/label")
 */
class LabelController extends Controller
{
    use \Illarra\CoreBundle\Traits\Controller\CoreConfiguration;
    
    /**
     * @Route("/{page}", name="admin_illarra_blog_label_index", defaults={"page" = 1}, requirements={"page" = "\d+"})
     * @Method("GET")
     * @Template()
     */
    public function indexAction($page)
    {
        if ($page < 1) {
            return $this->redirect($this->generateUrl('admin_illarra_blog_label_index'));
        }
        
        $repository = $this->getDoctrine()->getRepository($this->container->getParameter('illarra_blog.label_class'));
        $repository->setEntitiesPerPage($this->getEntitiesPerPageInAdmin());
        
        if ($page > $repository->getPages()) {
            return $this->redirect($this->generateUrl('admin_illarra_blog_label_index',
                array('page' => $repository->getPages()))
            );
        }
        
        return array(
            'page'              => $page,
            'pages'             => $repository->getPages(),
            'entities'          => $repository->findAllOrdered($page),
            'entities_per_page' => $repository->getEntitiesPerPage(),
            'entities_count'    => $repository->getEntitiesCount(),
        );
    }
    
    /**
     * @Route("/create", name="admin_illarra_blog_label_create")
     * @Method("POST")
     * @Template("AppBlogBundle:Label:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $class  = $this->container->getParameter('illarra_blog.label_class');
        $entity = new $class;
        
        $form = $this->createForm(new LabelType($this->container), $entity);
        $form->bind($request);
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            
            return $this->redirect($this->generateUrl('admin_illarra_blog_label_index'));
        }
        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
    
    /**
     * @Route("/new", name="admin_illarra_blog_label_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $class  = $this->container->getParameter('illarra_blog.label_class');
        $entity = new $class;
        
        foreach ($this->getAvailableLocales() as $locale) {
            $entity->translate($locale);
        }
        $entity->mergeNewTranslations();
        
        $form = $this->createForm(new LabelType($this->container), $entity);
        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
    
    /**
     * @Route("/{id}/edit", name="admin_illarra_blog_label_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository($this->container->getParameter('illarra_blog.label_class'))->find($id);
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Label entity.');
        }
        
        $editForm = $this->createForm(new LabelType($this->container), $entity);
        $deleteForm = $this->createDeleteForm($id);
        
        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    
    /**
     * @Route("/{id}/update", name="admin_illarra_blog_label_update")
     * @Method("PUT")
     * @Template("AppBlogBundle:Label:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository($this->container->getParameter('illarra_blog.label_class'))->find($id);
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Label entity.');
        }
        
        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new LabelType($this->container), $entity);
        $editForm->bind($request);
        
        if ($editForm->isValid()) {
            $entity->setUpdatedAt(new \DateTime('now'));
            $em->persist($entity);
            $em->flush();
            
            return $this->redirect($this->generateUrl('admin_illarra_blog_label_edit', array('id' => $id)));
        }
        
        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    
    /**
     * @Route("/{id}/delete", name="admin_illarra_blog_label_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository($this->container->getParameter('illarra_blog.label_class'))->find($id);
            
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Label entity.');
            }
            
            $em->remove($entity);
            $em->flush();
        }
        
        return $this->redirect($this->generateUrl('admin_illarra_blog_label_index'));
    }
    
    /**
     * @param  mixed $id
     * @return Symfony\Component\Form\Form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
