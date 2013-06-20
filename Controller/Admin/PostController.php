<?php

namespace Illarra\BlogBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Illarra\BlogBundle\Entity\Post;
use App\BlogBundle\Form\PostType;

/**
 * @Route("/post")
 */
class PostController extends Controller
{
    use \Illarra\CoreBundle\Traits\Controller\CoreConfiguration;
    
    /**
     * @return array
     */
    public function getTranslatableLocales()
    {
        $type = $this->container->getParameter('illarra_blog.type');
        
        return ($type['one_blog_per_locale'] && $type['predefined_locales'])
            ? $this->getAvailableLocales()
            : array($this->getDefaultLocale());
    }
    
    /**
     * @Route("/{page}", name="admin_illarra_blog_post_index", defaults={"page" = 1}, requirements={"page" = "\d+"})
     * @Method("GET")
     * @Template()
     */
    public function indexAction($page)
    {
        if ($page < 1) {
            return $this->redirect($this->generateUrl('admin_illarra_blog_post_index'));
        }
        
        $repository = $this->getDoctrine()->getRepository($this->container->getParameter('illarra_blog.post_class'));
        $repository->setEntitiesPerPage($this->getEntitiesPerPageInAdmin());
        
        if ($page > $repository->getPages()) {
            return $this->redirect($this->generateUrl('admin_illarra_blog_post_index',
                array('page' => $repository->getPages()))
            );
        }
        
        return array(
            'page'              => $page,
            'pages'             => $repository->getPages(),
            'entities'          => $repository->findAllOrdered($page),
            'entities_per_page' => $repository->getEntitiesPerPage(),
            'entities_count'    => $repository->getEntitiesCount(),
            'disqus_shortname'  => $this->container->getParameter('illarra_blog.comments.disqus_shortname'),
        );
    }
    
    /**
     * @Route("/{id}/show", name="admin_illarra_blog_post_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository($this->container->getParameter('illarra_blog.post_class'))->find($id);
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Post entity.');
        }
        
        $deleteForm = $this->createDeleteForm($id);
        
        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }
    
    /**
     * @Route("/new", name="admin_illarra_blog_post_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $class  = $this->container->getParameter('illarra_blog.post_class');
        $entity = new $class;
        
        foreach ($this->getTranslatableLocales() as $locale) {
            $entity->translate($locale);
        }
        $entity->mergeNewTranslations();
        
        $form = $this->createForm(new PostType($this->container), $entity);
        
        return array(
            'entity'    => $entity,
            'form'      => $form->createView(),
            'blog_type' => $this->container->getParameter('illarra_blog.type'),
        );
    }
    
    /**
     * @Route("/create", name="admin_illarra_blog_post_create")
     * @Method("POST")
     * @Template("IllarraBlogBundle:Admin/Post:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $class  = $this->container->getParameter('illarra_blog.post_class');
        $entity = new $class;
        
        $form = $this->createForm(new PostType($this->container), $entity);
        $form->bind($request);
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            
            return $this->redirect($this->generateUrl('admin_illarra_blog_post_show', array('id' => $entity->getId())));
        }
        
        return array(
            'entity'    => $entity,
            'form'      => $form->createView(),
            'blog_type' => $this->container->getParameter('illarra_blog.type'),
        );
    }
    
    /**
     * @Route("/{id}/edit", name="admin_illarra_blog_post_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository($this->container->getParameter('illarra_blog.post_class'))->find($id);
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Post entity.');
        }
        
        $editForm = $this->createForm(new PostType($this->container), $entity);
        $deleteForm = $this->createDeleteForm($id);
        
        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'blog_type'   => $this->container->getParameter('illarra_blog.type'),
        );
    }
    
    /**
     * @Route("/{id}/update", name="admin_illarra_blog_post_update")
     * @Method("PUT")
     * @Template("IllarraBlogBundle:Admin/Post:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository($this->container->getParameter('illarra_blog.post_class'))->find($id);
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Post entity.');
        }
        
        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new PostType($this->container), $entity);
        $editForm->bind($request);
        
        if ($editForm->isValid()) {
            $entity->setUpdatedAt(new \DateTime('now'));
            $em->persist($entity);
            $em->flush();
            
            return $this->redirect($this->generateUrl('admin_illarra_blog_post_show', array('id' => $id)));
        }
        
        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'blog_type'   => $this->container->getParameter('illarra_blog.type'),
        );
    }
    
    /**
     * @Route("/{id}/delete", name="admin_illarra_blog_post_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository($this->container->getParameter('illarra_blog.post_class'))->find($id);
            
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Post entity.');
            }
            
            $em->remove($entity);
            $em->flush();
        }
        
        return $this->redirect($this->generateUrl('admin_illarra_blog_post_index'));
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
