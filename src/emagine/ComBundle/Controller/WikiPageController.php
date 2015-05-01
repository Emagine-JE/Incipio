<?php

namespace emagine\ComBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;

use emagine\ComBundle\Entity\WikiPage;
use emagine\ComBundle\Form\WikiPageType;

use \Michelf\MarkdownExtra;

/**
 * WikiPage controller.
 *
 */
class WikiPageController extends Controller
{

    /**
     * Lists all WikiPage entities.
     *
     * @Secure(roles="IS_AUTHENTICATED_ANONYMOUSLY")
     * 
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('emagineComBundle:WikiPage')->findAll();

        return $this->render('emagineComBundle:WikiPage:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new WikiPage entity.
     * 
     * @Secure(roles="ROLE_CA")     * 
     */
    public function createAction(Request $request)
    {
        $entity = new WikiPage();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('wikipage_show', array('slug' => $entity->getSlug())));
        }

        return $this->render('emagineComBundle:WikiPage:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a WikiPage entity.
     *
     * @param WikiPage $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(WikiPage $entity)
    {
        $form = $this->createForm(new WikiPageType(), $entity, array(
            'action' => $this->generateUrl('wikipage_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new WikiPage entity.
     *
     * @Secure(roles="ROLE_CA")
     *
     */
    public function newAction()
    {
        $entity = new WikiPage();
        $form   = $this->createCreateForm($entity);

        return $this->render('emagineComBundle:WikiPage:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a WikiPage entity.
     *
     * @Secure(roles="IS_AUTHENTICATED_ANONYMOUSLY")
     */
    public function showAction($slug)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('emagineComBundle:WikiPage')->findOneBySlug($slug);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find WikiPage entity.');
        }

        $deleteForm = $this->createDeleteForm($entity->getId());

        return $this->render('emagineComBundle:WikiPage:show.html.twig', array(
            'entity'      => $entity,
            'contenu'     => MarkdownExtra::defaultTransform($entity->getContenu()),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing WikiPage entity.
     *
     * @Secure(roles="ROLE_CA")
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('emagineComBundle:WikiPage')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find WikiPage entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('emagineComBundle:WikiPage:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a WikiPage entity.
    *
    * @param WikiPage $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(WikiPage $entity)
    {
        $form = $this->createForm(new WikiPageType(), $entity, array(
            'action' => $this->generateUrl('wikipage_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing WikiPage entity.
     *
     * @Secure(roles="ROLE_CA")
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('emagineComBundle:WikiPage')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find WikiPage entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('wikipage_show', array('slug' => $entity->getSlug())));
        }

        return $this->render('emagineComBundle:WikiPage:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a WikiPage entity.
     *
     * @Secure(roles="ROLE_CA")
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('emagineComBundle:WikiPage')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find WikiPage entity.');
            }

            if($entity->getProtected()){
                $this->addFlash(
                    'danger',
                    'Vous ne pouvez pas supprimer une page protégée'
                );
            }else{
                $em->remove($entity);
                $em->flush();
            }


        }

        return $this->redirect($this->generateUrl('wikipage'));
    }

    /**
     * Creates a form to delete a WikiPage entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('wikipage_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    public function markdownAction(Request $request){

        $response = MarkdownExtra::defaultTransform($request->request->get('data'));

        return new Response($response);
    }
}
